<?php

namespace App\Services;

use App\DTO\MonobankAccountData;
use App\Models\Account;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountService
{
    public function __construct(private readonly MonobankApi $monobankApi)
    {
    }

    /**
     * Update balances for all accounts
     *
     * @return void
     * @throws UnknownProperties
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
     * @return MonobankAccountData
     * @throws UnknownProperties
     */
    public function fetchMonobankAccountData(Account $account): MonobankAccountData
    {
        $clientInfo = $this->monobankApi->getClientInfo();

        return $clientInfo->accounts->where('id', $account->external_id)->first();
    }
}
