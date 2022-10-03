<?php

namespace App\Http\Controllers;

use App\AccountModule\Models\Account;
use App\AccountModule\Services\AccountService;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected readonly AccountService $accountService)
    {
    }

    /**
     * @return mixed
     */
    public function __invoke(Request $request): Collection
    {
        // Refresh balances
        $this->accountService->updateAccountBalances();

        return Account::where('user_id', $request->user()->id)->with([
            'jars',
            'payments' => function (HasManyThrough $query) {
                $selectBalance = DB::raw(
                    'accounts.balance + sum(payments.amount_converted) over (partition by jars.account_id order by payments.date, payments.created_at, payments.amount_converted rows between unbounded preceding and current row) as balance'
                );

                $selectJarBalance = DB::raw(
                    'sum(payments.amount_converted) over (partition by payments.jar_id order by payments.date, payments.created_at, payments.amount_converted rows between unbounded preceding and current row) as jar_balance'
                );

                $selectJarSavingsBalance = DB::raw(
                    'if(jars.default, null, sum(payments.amount_converted) over (partition by jars.default order by payments.date, payments.created_at, payments.amount_converted rows between unbounded preceding and current row)) as jar_savings_balance'
                );

                $query->join('accounts', 'accounts.id', '=', 'jars.account_id');

                $query->select('payments.*')
                      ->addSelect($selectBalance)
                      ->addSelect($selectJarBalance)
                      ->addSelect($selectJarSavingsBalance)
                      ->orderBy('payments.date')
                      ->orderBy('payments.created_at')
                      ->orderBy('payments.amount_converted')
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
