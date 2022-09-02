<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        Account::all()->each(function (Account $account) {
            Payment::factory()->count(5)->create([
                'account_id' => $account->id,
                'currency'   => $account->currency,
            ]);
        });
    }
}
