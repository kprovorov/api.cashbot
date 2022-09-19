<?php

namespace App\Http\Controllers;

use App\DTO\CreatePaymentData;
use App\DTO\UpdatePaymentData;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Group;
use App\Models\Jar;
use App\Models\Payment;
use App\Services\CurrencyConverter;
use App\Services\PaymentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PaymentController extends Controller
{
    public function __construct(
        protected readonly CurrencyConverter $currencyConverter,
        protected readonly PaymentService $paymentService
    ) {
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
        $repeat = $request->input('repeat', 'none');

        $date = Carbon::parse($request->input('date'));

        if ($repeat !== 'none') {
            $group = Group::create([
                'name' => $request->input('description'),
            ]);
        }

        if ($repeat === 'quarterly') {
            for ($i = 0; $i < 4; $i++) {
                $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $request->input('jar_id'),
                        groupId: $group->id,
                        description: $request->input('description'),
                        amount: (int)$request->input('amount'),
                        currency: $request->input('currency'),
                        date: $date->clone()->addMonthsNoOverflow($i * 3),
                    )
                );
            }
        } elseif ($repeat === 'monthly') {
            for ($i = 0; $i < 12; $i++) {
                $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $request->input('jar_id'),
                        groupId: isset($group) ? $group->id : null,
                        description: $request->input('description'),
                        amount: (int)$request->input('amount'),
                        currency: $request->input('currency'),
                        date: $date->clone()->addMonthsNoOverflow($i),
                    )
                );
            }
        } elseif ($repeat === 'weekly') {
            for ($i = 0; $i < 52; $i++) {
                $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $request->input('jar_id'),
                        groupId: isset($group) ? $group->id : null,
                        description: $request->input('description'),
                        amount: (int)$request->input('amount'),
                        currency: $request->input('currency'),
                        date: $date->clone()->addWeeks($i),
                    )
                );
            }
        } else {
            $this->paymentService->createPayment(
                new CreatePaymentData(
                    jarId: $request->input('jar_id'),
                    groupId: isset($group) ? $group->id : null,
                    description: $request->input('description'),
                    amount: (int)$request->input('amount'),
                    currency: $request->input('currency'),
                    date: $date,
                )
            );
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
     * @throws UnknownProperties
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $amount = (int)$request->input('amount');

        $this->paymentService->updatePayment(
            $payment,
            new UpdatePaymentData(
                jarId: $request->input('jar_id'),
                description: $request->input('description'),
                amount: $amount,
                currency: $request->input('currency'),
                date: $request->input('date'),
            )
        );

        if ($payment->from_transfer) {
            $this->paymentService->updatePayment(
                $payment->from_transfer->payment_from,
                new UpdatePaymentData(
                    jarId: $payment->from_transfer->payment_from->jar_id,
                    description: $request->input('description'),
                    amount: -$amount,
                    currency: $request->input('currency'),
                    date: $request->input('date'),
                )
            );
        }

        if ($payment->to_transfer) {
            $this->paymentService->updatePayment(
                $payment->to_transfer->payment_to,
                new UpdatePaymentData(
                    jarId: $payment->to_transfer->payment_to->jar_id,
                    description: $request->input('description'),
                    amount: -$amount,
                    currency: $request->input('currency'),
                    date: $request->input('date'),
                )
            );
        }
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
