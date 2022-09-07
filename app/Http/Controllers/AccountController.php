<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
            'payments' => function (HasManyThrough $query) {
                $selectBalance = DB::raw(
                    'sum(payments.amount) over (partition by jars.account_id order by payments.date, payments.amount rows between unbounded preceding and current row) as balance'
                );

                $selectJarBalance = DB::raw(
                    'sum(payments.amount) over (partition by payments.jar_id order by payments.date, payments.amount rows between unbounded preceding and current row) as jar_balance'
                );

                $selectJarSavingsBalance = DB::raw(
                    'if(jars.default, null, sum(payments.amount) over (partition by jars.default order by payments.date, payments.amount rows between unbounded preceding and current row)) as jar_savings_balance'
                );

                $query->select('payments.*')
                      ->addSelect($selectBalance)
                      ->addSelect($selectJarBalance)
                      ->addSelect($selectJarSavingsBalance)
                      ->orderBy('payments.date')
                      ->orderBy('payments.amount')
                      ->with([
                          'jar.account.jars',
                      ]);
            },
        ])->get();
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
