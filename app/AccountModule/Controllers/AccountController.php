<?php

namespace App\AccountModule\Controllers;

use App\AccountModule\DTO\CreateAccountData;
use App\AccountModule\DTO\UpdateAccountData;
use App\AccountModule\Models\Account;
use App\AccountModule\Requests\StoreAccountRequest;
use App\AccountModule\Requests\UpdateAccountRequest;
use App\AccountModule\Services\AccountService;
use App\Enums\Currency;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountController extends Controller
{
    /**
     * AccountController constructor.
     */
    public function __construct(protected AccountService $accountService)
    {
    }

    /**
     * Display a listing of the resource.
     *
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
            'payments_from',
            'payments_to',
            'payments_from.account_from', 'payments_from.account_to',
            'payments_to.account_from', 'payments_to.account_to',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws UnknownProperties
     */
    public function store(StoreAccountRequest $request): Account
    {
        $data = new CreateAccountData([
            ...$request->validated(),
            'currency' => Currency::from($request->validated('currency')),
            'user_id' => $request->user()->id,
        ]);

        return $this->accountService->createAccount($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account): Account
    {
        return $this->accountService->getAccount($account->id);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @throws UnknownProperties
     */
    public function update(UpdateAccountRequest $request, Account $account): Account
    {
        $data = new UpdateAccountData([
            ...$request->validated(),
            'currency' => Currency::from($request->validated('currency')),
        ]);

        $this->accountService->updateAccount($account, $data);

        return $this->accountService->getAccount($account->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account): bool
    {
        return $this->accountService->deleteAccount($account);
    }
}
