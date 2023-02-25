<?php

namespace App\Http\Controllers;

use App\AccountModule\Models\Account;
use App\AccountModule\Services\AccountService;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
            'payments' => function (HasMany $query) {
                $selectBalance = DB::raw(
                    'accounts.balance + sum(payments.amount_converted) over (partition by payments.account_id order by payments.date, payments.created_at, payments.amount_converted rows between unbounded preceding and current row) as balance'
                );

                $query->join('accounts', 'accounts.id', '=', 'payments.account_id');

                $query->select('payments.*')
                      ->addSelect($selectBalance)
                      ->orderBy('payments.date')
                      ->orderBy('payments.created_at')
                      ->orderBy('payments.amount_converted')
                      ->with([
                          'account',
                      ]);
            },
        ])->get();
    }
}
