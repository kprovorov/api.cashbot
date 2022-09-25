<?php

namespace App\PaymentModule\Controllers;

use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\PaymentModule\DTO\CreatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentData;
use App\PaymentModule\Models\Payment;
use App\PaymentModule\Requests\StorePaymentRequest;
use App\PaymentModule\Requests\UpdatePaymentRequest;
use App\PaymentModule\Services\PaymentService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Exceptions\ValidationException;

class PaymentController extends Controller
{
    /**
     * PaymentController constructor.
     *
     * @param  PaymentService  $paymentService
     */
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->paymentService->getAllPayments();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePaymentRequest  $request
     * @return void
     *
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function store(StorePaymentRequest $request): void
    {
        $repeat = $request->input('repeat', 'none');

        $date = Carbon::parse(Carbon::parse($request->input('date')));
        $endsOn = $request->input('ends_on') ? Carbon::parse($request->input('ends_on')) : null;

        if ($repeat !== 'none') {
            $group = Group::create([
                'name' => $request->input('description'),
            ]);
        }

        if ($repeat === 'quarterly') {
            for ($i = 0; $i < 4; $i++) {
                $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jar_id: $request->input('jar_id'),
                        group_id: $group->id,
                        description: $request->input('description'),
                        amount: (int) $request->input('amount'),
                        currency: Currency::from($request->input('currency')),
                        date: $date->clone()->addMonthsNoOverflow($i * 3),
                        ends_on: $endsOn?->clone()->addMonthsNoOverflow($i * 3),
                    )
                );
            }
        } elseif ($repeat === 'monthly') {
            for ($i = 0; $i < 12; $i++) {
                $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jar_id: $request->input('jar_id'),
                        group_id: isset($group) ? $group->id : null,
                        description: $request->input('description'),
                        amount: (int) $request->input('amount'),
                        currency: Currency::from($request->input('currency')),
                        date: $date->clone()->addMonthsNoOverflow($i),
                        ends_on: $endsOn?->clone()->addMonthsNoOverflow($i),
                    )
                );
            }
        } elseif ($repeat === 'weekly') {
            for ($i = 0; $i < 52; $i++) {
                $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jar_id: $request->input('jar_id'),
                        group_id: isset($group) ? $group->id : null,
                        description: $request->input('description'),
                        amount: (int) $request->input('amount'),
                        currency: Currency::from($request->input('currency')),
                        date: $date->clone()->addWeeks($i),
                        ends_on: $endsOn?->clone()->addWeeks($i * 3),
                    )
                );
            }
        } else {
            $this->paymentService->createPayment(
                new CreatePaymentData(
                    jar_id: $request->input('jar_id'),
                    group_id: isset($group) ? $group->id : null,
                    description: $request->input('description'),
                    amount: (int) $request->input('amount'),
                    currency: Currency::from($request->input('currency')),
                    date: $date,
                    ends_on: $endsOn,
                )
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Payment  $payment
     * @return Payment
     */
    public function show(Payment $payment): Payment
    {
        return $this->paymentService->getPayment($payment->id, [
            'jar.account.jars',
            'from_transfer.payment_from.jar',
            'to_transfer.payment_to.jar',
            'group.payments',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdatePaymentRequest  $request
     * @param  Payment  $payment
     * @return Payment
     *
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function update(UpdatePaymentRequest $request, Payment $payment): void
    {
        $amount = (int) $request->input('amount');
        $endsOn = $request->input('ends_on') ? Carbon::parse($request->input('ends_on')) : null;

        $this->paymentService->updatePayment(
            $payment,
            new UpdatePaymentData(
                jar_id: $request->input('jar_id'),
                description: $request->input('description'),
                amount: $amount,
                currency: Currency::from($request->input('currency')),
                date: Carbon::parse($request->input('date')),
                ends_on: $endsOn,
                hidden: $request->input('hidden'),
            )
        );

        if ($payment->from_transfer) {
            $this->paymentService->updatePayment(
                $payment->from_transfer->payment_from,
                new UpdatePaymentData(
                    jar_id: $payment->from_transfer->payment_from->jar_id,
                    description: $request->input('description'),
                    amount: -$amount,
                    currency: Currency::from($request->input('currency')),
                    date: Carbon::parse($request->input('date')),
                    ends_on: $endsOn,
                    hidden: $request->input('hidden'),
                )
            );
        }

        if ($payment->to_transfer) {
            $this->paymentService->updatePayment(
                $payment->to_transfer->payment_to,
                new UpdatePaymentData(
                    jar_id: $payment->to_transfer->payment_to->jar_id,
                    description: $request->input('description'),
                    amount: -$amount,
                    currency: Currency::from($request->input('currency')),
                    date: Carbon::parse($request->input('date')),
                    ends_on: $endsOn,
                    hidden: $request->input('hidden'),
                )
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Payment  $payment
     * @return bool
     */
    public function destroy(Payment $payment): void
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