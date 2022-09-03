<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Account;
use App\Models\Payment;
use App\Models\Transfer;

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
     * @param \App\Http\Requests\StoreTransferRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransferRequest $request)
    {
        $date = $request->input('date');
        $amount = $request->input('amount');
        $rate = $request->input('rate');

        $accountFrom = Account::findOrFail($request->input('account_from_id'));
        $accountTo = Account::findOrFail($request->input('account_to_id'));

        $paymentFrom = Payment::create([
            'account_id' => $accountFrom->id,
            'description' => "Transfer to {$accountTo->name}",
            'amount' => -$amount,
            'currency' => $accountFrom->currency,
            'date' => $date,
        ]);

        $paymentTo = Payment::create([
            'account_id' => $accountTo->id,
            'description' => "Transfer from {$accountFrom->name}",
            'amount' => round($amount * $rate / 10000),
            'currency' => $accountTo->currency,
            'date' => $date,
        ]);

        return Transfer::create([
            'from_payment_id' => $paymentFrom->id,
            'to_payment_id'   => $paymentTo->id,
        ]);
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
