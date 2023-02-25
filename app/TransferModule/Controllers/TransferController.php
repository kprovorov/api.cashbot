<?php

namespace App\TransferModule\Controllers;

use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\PaymentModule\DTO\CreatePaymentData;
use App\PaymentModule\Services\PaymentService;
use App\TransferModule\DTO\UpdateTransferData;
use App\TransferModule\Models\Transfer;
use App\TransferModule\Requests\StoreTransferRequest;
use App\TransferModule\Requests\UpdateTransferRequest;
use App\TransferModule\Services\TransferService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Str;

class TransferController extends Controller
{
    /**
     * TransferController constructor.
     *
     * @param  TransferService  $transferService
     * @param  PaymentService  $paymentService
     */
    public function __construct(
        protected readonly TransferService $transferService,
        protected readonly PaymentService $paymentService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->transferService->getAllTransfers();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTransferRequest  $request
     * @return void
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function store(StoreTransferRequest $request): void
    {
        $repeat = $request->input('repeat', 'none');

        $date = Carbon::parse($request->input('date'));
        $amount = $request->input('amount');

        $group = Str::orderedUuid();

        if ($repeat === 'quarterly') {
            for ($i = 0; $i < 4; $i++) {
                $paymentFrom = $this->paymentService->createPayment(
                    new CreatePaymentData([
                        ...$request->validated(),
                        'account_id' => $request->input('account_from_id'),
                        'group' => $group,
                        'amount' => -$amount,
                        'currency' => Currency::from($request->input('currency')),
                        'date' => $date->clone()->addMonthsNoOverflow($i * 3),
                    ])
                );

                $paymentTo = $this->paymentService->createPayment(
                    new CreatePaymentData([
                        ...$request->validated(),
                        'account_id' => $request->input('account_to_id'),
                        'group' => $group,
                        'amount' => $amount,
                        'currency' => Currency::from($request->input('currency')),
                        'date' => $date->clone()->addMonthsNoOverflow($i * 3),
                    ])
                );

                Transfer::create([
                    'from_payment_id' => $paymentFrom->id,
                    'to_payment_id' => $paymentTo->id,
                ]);
            }
        } elseif ($repeat === 'monthly') {
            for ($i = 0; $i < 12; $i++) {
                $paymentFrom = $this->paymentService->createPayment(
                    new CreatePaymentData([
                        ...$request->validated(),
                        'account_id' => $request->input('account_from_id'),
                        'group' => $group,
                        'amount' => -$amount,
                        'currency' => Currency::from($request->input('currency')),
                        'date' => $date->clone()->addMonthsNoOverflow($i),
                    ])
                );

                $paymentTo = $this->paymentService->createPayment(
                    new CreatePaymentData([
                        ...$request->validated(),
                        'account_id' => $request->input('account_to_id'),
                        'group' => $group,
                        'amount' => $amount,
                        'currency' => Currency::from($request->input('currency')),
                        'date' => $date->clone()->addMonthsNoOverflow($i),
                    ])
                );

                Transfer::create([
                    'from_payment_id' => $paymentFrom->id,
                    'to_payment_id' => $paymentTo->id,
                ]);
            }
        } elseif ($repeat === 'weekly') {
            for ($i = 0; $i < 52; $i++) {
                $paymentFrom = $this->paymentService->createPayment(
                    new CreatePaymentData([
                        ...$request->validated(),
                        'account_id' => $request->input('account_from_id'),
                        'group' => $group,
                        'amount' => -$amount,
                        'currency' => Currency::from($request->input('currency')),
                        'date' => $date->clone()->addWeeks($i),
                    ])
                );

                $paymentTo = $this->paymentService->createPayment(
                    new CreatePaymentData([
                        ...$request->validated(),
                        'account_id' => $request->input('account_to_id'),
                        'group' => $group,
                        'amount' => $amount,
                        'currency' => Currency::from($request->input('currency')),
                        'date' => $date->clone()->addWeeks($i),
                    ])
                );

                Transfer::create([
                    'from_payment_id' => $paymentFrom->id,
                    'to_payment_id' => $paymentTo->id,
                ]);
            }
        } else {
            $paymentFrom = $this->paymentService->createPayment(
                new CreatePaymentData([
                    ...$request->validated(),
                    'account_id' => $request->input('account_from_id'),
                    'group' => $group,
                    'amount' => -$amount,
                    'currency' => Currency::from($request->input('currency')),
                    'date' => $date,
                ])
            );

            $paymentTo = $this->paymentService->createPayment(
                new CreatePaymentData([
                    ...$request->validated(),
                    'account_id' => $request->input('account_to_id'),
                    'group' => $group,
                    'amount' => $amount,
                    'currency' => Currency::from($request->input('currency')),
                    'date' => $date,
                ])
            );

            Transfer::create([
                'from_payment_id' => $paymentFrom->id,
                'to_payment_id' => $paymentTo->id,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Transfer  $transfer
     * @return Transfer
     */
    public function show(Transfer $transfer): Transfer
    {
        return $this->transferService->getTransfer($transfer->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateTransferRequest  $request
     * @param  Transfer  $transfer
     * @return Transfer
     *
     * @throws UnknownProperties
     */
    public function update(UpdateTransferRequest $request, Transfer $transfer): Transfer
    {
        $data = new UpdateTransferData($request->all());

        $this->transferService->updateTransfer($transfer->id, $data);

        return $this->transferService->getTransfer($transfer->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Transfer  $transfer
     * @return bool
     */
    public function destroy(Transfer $transfer): bool
    {
        return $this->transferService->deleteTransfer($transfer->id);
    }
}
