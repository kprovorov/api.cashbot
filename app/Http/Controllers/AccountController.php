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
        ])->get()->each->append('uah_balance');
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
