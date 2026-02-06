<?php

namespace App\AccountModule\Services;

use App\AccountModule\DTO\CreateAccountData;
use App\AccountModule\DTO\UpdateAccountData;
use App\AccountModule\Models\Account;
use App\AccountModule\Repositories\AccountRepo;
use App\Http\Integrations\Monobank\Monobank;
use App\Monobank\DTO\AccountData;
use App\PaymentModule\Repositories\PaymentRepo;
use App\UserModule\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountService
{
    /**
     * AccountService constructor.
     */
    public function __construct(
        protected AccountRepo $accountRepo,
        protected PaymentRepo $paymentRepo,
        private readonly Monobank $monobank,
    ) {}

    /**
     * Get all Accounts
     */
    public function getAllAccounts(
        array $with = [],
        array $columns = ["*"],
    ): Collection {
        return $this->accountRepo->getAll($with, $columns);
    }

    /**
     * Get all Accounts
     */
    public function getAllUserAccounts(
        User|int $user,
        array $with = [],
        array $columns = ["*"],
    ): Collection {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->accountRepo->getWhere(
            "user_id",
            "=",
            $userId,
            $with,
            $columns,
            "name",
            "asc",
        );
    }

    /**
     * Get all Accounts paginated
     */
    public function getAllAccountsPaginated(
        ?int $perPage = null,
        ?int $page = null,
        array $with = [],
        array $columns = ["*"],
    ): LengthAwarePaginator {
        return $this->accountRepo->paginateAll(
            $perPage,
            $page,
            $with,
            $columns,
        );
    }

    /**
     * Get Account by id
     */
    public function getAccount(
        int $accountId,
        array $with = [],
        array $columns = ["*"],
    ): Account {
        return $this->accountRepo->firstOrFail($accountId, $with, $columns);
    }

    /**
     * Create new Account
     */
    public function createAccount(CreateAccountData $data): Account
    {
        return $this->accountRepo->create([
            ...$data->toArray(),
            "currency" => $data->currency->value,
        ]);
    }

    /**
     * Update Account by id
     */
    public function updateAccount(
        int|Account $account,
        UpdateAccountData $data,
    ): bool {
        $accountId = $account instanceof Account ? $account->id : $account;

        return $this->accountRepo->update($accountId, [
            ...$data->toArray(),
            "currency" => $data->currency->value,
        ]);
    }

    /**
     * Delete Account by id
     */
    public function deleteAccount(int|Account $account): bool
    {
        $accountId = $account instanceof Account ? $account->id : $account;

        // Delete account payments
        $this->paymentRepo->deleteWhere("account_from_id", "=", $accountId);
        $this->paymentRepo->deleteWhere("account_to_id", "=", $accountId);

        // Find jars
        $jars = $this->accountRepo->getWhere("parent_id", "=", $accountId);

        // Delete jars with payments
        $jars->each(function (Account $jar) {
            $this->deleteAccount($jar->id);
        });

        return $this->accountRepo->delete($accountId);
    }

    /**
     * Update balances for all accounts
     *
     *
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function updateAccountBalancesForUser(int $userId): void
    {
        $accounts = Account::where("user_id", $userId)
            ->whereNotNull("provider_id")
            ->get();

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
        if ($account->provider_id) {
            $balance = $this->fetchAccountBalance($account);

            $account->update(["balance" => $balance]);
        }
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function fetchAccountBalance(Account $account): int
    {
        if ($account->provider === "monobank") {
            return $this->fetchMonobankAccountData($account)->balance;
        }

        return $account->balance;
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function fetchMonobankAccountData(Account $account): AccountData
    {
        $res = $this->monobank->getClientInfo();

        return $res
            ->dto()
            ->accounts->where("id", $account->provider_id)
            ->first();
    }
}
