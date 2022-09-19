<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Group;
use App\Models\Jar;
use App\Models\Payment;
use App\Services\CurrencyConverter;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(protected readonly CurrencyConverter $currencyConverter)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePaymentRequest $request
     * @return void
     * @throws Exception
     */
    public function store(StorePaymentRequest $request)
    {
        $jar = Jar::with(['account'])->findOrFail($request->input('jar_id'));
        $repeat = $request->input('repeat', 'none');

        $paymentCurrency = $request->input('currency', $jar->account->currency);

        $rate = $paymentCurrency === $jar->account->currency
            ? 1
            : $this->currencyConverter->getRate($paymentCurrency, $jar->account->currency)['sell'];

        $date = Carbon::parse($request->input('date'));

        $originalAmount = (int)$request->input('amount');

        if ($repeat === 'quarterly') {
            $group = Group::create([
                'name' => $request->input('description'),
            ]);

            for ($i = 0; $i < 4; $i++) {
                Payment::create([
                    'description'     => $request->input('description'),
                    'amount'          => round($originalAmount * $rate, 4),
                    'original_amount' => $originalAmount,
                    'currency'        => $paymentCurrency,
                    'group_id'        => $group->id,
                    'date'            => $date->clone()->addMonthsNoOverflow($i * 3),
                    'jar_id'          => $jar->id,
                ]);
            }
        } elseif ($repeat === 'monthly') {
            $group = Group::create([
                'name' => $request->input('description'),
            ]);

            for ($i = 0; $i < 12; $i++) {
                Payment::create([
                    'description'     => $request->input('description'),
                    'amount'          => round($originalAmount * $rate, 4),
                    'original_amount' => $originalAmount,
                    'currency'        => $paymentCurrency,
                    'group_id'        => $group->id,
                    'date'            => $date->clone()->addMonthsNoOverflow($i),
                    'jar_id'          => $jar->id,
                ]);
            }
        } elseif ($repeat === 'weekly') {
            $group = Group::create([
                'name' => $request->input('description'),
            ]);

            for ($i = 0; $i < 52; $i++) {
                Payment::create([
                    'description'     => $request->input('description'),
                    'amount'          => round($originalAmount * $rate, 4),
                    'original_amount' => $originalAmount,
                    'currency'        => $paymentCurrency,
                    'group_id'        => $group->id,
                    'date'            => $date->clone()->addWeeks($i),
                    'jar_id'          => $jar->id,

                ]);
            }
        } else {
            Payment::create([
                'description'     => $request->input('description'),
                'amount'          => round($originalAmount * $rate, 4),
                'original_amount' => $originalAmount,
                'currency'        => $paymentCurrency,
                'date'            => $date,
                'jar_id'          => $jar->id,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Payment $payment
     * @return Response|Payment
     */
    public function show(Payment $payment): Response|Payment
    {
        $payment->load([
            'jar.account.jars',
            'from_transfer.payment_from.jar',
            'to_transfer.payment_to.jar',
            'group.payments',
        ]);

        return $payment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePaymentRequest $request
     * @param \App\Models\Payment $payment
     * @return Response
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $jar = Jar::with(['account'])->findOrFail($request->input('jar_id'));

        $paymentCurrency = $request->input('currency', $jar->account->currency);

        $rate = $paymentCurrency === $jar->account->currency
            ? 1
            : $this->currencyConverter->getRate($paymentCurrency, $jar->account->currency)['sell'];

        $originalAmount = (int)$request->input('amount');

        $payment->update(
            [
                ...$request->only([
                    'description',
                    'jar_id',
                    'amount',
                    'date',
                ]),
                'currency'        => $paymentCurrency,
                'amount'          => round($originalAmount * $rate, 4),
                'original_amount' => $originalAmount,
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Payment $payment
     * @return Response
     */
    public function destroy(Payment $payment)
    {
        $transfer = $payment->from_transfer ?? $payment->to_transfer;
        if ($transfer) {
            $transfer->payment_from->delete();
            $transfer->payment_to->delete();
            $transfer->delete();
        } else {
            $payment->delete();
        }
    }
}
