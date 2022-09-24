<?php

namespace App\Services;

use App\DTO\CreatePaymentData;
use App\DTO\UpdatePaymentData;
use App\Jobs\UpdatePaymentCurrencyAmountJob;
use App\Jobs\UpdateReducingPaymentJob;
use App\Models\Jar;
use App\Models\Payment;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PaymentService
{
    public function __construct(protected readonly CurrencyConverter $currencyConverter)
    {
    }

    /**
     * Create a Payment
     *
     * @param CreatePaymentData $data
     * @return Payment
     * @throws Exception
     */
    public function createPayment(CreatePaymentData $data): Payment
    {
        $jar = Jar::with(['account'])->findOrFail($data->jar_id);

        $rate = $data->currency === $jar->account->currency
            ? 1
            : $this->currencyConverter->getRate($data->currency, $jar->account->currency);

        return Payment::create([
            'amount'          => round($data->amount * $rate, 4),
            'original_amount' => $data->amount,
            ...$data->toArray(),
        ]);
    }

    public function updatePayment(Payment|int $payment, UpdatePaymentData $data)
    {
        $paymentId = $payment instanceof Payment ? $payment->id : $payment;

        $jar = Jar::with(['account'])->findOrFail($data->jar_id);

        $rate = $data->currency === $jar->account->currency
            ? 1
            : $this->currencyConverter->getRate($data->currency, $jar->account->currency);

        Payment::where('id', $paymentId)->update([
            ...$data->toArray(),
            'amount'          => round($data->amount * $rate, 4),
            'original_amount' => $data->amount,
        ]);
    }

    /**
     * Find all currency payment and update their amount
     *
     * @return void
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
     * @param Payment|int $payment
     * @return void
     * @throws Exception
     */
    public function updateCurrencyAmount(Payment|int $payment): void
    {
        $payment = $payment instanceof Payment ? $payment : Payment::find($payment);

        $payment->load('jar.account');

        if ($payment->currency !== $payment->jar->account->currency) {
            $rate = $this->currencyConverter->getRate($payment->currency, $payment->jar->account->currency);

            $payment->amount = round($payment->original_amount * $rate, 4);
            $payment->save();
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

    public function updateReducingPayment(Payment|int $payment)
    {
        $payment = $payment instanceof Payment ? $payment->refresh() : Payment::find($payment);

        $totalDays = $payment->date->diffInDays($payment->ends_on);
        $daysLeft = $payment->ends_on->diffInDays(today());

        $amount = round($payment->original_amount / $totalDays * $daysLeft, 4);

        $this->updatePayment(
            $payment,
            new UpdatePaymentData([
                ...$payment->toArray(),
                'currency' => $payment->currency,
                'amount'   => $amount,
                'ends_on'  => $payment->ends_on,
                'date'     => today(),
            ])
        );
    }
}
