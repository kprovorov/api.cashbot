<?php

namespace App\PaymentModule\Services;

use App\Models\Jar;
use App\PaymentModule\DTO\CreatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentData;
use App\PaymentModule\Jobs\UpdatePaymentCurrencyAmountJob;
use App\PaymentModule\Jobs\UpdateReducingPaymentJob;
use App\PaymentModule\Models\Payment;
use App\PaymentModule\Repositories\PaymentRepo;
use App\Services\CurrencyConverter;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PaymentService
{
    /**
     * PaymentService constructor.
     *
     * @param  PaymentRepo  $paymentRepo
     * @param  CurrencyConverter  $currencyConverter
     */
    public function __construct(
        protected readonly PaymentRepo $paymentRepo,
        protected readonly CurrencyConverter $currencyConverter
    ) {
    }

    /**
     * Get all Payments
     *
     * @param  array  $with
     * @param  array  $columns
     * @return Collection
     */
    public function getAllPayments(array $with = [], array $columns = ['*']): Collection
    {
        return $this->paymentRepo->getAll($with, $columns);
    }

    /**
     * Get all Payments paginated
     *
     * @param  int|null  $perPage
     * @param  int|null  $page
     * @param  array  $with
     * @param  array  $columns
     * @return LengthAwarePaginator
     */
    public function getAllPaymentsPaginated(
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*']
    ): LengthAwarePaginator {
        return $this->paymentRepo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get Payment by id
     *
     * @param  int  $paymentId
     * @param  array  $with
     * @param  array  $columns
     * @return Payment
     */
    public function getPayment(int $paymentId, array $with = [], array $columns = ['*']): Payment
    {
        return $this->paymentRepo->firstOrFail($paymentId, $with, $columns);
    }

    /**
     * Create new Payment
     *
     * @param  CreatePaymentData  $data
     * @return Payment
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function createPayment(CreatePaymentData $data): Payment
    {
        $jar = Jar::with(['account'])->findOrFail($data->jar_id);

        $rate = $data->currency === $jar->account->currency
            ? 1
            : $this->currencyConverter->getRate($data->currency, $jar->account->currency);

        return $this->paymentRepo->create([
            ...$data->toArray(),
            'amount' => $data->amount,
            'amount_converted' => round($data->amount * $rate, 4),
        ]);
    }

    /**
     * Update Payment by id
     *
     * @param  Payment|int  $payment
     * @param  UpdatePaymentData  $data
     * @return bool
     *
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function updatePayment(Payment|int $payment, UpdatePaymentData $data): bool
    {
        $paymentId = $payment instanceof Payment ? $payment->id : $payment;

        $jar = Jar::with(['account'])->findOrFail($data->jar_id);

        $rate = $data->currency === $jar->account->currency
            ? 1
            : $this->currencyConverter->getRate($data->currency, $jar->account->currency);

        return $this->paymentRepo->update($paymentId, [
            ...$data->toArray(),
            'amount' => $data->amount,
            'amount_converted' => round($data->amount * $rate, 4),
        ]);
    }

    /**
     * Delete Payment by id
     *
     * @param  int  $paymentId
     * @return bool
     */
    public function deletePayment(int $paymentId): bool
    {
        return $this->paymentRepo->delete($paymentId);
    }

    /**
     * Find all currency payment and update their amount
     *
     * @return void
     *
     * @throws Exception
     */
    public function updateCurrencyAmounts(): void
    {
        Payment::join('jars', 'payments.jar_id', '=', 'jars.id')
               ->join('accounts', 'jars.account_id', '=', 'accounts.id')
               ->where('payments.currency', '!=', 'accounts.currency')
               ->select('payments.*')
               ->chunk(1000, function (Collection $payments) {
                   $payments->each(function (Payment $payment) {
                       dispatch(new UpdatePaymentCurrencyAmountJob($payment));
                   });
               });
    }

    /**
     * Update payment amount on a fresh currency rate
     *
     * @param  Payment|int  $payment
     * @return void
     *
     * @throws Exception
     * @throws GuzzleException
     */
    public function updateCurrencyAmount(Payment|int $payment): void
    {
        $payment = $payment instanceof Payment ? $payment : Payment::find($payment);

        if ($payment->currency !== $payment->jar->account->currency) {
            $this->updatePayment(
                $payment,
                new UpdatePaymentData([
                    ...$payment->toArray(),
                    'currency' => $payment->currency,
                    'date' => $payment->date,
                    'ends_on' => $payment->ends_on,
                ])
            );
        }
    }

    /**
     * @return void
     */
    public function updateReducingPayments(): void
    {
        Payment::whereNotNull('ends_on')
               ->chunk(1000, function (Collection $payments) {
                   $payments->each(function (Payment $payment) {
                       dispatch(new UpdateReducingPaymentJob($payment));
                   });
               });
    }

    /**
     * @param  Payment|int  $payment
     * @return void
     *
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
            ])
        );
    }
}