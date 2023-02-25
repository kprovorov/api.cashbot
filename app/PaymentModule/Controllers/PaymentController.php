<?php

namespace App\PaymentModule\Controllers;

use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\PaymentModule\DTO\CreatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentData;
use App\PaymentModule\Models\Payment;
use App\PaymentModule\Requests\StorePaymentRequest;
use App\PaymentModule\Requests\UpdatePaymentRequest;
use App\PaymentModule\Services\PaymentService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Str;

class PaymentController extends Controller
{
    /**
     * PaymentController constructor.
     */
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Collection
    {
        $with = ['account'];

        return $request->has('group')
            ? $this->paymentService->getPaymentsWhere('group', '=', $request->input('group'), $with)
            : $this->paymentService->getAllPayments($with);
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function store(StorePaymentRequest $request): void
    {
        $repeat = $request->input('repeat', 'none');

        $date = Carbon::parse(Carbon::parse($request->input('date')));
        $endsOn = $request->input('ends_on') ? Carbon::parse($request->input('ends_on')) : null;

        $group = Str::orderedUuid();

        if ($repeat === 'quarterly') {
            for ($i = 0; $i < 4; $i++) {
                $this->paymentService->createPayment(
                    new CreatePaymentData(
                        account_id: $request->input('account_id'),
                        group: $group,
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
                        account_id: $request->input('account_id'),
                        group: $group,
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
                        account_id: $request->input('account_id'),
                        group: $group,
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
                    account_id: $request->input('account_id'),
                    group: $group,
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
     */
    public function show(Payment $payment): Payment
    {
        return $this->paymentService->getPayment($payment->id, [
            'account',
            'from_transfer.payment_from.account',
            'to_transfer.payment_to.account',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function update(UpdatePaymentRequest $request, Payment $payment): void
    {
        $amount = (int) $request->input('amount');
        $endsOn = $request->input('ends_on') ? Carbon::parse($request->input('ends_on')) : null;

        $this->paymentService->updatePayment(
            $payment,
            new UpdatePaymentData(
                account_id: $request->input('account_id'),
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
                    account_id: $payment->from_transfer->payment_from->account_id,
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
                    account_id: $payment->to_transfer->payment_to->account_id,
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
