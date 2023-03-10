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
        $with = ['account_to', 'account_from'];

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
    public function store(StorePaymentRequest $request): Payment
    {
        return $this->paymentService->createPayment(
            new CreatePaymentData([
                ...$request->validated(),
                'amount' => (int) $request->input('amount'),
                'currency' => Currency::from($request->input('currency')),
                'repeat_unit' => RepeatUnit::from($request->input('repeat_unit')),
                'date' => Carbon::parse($request->input('date')),
                'ends_on' => $request->input('ends_on') ? Carbon::parse($request->input('ends_on')) : null,
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
            'account_to',
            'account_from'
        ]);
    }

    public function updateGeneral(UpdatePaymentGeneralRequest $request, Payment $payment): void
    {
        $this->paymentService->updatePaymentGeneral(
            $payment,
            Carbon::parse($request->input('from_date')),
            new UpdatePaymentGeneralData([
                ...$request->validated(),
                'currency' => Currency::from($request->input('currency')),
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

        $dataToUpdate = [
            ...$request->validated(),
            'currency' => Currency::from($request->input('currency')),
            'date' => Carbon::parse($request->input('date')),
            'ends_on' => $endsOn,
            'repeat_unit' => RepeatUnit::from($request->input('repeat_unit')),
            'repeat_ends_on' => $request->input('repeat_ends_on') ? Carbon::parse($request->input('repeat_ends_on')) : null,
        ];

        $this->paymentService->updatePayment(
            $payment,
            new UpdatePaymentData([
                ...$dataToUpdate,
                'amount' => $amount,
            ])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment, Request $request): void
    {
        $date = Carbon::parse($request->input('date'));

        if ($date) {
            $this->paymentService->cutoffPayment($payment, $date);
        } else {
            $this->paymentService->deletePayment($payment);
        }
    }

    public function deleteGroup(string $group): void
    {
        $this->paymentService->deleteGroup($group);
    }
}
