<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountService;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DashboardController extends Controller
{
    public function __construct(protected readonly AccountService $accountService)
    {
    }

    /**
     * @return mixed
     * @throws UnknownProperties
     */
    public function __invoke(): Collection
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
                          'jar.account',
                          'from_transfer.payment_from.jar',
                          'to_transfer.payment_to.jar',
                          'group.payments',
                      ]);
            },
        ])->get();
    }
}
