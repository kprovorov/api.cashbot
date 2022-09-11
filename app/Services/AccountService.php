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
     * @param Account $account
     * @return void
     * @throws UnknownProperties
     */
    public function updateAccountBalance(Account $account): void
    {
        if ($account->external_id) {
            $balance = $this->fetchMonobankAccountData($account)->balance * 100;

            $account->update(['balance' => $balance]);
        }
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
