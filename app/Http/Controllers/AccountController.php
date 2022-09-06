<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\Payment;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return Account::with([
            'jars',
        ])->get();
    }

    public function payments(Account $account)
    {
        $selectBalance = DB::raw(
            'sum(amount) over (order by date, amount rows between unbounded preceding and current row) as balance'
        );

        $selectJarBalance = DB::raw(
            'sum(amount) over (partition by jar_id order by date, amount rows between unbounded preceding and current row) as jar_balance'
        );

        return Payment::select()
                      ->addSelect($selectBalance)
                      ->addSelect($selectJarBalance)
                      ->whereIn('jar_id', $account->jars->pluck('id'))
                      ->with([
                          'jar',
                      ])
                      ->orderBy('date')
                      ->orderBy('amount')
                      ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreAccountRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateAccountRequest $request
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }
}
