<?php

namespace App\PaymentModule\Seeders;

use App\AccountModule\Models\Account;
use App\PaymentModule\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Account::each(function (Account $account) {
            Payment::factory()->count(30)->create([
                'account_id' => $account->id,
                'currency' => $account->currency,
            ]);
        });
    }
}
