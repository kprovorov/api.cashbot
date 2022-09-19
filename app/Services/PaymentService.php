<?php

namespace App\Services;

use App\Jobs\UpdatePaymentCurrencyAmount;
use App\Models\Payment;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PaymentService
{
    public function __construct(protected readonly CurrencyConverter $currencyConverter)
    {
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
                       dispatch(new UpdatePaymentCurrencyAmount($payment));
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
            $rate = $this->currencyConverter->getRate($payment->currency, $payment->jar->account->currency)['sell'];

            $payment->amount = round($payment->original_amount * $rate, 4);
            $payment->save();
        }
    }
}
