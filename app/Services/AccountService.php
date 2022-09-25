<?php

namespace App\Services;

use App\Models\Account;
use App\Monobank\DTO\AccountData;
use App\Monobank\Services\MonobankService;
use GuzzleHttp\Exception\GuzzleException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountService
{
    public function __construct(private readonly MonobankService $monobankService)
    {
    }

    /**
     * Update balances for all accounts
     *
     * @return void
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function updateAccountBalances(): void
    {
        $accounts = Account::whereNotNull('external_id')->get();

        $accounts->each(function (Account $account) {
            $this->updateAccountBalance($account);
        });
    }

    /**
     * @param Account $account
     * @return void
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function updateAccountBalance(Account $account): void
    {
        if ($account->external_id) {
            $balance = $this->fetchAccountBalance($account);

            $account->update(['balance' => $balance]);
        }
    }

    /**
     * @param Account $account
     * @return int
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function fetchAccountBalance(Account $account): int
    {
        if ($account->provider === 'monobank') {
            return $this->fetchMonobankAccountData($account)->balance * 100;
        }

        return $account->balance;
    }

    /**
     * @param Account $account
     * @return AccountData
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function fetchMonobankAccountData(Account $account): AccountData
    {
        $clientInfo = $this->monobankService->getClientInfo();

        return $clientInfo->accounts->where('id', $account->external_id)->first();
    }
}
