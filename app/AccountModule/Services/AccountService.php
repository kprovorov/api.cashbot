<?php

namespace App\AccountModule\Services;

use App\AccountModule\DTO\CreateAccountData;
use App\AccountModule\DTO\UpdateAccountData;
use App\AccountModule\Models\Account;
use App\AccountModule\Repositories\AccountRepo;
use App\Monobank\DTO\AccountData;
use App\Monobank\Services\MonobankService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountService
{
    /**
     * AccountService constructor.
     *
     * @param AccountRepo $accountRepo
     * @param MonobankService $monobankService
     */
    public function __construct(protected AccountRepo $accountRepo, private readonly MonobankService $monobankService)
    {
    }

    /**
     * Get all Accounts
     *
     * @param array $with
     * @param array $columns
     * @return Collection
     */
    public function getAllAccounts(array $with = [], array $columns = ['*']): Collection
    {
        return $this->accountRepo->getAll($with, $columns);
    }

    /**
     * Get all Accounts paginated
     *
     * @param int|null $perPage
     * @param int|null $page
     * @param array $with
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function getAllAccountsPaginated(
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ['*']
    ): LengthAwarePaginator {
        return $this->accountRepo->paginateAll($perPage, $page, $with, $columns);
    }

    /**
     * Get Account by id
     *
     * @param int $accountId
     * @param array $with
     * @param array $columns
     * @return Account
     */
    public function getAccount(int $accountId, array $with = [], array $columns = ['*']): Account
    {
        return $this->accountRepo->firstOrFail($accountId, $with, $columns);
    }

    /**
     * Create new Account
     *
     * @param CreateAccountData $data
     * @return Account
     */
    public function createAccount(CreateAccountData $data): Account
    {
        return $this->accountRepo->create($data->toArray());
    }

    /**
     * Update Account by id
     *
     * @param int $accountId
     * @param UpdateAccountData $data
     * @return bool
     */
    public function updateAccount(int $accountId, UpdateAccountData $data): bool
    {
        return $this->accountRepo->update($accountId, $data->toArray());
    }

    /**
     * Delete Account by id
     *
     * @param int $accountId
     * @return bool
     */
    public function deleteAccount(int $accountId): bool
    {
        return $this->accountRepo->delete($accountId);
    }

    /**
     * Update balances for all accounts
     *
     *
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
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function fetchMonobankAccountData(Account $account): AccountData
    {
        $clientInfo = $this->monobankService->getClientInfo();

        return $clientInfo->accounts->where('id', $account->external_id)->first();
    }
}
