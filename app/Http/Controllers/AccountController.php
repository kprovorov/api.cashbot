<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountController extends Controller
{
    public function __construct(protected readonly AccountService $accountService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     * @throws UnknownProperties
     */
    public function index(): Collection
    {
        // Refresh balances
        $this->accountService->updateAccountBalances();

        return Account::with([
            'jars',
            'payments' => function (HasManyThrough $query) {
                $selectBalance = DB::raw(
                    'accounts.balance + sum(payments.amount) over (partition by jars.account_id order by payments.date, payments.created_at, payments.amount rows between unbounded preceding and current row) as balance'
                );

                $selectJarBalance = DB::raw(
                    'sum(payments.amount) over (partition by payments.jar_id order by payments.date, payments.created_at, payments.amount rows between unbounded preceding and current row) as jar_balance'
                );

                $selectJarSavingsBalance = DB::raw(
                    'if(jars.default, null, sum(payments.amount) over (partition by jars.default order by payments.date, payments.created_at, payments.amount rows between unbounded preceding and current row)) as jar_savings_balance'
                );

                $query->join('accounts', 'accounts.id', '=', 'jars.account_id');

                $query->select('payments.*')
                      ->addSelect($selectBalance)
                      ->addSelect($selectJarBalance)
                      ->addSelect($selectJarSavingsBalance)
                      ->orderBy('payments.date')
                      ->orderBy('payments.created_at')
                      ->orderBy('payments.amount')
                      ->with([
                          'jar.account.jars',
                          'from_transfer.payment_from.jar',
                          'to_transfer.payment_to.jar',
                          'group.payments'
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
        $account->update(
            $request->only([
                'name',
                'balance',
            ])
        );
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
