<?php

namespace App\PaymentModule\Services;

use App\AccountModule\Models\Account;
use App\Enums\RepeatUnit;
use App\PaymentModule\DTO\CreatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentGeneralData;
use App\PaymentModule\Jobs\UpdatePaymentCurrencyAmountJob;
use App\PaymentModule\Jobs\UpdateReducingPaymentJob;
use App\PaymentModule\Models\Payment;
use App\PaymentModule\Repositories\PaymentRepo;
use App\Services\CurrencyConverter;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Str;

class PaymentService
{
    /**
     * PaymentService constructor.
     */
    public function __construct(
        protected readonly PaymentRepo $paymentRepo,
        protected readonly CurrencyConverter $currencyConverter
    )
    {
    }

    /**
     * Get all Payments
     */
    public function getAllPayments(array $with = [], array $columns = ['*']): Collection
    {
        return $this->paymentRepo->getAll($with, $columns, 'date', 'asc');
    }

    /**
     * Get all Payments filtered by column
     */
    public function getPaymentsWhere(
        string $column,
        string $operator,
        int|string|float|bool|null $value,
        array $with = [],
        array $columns = ['*']
    ): Collection
    {
        return $this->paymentRepo->getWhere($column, $operator, $value, $with, $columns, 'date', 'asc');
    }

    /**
     * Get all Payments paginated
     */
    public function getAllPaymentsPaginated(
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*']
    ): LengthAwarePaginator
    {
        return $this->paymentRepo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get Payment by id
     */
    public function getPayment(int $paymentId, array $with = [], array $columns = ['*']): Payment
    {
        return $this->paymentRepo->firstOrFail($paymentId, $with, $columns);
    }

    /**
     * Create new Payment
     *
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function createPayment(CreatePaymentData $data): Payment
    {
        $accountTo = Account::find($data->account_to_id);
        $accountFrom = Account::find($data->account_from_id);

        return $this->paymentRepo->create([
            ...$data->toArray(),
            'group' => $data->group ?? Str::orderedUuid(),
            'amount' => $data->amount,
            'amount_to_converted' => $accountTo ? $this->currencyConverter->convert(
                $data->amount,
                $accountTo->currency,
                $data->currency,
            ) : null,
            'amount_from_converted' => $accountFrom ? -$this->currencyConverter->convert(
                -$data->amount,
                $accountFrom->currency,
                $data->currency,
            ) : null,
        ])->refresh();
    }

    /**
     * Update Payment by id
     *
     *
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function updatePayment(Payment|int $payment, UpdatePaymentData $data): bool
    {
        $paymentId = $payment instanceof Payment ? $payment->id : $payment;

        $accountTo = Account::find($data->account_to_id);
        $accountFrom = Account::find($data->account_from_id);

        return $this->paymentRepo->update($paymentId, [
            ...$data->toArray(),
            'amount' => $data->amount,
            'amount_to_converted' => $accountTo ? $this->currencyConverter->convert(
                $data->amount,
                $accountTo->currency,
                $data->currency,
            ) : null,
            'amount_from_converted' => $accountFrom ? -$this->currencyConverter->convert(
                -$data->amount,
                $accountFrom->currency,
                $data->currency,
            ) : null,
        ]);
    }

    protected function splitPayment(Payment|int $payment, Carbon $fromDate): array
    {
        $payment = $payment instanceof Payment ? $payment : Payment::find($payment);

        // Cut off payments before old date
        $this->paymentRepo->update($payment->id, [
            'repeat_ends_on' => $fromDate->clone()->subDay(),
        ]);

        // Create new payment which continues the sub-chain
        $newPayment = Payment::create([
            ...$payment->toArray(),
            'date' => $fromDate,
        ]);

        return [
            $payment->refresh(),
            $newPayment->refresh(),
        ];
    }

    public function cutoffPayment(Payment|int $payment, Carbon $date): void
    {
        $payment = $payment instanceof Payment ? $payment : Payment::find($payment);

        if ($payment->repeat_unit === RepeatUnit::NONE) {
            $this->deletePayment($payment->id);
        } else {
            [$payment, $newPayment] = $this->splitPayment($payment, $date);

            // If this is the same day as the payment date then delete the payment
            if ($payment->repeat_ends_on && $payment->repeat_ends_on->isBefore($payment->date)) {
                $this->deletePayment($payment->id);
            }

            [$paymentToDelete, $restOfChain] = $this->splitPayment(
                $newPayment,
                $date->clone()->add($newPayment->repeat_interval, $newPayment->repeat_unit->value)
            );

            if ($restOfChain->repeat_ends_on && $restOfChain->repeat_ends_on->isBefore($restOfChain->date)) {
                $this->deletePayment($restOfChain->id);
            }

            $this->deletePayment($paymentToDelete->id);
        }
    }

    public function updatePaymentGeneral(Payment|int $payment, Carbon $fromDate, UpdatePaymentGeneralData $data): bool
    {
        $payment = $payment instanceof Payment ? $payment : Payment::find($payment);

        if ($payment->repeat_unit === RepeatUnit::NONE) {
            return $this->updatePayment($payment, new UpdatePaymentData([
                ...$payment->toArray(),
                ...$data->toArray(),
                'date' => $payment->date,
                'repeat_unit' => $payment->repeat_unit,
                'repeat_ends_on' => $payment->repeat_ends_on,
            ]));
        } else {
            $accountFrom = Account::find($data->account_from_id);
            $accountTo = Account::find($data->account_to_id);

            // Cut off payments before date
            $this->splitPayment($payment, $fromDate);

            // Update current and all future payments by incrementing days diff between old date and new date
            Payment::where('group', $payment->group)
                ->where('date', '>=', $fromDate)
                ->update([
                    ...$data->toArray(),
                    'amount' => $data->amount,
                    'amount_to_converted' => $accountTo ? $this->currencyConverter->convert(
                        $data->amount,
                        $accountTo->currency,
                        $data->currency,
                    ) : null,
                    'amount_from_converted' => $accountFrom ? -$this->currencyConverter->convert(
                        -$data->amount,
                        $accountFrom->currency,
                        $data->currency,
                    ) : null,
                ]);

            return true;
        }
    }

    /**
     * Delete Payment by id
     */
    public function deletePayment(Payment|int $payment): bool
    {
        $paymentId = $payment instanceof Payment ? $payment->id : $payment;

        return $this->paymentRepo->delete($paymentId);
    }

    /**
     * Find all currency payment and update their amount
     *
     *
     * @throws Exception
     */
    public function updateCurrencyAmounts(): void
    {
        Payment::join('accounts', 'payments.account_to_id', '=', 'accounts.id')
            ->where('payments.currency', '!=', 'accounts.currency')
            ->select('payments.*')
            ->chunk(1000, function (Collection $payments) {
                $payments->each(function (Payment $payment) {
                    dispatch(new UpdatePaymentCurrencyAmountJob($payment));
                }
                );
            });

        Payment::join('accounts', 'payments.account_from_id', '=', 'accounts.id')
            ->where('payments.currency', '!=', 'accounts.currency')
            ->select('payments.*')
            ->chunk(1000, function (Collection $payments) {
                $payments->each(function (Payment $payment) {
                    dispatch(new UpdatePaymentCurrencyAmountJob($payment));
                }
                );
            });
    }

    /**
     * Update payment amount on a fresh currency rate
     *
     *
     * @throws Exception
     * @throws GuzzleException
     */
    public function updateCurrencyAmount(Payment|int $payment): void
    {
        $payment = $payment instanceof Payment ? $payment : Payment::find($payment);

        $payment->load(['account_to', 'account_from']);

        if ($payment->account_to_id && $payment->account_to->currency !== $payment->currency) {
            $this->updatePayment(
                $payment,
                new UpdatePaymentData([
                    ...$payment->toArray(),
                    'currency' => $payment->currency,
                    'date' => $payment->date,
                    'ends_on' => $payment->ends_on,
                    'repeat_unit' => $payment->repeat_unit,
                    'repeat_ends_on' => $payment->repeat_ends_on,
                ])
            );
        }

        if ($payment->account_from_id && $payment->account_from->currency !== $payment->currency) {
            $this->updatePayment(
                $payment,
                new UpdatePaymentData([
                    ...$payment->toArray(),
                    'currency' => $payment->currency,
                    'date' => $payment->date,
                    'ends_on' => $payment->ends_on,
                    'repeat_unit' => $payment->repeat_unit,
                    'repeat_ends_on' => $payment->repeat_ends_on,
                ])
            );
        }
    }

    public function updateReducingPayments(): void
    {
        Payment::whereNotNull('ends_on')
            ->chunk(1000, function (Collection $payments) {
                $payments->each(function (Payment $payment) {
                    dispatch(new UpdateReducingPaymentJob($payment));
                }
                );
            });
    }

    /**
     * @throws UnknownProperties
     */
    public function updateReducingPayment(Payment|int $payment): void
    {
        $payment = $payment instanceof Payment ? $payment->refresh() : Payment::find($payment);

        $totalDays = $payment->date->diffInDays($payment->ends_on);
        $daysLeft = $payment->ends_on->diffInDays(today());

        $amount = round($payment->amount / $totalDays * $daysLeft, 4);

        $this->updatePayment(
            $payment,
            new UpdatePaymentData([
                ...$payment->toArray(),
                'currency' => $payment->currency,
                'ends_on' => $payment->ends_on,
                'amount' => $amount,
                'date' => today(),
                'repeat_unit' => $payment->repeat_unit,
            ])
        );
    }

    public function deleteGroup(string $group): void
    {
        Payment::where('group', $group)->delete();
    }
}
