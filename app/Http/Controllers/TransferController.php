<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Account;
use App\Models\Jar;
use App\Models\Payment;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TransferController extends Controller
{
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
     * @return Transfer
     */
    public function store(StoreTransferRequest $request): void
    {
        $repeat = $request->input('repeat', 'none');

        $date = Carbon::parse($request->input('date'));
        $amount = $request->input('amount');
        $rate = $request->input('rate');

        $jarFrom = Jar::with('account')->findOrFail($request->input('jar_from_id'));
        $jarTo = Jar::with('account')->findOrFail($request->input('jar_to_id'));

        if ($repeat === 'monthly') {
            for ($i = 0; $i < 12; $i++) {
                $paymentFrom = Payment::create([
                    'jar_id'      => $jarFrom->id,
                    'description' => "Transfer to {$jarTo->account->name} ({$jarTo->name})",
                    'amount'      => -$amount,
                    'currency'    => $jarFrom->account->currency,
                    'date'        => $date->clone()->addMonths($i),
                ]);

                $paymentTo = Payment::create([
                    'jar_id'      => $jarTo->id,
                    'description' => "Transfer from {$jarFrom->account->name} ({$jarFrom->name})",
                    'amount'      => round($amount * $rate / 10000),
                    'currency'    => $jarTo->account->currency,
                    'date'        => $date->clone()->addMonths($i),
                ]);

                Transfer::create([
                    'from_payment_id' => $paymentFrom->id,
                    'to_payment_id'   => $paymentTo->id,
                ]);
            }
        } elseif ($repeat === 'weekly') {
            for ($i = 0; $i < 52; $i++) {
                $paymentFrom = Payment::create([
                    'jar_id'      => $jarFrom->id,
                    'description' => "Transfer to {$jarTo->account->name} ({$jarTo->name})",
                    'amount'      => -$amount,
                    'currency'    => $jarFrom->account->currency,
                    'date'        => $date->clone()->addWeeks($i),
                ]);

                $paymentTo = Payment::create([
                    'jar_id'      => $jarTo->id,
                    'description' => "Transfer from {$jarFrom->account->name} ({$jarFrom->name})",
                    'amount'      => round($amount * $rate / 10000),
                    'currency'    => $jarTo->account->currency,
                    'date'        => $date->clone()->addWeeks($i),
                ]);

                Transfer::create([
                    'from_payment_id' => $paymentFrom->id,
                    'to_payment_id'   => $paymentTo->id,
                ]);
            }
        } else {
            $paymentFrom = Payment::create([
                'jar_id'      => $jarFrom->id,
                'description' => "Transfer to {$jarTo->account->name} ({$jarTo->name})",
                'amount'      => -$amount,
                'currency'    => $jarFrom->account->currency,
                'date'        => $date,
            ]);

            $paymentTo = Payment::create([
                'jar_id'      => $jarTo->id,
                'description' => "Transfer from {$jarFrom->account->name} ({$jarFrom->name})",
                'amount'      => round($amount * $rate / 10000),
                'currency'    => $jarTo->account->currency,
                'date'        => $date,
            ]);

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
