<?php

namespace App\AccountModule\Controllers;

use App\AccountModule\DTO\CreateAccountData;
use App\AccountModule\DTO\UpdateAccountData;
use App\AccountModule\Models\Account;
use App\AccountModule\Requests\StoreAccountRequest;
use App\AccountModule\Requests\UpdateAccountRequest;
use App\AccountModule\Services\AccountService;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountController extends Controller
{
    /**
     * AccountController constructor.
     *
     * @param  AccountService  $accountService
     */
    public function __construct(protected AccountService $accountService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Collection
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function index(Request $request): Collection
    {
        // Refresh balances
        $this->accountService->updateAccountBalances();

        return $this->accountService->getAllUserAccounts($request->user()->id, [
            'jars',
        ])->each->append('uah_balance');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreAccountRequest  $request
     * @return Account
     *
     * @throws UnknownProperties
     */
    public function store(StoreAccountRequest $request): Account
    {
        $data = new CreateAccountData($request->all());

        return $this->accountService->createAccount($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  Account  $account
     * @return Account
     */
    public function show(Account $account): Account
    {
        return $this->accountService->getAccount($account->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateAccountRequest  $request
     * @param  Account  $account
     * @return Account
     *
     * @throws UnknownProperties
     */
    public function update(UpdateAccountRequest $request, Account $account): Account
    {
        $data = new UpdateAccountData($request->all());

        $this->accountService->updateAccount($account->id, $data);

        return $this->accountService->getAccount($account->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Account  $account
     * @return bool
     */
    public function destroy(Account $account): bool
    {
        return $this->accountService->deleteAccount($account->id);
    }
}
