<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\Payment;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return Account::all();
    }

    public function payments(Account $account)
    {
        $selectBalance = DB::raw(
            'sum(amount) over (order by date rows between unbounded preceding and current row) as balance'
        );

        return Payment::select()
                      ->addSelect($selectBalance)
                      ->where('account_id', $account->id)
                      ->orderBy('date')
                      ->get();
    }

    public function createPayment(Account $account, StorePaymentRequest $request): void
    {
        $repeat = $request->input('repeat', 'none');

        if ($repeat === 'monthly') {
            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::parse($request->input('date'));

                Payment::create([
                    ...$request->only([
                        'description',
                        'amount',
                    ]),
                    'date'       => $date->addMonths($i),
                    'account_id' => $account->id,
                    'currency'   => $account->currency,
                ]);
            }
        } elseif ($repeat === 'weekly') {
            for ($i = 0; $i < 52; $i++) {
                $date = Carbon::parse($request->input('date'));

                Payment::create([
                    ...$request->only([
                        'description',
                        'amount',
                    ]),
                    'date'       => $date->addWeeks($i),
                    'account_id' => $account->id,
                    'currency'   => $account->currency,
                ]);
            }
        } else {
            Payment::create([
                ...$request->only([
                    'description',
                    'amount',
                    'date',
                ]),
                'account_id' => $account->id,
                'currency'   => $account->currency,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreAccountRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateAccountRequest $request
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }
}
