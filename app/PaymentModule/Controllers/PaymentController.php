<?php

namespace App\PaymentModule\Controllers;

use App\Enums\Currency;
use App\Enums\RepeatUnit;
use App\Http\Controllers\Controller;
use App\PaymentModule\DTO\CreatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentGeneralData;
use App\PaymentModule\Models\Payment;
use App\PaymentModule\Requests\StorePaymentRequest;
use App\PaymentModule\Requests\UpdatePaymentGeneralRequest;
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
        $this->paymentService->createPayment(
            new CreatePaymentData([
                ...$request->validated(),
                'amount'         => (int) $request->input('amount'),
                'currency'       => Currency::from($request->input('currency')),
                'repeat_unit'    => RepeatUnit::from($request->input('repeat_unit')),
                'date'           => Carbon::parse($request->input('date')),
                'ends_on'        => $request->input('ends_on') ? Carbon::parse($request->input('ends_on')) : null,
                'repeat_ends_on' => $request->input('repeat_ends_on') ? Carbon::parse($request->input('repeat_ends_on')) : null,
            ])
        );
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

    public function updateGeneral(UpdatePaymentGeneralRequest $request, Payment $payment): void
    {
        $this->paymentService->updatePaymentGeneral(
            $payment,
            Carbon::parse($request->input('fromDate')),
            new UpdatePaymentGeneralData([
                ...$request->validated(),
                'currency' => Currency::from($request->input('currency'))
            ])
        );
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
            new UpdatePaymentData([
                ...$request->validated(),
                'amount'         => $amount,
                'currency'       => Currency::from($request->input('currency')),
                'date'           => Carbon::parse($request->input('date')),
                'ends_on'        => $endsOn,
                'repeat_unit'    => RepeatUnit::from($request->input('repeat_unit')),
                'repeat_ends_on' => $request->input('repeat_ends_on') ? Carbon::parse($request->input('repeat_ends_on')) : null,
            ])
        );

        if ($payment->from_transfer) {
            $this->paymentService->updatePayment(
                $payment->from_transfer->payment_from,
                new UpdatePaymentData([
                    ...$request->validated(),
                    'account_id'     => $payment->from_transfer->payment_from->account_id,
                    'amount'         => -$amount,
                    'currency'       => Currency::from($request->input('currency')),
                    'date'           => Carbon::parse($request->input('date')),
                    'ends_on'        => $endsOn,
                    'repeat_unit'    => RepeatUnit::from($request->input('repeat_unit')),
                    'repeat_ends_on' => $request->input('repeat_ends_on') ? Carbon::parse($request->input('repeat_ends_on')) : null,
                ])
            );
        }

        if ($payment->to_transfer) {
            $this->paymentService->updatePayment(
                $payment->to_transfer->payment_to,
                new UpdatePaymentData([
                    ...$request->validated(),
                    'account_id'     => $payment->to_transfer->payment_to->account_id,
                    'amount'         => -$amount,
                    'currency'       => Currency::from($request->input('currency')),
                    'date'           => Carbon::parse($request->input('date')),
                    'ends_on'        => $endsOn,
                    'repeat_unit'    => RepeatUnit::from($request->input('repeat_unit')),
                    'repeat_ends_on' => $request->input('repeat_ends_on') ? Carbon::parse($request->input('repeat_ends_on')) : null,
                ])
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
        }
        else {
            $payment->delete();
        }
    }

    public function deleteGroup(string $group): void
    {
        $this->paymentService->deleteGroup($group);
    }
}
