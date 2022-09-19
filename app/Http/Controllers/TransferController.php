<?php

namespace App\Http\Controllers;

use App\DTO\CreatePaymentData;
use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Account;
use App\Models\Group;
use App\Models\Jar;
use App\Models\Payment;
use App\Models\Transfer;
use App\Services\CurrencyConverter;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class TransferController extends Controller
{
    public function __construct(protected readonly PaymentService $paymentService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTransferRequest $request
     * @return void
     * @throws UnknownProperties
     */
    public function store(StoreTransferRequest $request): void
    {
        $repeat = $request->input('repeat', 'none');

        $date = Carbon::parse($request->input('date'));
        $amount = $request->input('amount');

        $jarFrom = Jar::with('account')->findOrFail($request->input('jar_from_id'));
        $jarTo = Jar::with('account')->findOrFail($request->input('jar_to_id'));

        if ($repeat !== 'none') {
            $group = Group::create([
                'name' => "Transfer from {$jarFrom->account->name} ({$jarFrom->name}) to {$jarTo->account->name} ({$jarTo->name})",
            ]);
        }

        if ($repeat === 'quarterly') {
            for ($i = 0; $i < 4; $i++) {
                $paymentFrom = $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $jarFrom->id,
                        groupId: isset($group) ? $group->id : null,
                        description: $request->input(
                            'description',
                            "Transfer to {$jarTo->account->name} ({$jarTo->name})"
                        ),
                        amount: -$amount,
                        currency: $request->input('currency'),
                        date: $date->clone()->addMonthsNoOverflow($i * 3),
                    )
                );

                $paymentTo = $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $jarTo->id,
                        groupId: isset($group) ? $group->id : null,
                        description: $request->input(
                            'description',
                            "Transfer from {$jarFrom->account->name} ({$jarFrom->name})"
                        ),
                        amount: $amount,
                        currency: $request->input('currency'),
                        date: $date->clone()->addMonthsNoOverflow($i * 3),
                    )
                );

                Transfer::create([
                    'from_payment_id' => $paymentFrom->id,
                    'to_payment_id'   => $paymentTo->id,
                ]);
            }
        } elseif ($repeat === 'monthly') {
            for ($i = 0; $i < 12; $i++) {
                $paymentFrom = $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $jarFrom->id,
                        groupId: isset($group) ? $group->id : null,
                        description: $request->input(
                            'description',
                            "Transfer to {$jarTo->account->name} ({$jarTo->name})"
                        ),
                        amount: -$amount,
                        currency: $request->input('currency'),
                        date: $date->clone()->addMonthsNoOverflow($i),
                    )
                );

                $paymentTo = $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $jarTo->id,
                        groupId: isset($group) ? $group->id : null,
                        description: $request->input(
                            'description',
                            "Transfer from {$jarFrom->account->name} ({$jarFrom->name})"
                        ),
                        amount: $amount,
                        currency: $request->input('currency'),
                        date: $date->clone()->addMonthsNoOverflow($i),
                    )
                );

                Transfer::create([
                    'from_payment_id' => $paymentFrom->id,
                    'to_payment_id'   => $paymentTo->id,
                ]);
            }
        } elseif ($repeat === 'weekly') {
            for ($i = 0; $i < 52; $i++) {
                $paymentFrom = $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $jarFrom->id,
                        groupId: isset($group) ? $group->id : null,
                        description: $request->input(
                            'description',
                            "Transfer to {$jarTo->account->name} ({$jarTo->name})"
                        ),
                        amount: -$amount,
                        currency: $request->input('currency'),
                        date: $date->clone()->addWeeks($i),
                    )
                );

                $paymentTo = $this->paymentService->createPayment(
                    new CreatePaymentData(
                        jarId: $jarTo->id,
                        groupId: isset($group) ? $group->id : null,
                        description: $request->input(
                            'description',
                            "Transfer from {$jarFrom->account->name} ({$jarFrom->name})"
                        ),
                        amount: $amount,
                        currency: $request->input('currency'),
                        date: $date->clone()->addWeeks($i),
                    )
                );

                Transfer::create([
                    'from_payment_id' => $paymentFrom->id,
                    'to_payment_id'   => $paymentTo->id,
                ]);
            }
        } else {
            $paymentFrom = $this->paymentService->createPayment(
                new CreatePaymentData(
                    jarId: $jarFrom->id,
                    groupId: isset($group) ? $group->id : null,
                    description: $request->input(
                        'description',
                        "Transfer to {$jarTo->account->name} ({$jarTo->name})"
                    ),
                    amount: -$amount,
                    currency: $request->input('currency'),
                    date: $date,
                )
            );

            $paymentTo = $this->paymentService->createPayment(
                new CreatePaymentData(
                    jarId: $jarTo->id,
                    groupId: isset($group) ? $group->id : null,
                    description: $request->input(
                        'description',
                        "Transfer from {$jarFrom->account->name} ({$jarFrom->name})"
                    ),
                    amount: $amount,
                    currency: $request->input('currency'),
                    date: $date,
                )
            );

            Transfer::create([
                'from_payment_id' => $paymentFrom->id,
                'to_payment_id'   => $paymentTo->id,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Transfer $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $transfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateTransferRequest $request
     * @param \App\Models\Transfer $transfer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransferRequest $request, Transfer $transfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Transfer $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $transfer)
    {
        //
    }
}
