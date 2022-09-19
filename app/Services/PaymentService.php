<?php

namespace App\Services;

use App\DTO\CreatePaymentData;
use App\DTO\UpdatePaymentData;
use App\Jobs\UpdatePaymentCurrencyAmount;
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
        $jar = Jar::with(['account'])->findOrFail($data->jarId);

        $rate = $data->currency === $jar->account->currency
            ? 1
            : $this->currencyConverter->getRate($data->currency, $jar->account->currency)['sell'];

        return Payment::create([
            'description'     => $data->description,
            'amount'          => round($data->amount * $rate, 4),
            'original_amount' => $data->amount,
            'currency'        => $data->currency,
            'group_id'        => $data->groupId,
            'date'            => $data->date,
            'jar_id'          => $data->jarId,
        ]);
    }

    public function updatePayment(Payment|int $payment, UpdatePaymentData $data)
    {
        $paymentId = $payment instanceof Payment ? $payment->id : $payment;

        $jar = Jar::with(['account'])->findOrFail($data->jarId);

        $rate = $data->currency === $jar->account->currency
            ? 1
            : $this->currencyConverter->getRate($data->currency, $jar->account->currency)['sell'];

        Payment::where('id', $paymentId)->update([
            'description'     => $data->description,
            'amount'          => round($data->amount * $rate, 4),
            'original_amount' => $data->amount,
            'currency'        => $data->currency,
            'date'            => $data->date,
            'jar_id'          => $data->jarId,
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
