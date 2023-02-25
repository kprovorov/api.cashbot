<?php

namespace App\AccountModule\Seeders;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use App\PaymentModule\Models\Payment;
use App\TransferModule\Models\Transfer;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            [
                'name' => 'Wise FOP',
                'balance' => 0,
                'currency' => Currency::EUR,
            ],
            [
                'name' => 'Wise',
                'balance' => 28,
                'currency' => Currency::EUR,
            ],
            [
                'name' => 'Revolut',
                'balance' => 0,
                'currency' => Currency::USD,
            ],
            [
                'name' => 'Revolut',
                'balance' => 7933,
                'currency' => Currency::EUR,
            ],
            [
                'name' => 'Mono FOP',
                'balance' => 0,
                'currency' => Currency::EUR,
            ],
            [
                'name' => 'Mono FOP',
                'balance' => 2666,
                'currency' => Currency::USD,
            ],
            [
                'name' => 'Mono FOP',
                'balance' => 14397,
                'currency' => Currency::UAH,
            ],
            [
                'name' => 'Mono',
                'balance' => 0,
                'currency' => Currency::EUR,
            ],
            [
                'name' => 'Mono',
                'balance' => 0,
                'currency' => Currency::USD,
            ],
            [
                'name' => 'Mono White',
                'balance' => 22980,
                'currency' => Currency::UAH,
            ],
            [
                'name' => 'Mono Black',
                'balance' => 59692,
                'currency' => Currency::UAH,
            ],
        ])->each(function (array $accountData) {
            $account = Account::create([
                ...$accountData,
                'balance' => $accountData['balance'] * 10000,
                'user_id' => 1,
            ]);

            if ($accountData['name'] === 'Mono FOP' && $accountData['currency'] === Currency::USD) {
                Account::create([
                    ...$accountData,
                    'parent_id' => $account->id,
                    'name' => 'Backup',
                    'balance' => 0,
                    'user_id' => 1,
                ]);
            }
        });

//        Account::factory()
//               ->count(10)
//               ->hasJars(1, [
//                   'name' => 'Default',
//                   'default' => true,
//               ])
//               ->hasJars(2)
//               ->create([
//                   'user_id' => 1,
//               ]);

//
//        Payment::factory()
//               ->count(500)
//               ->create([
//                   'jar_id' => function () {
//                       return Jar::inRandomOrder()->first();
//                   },
//               ]);
//
//        Payment::factory()
//            ->count(500)
//            ->create([
//                'jar_id' => function () {
//                    return Jar::inRandomOrder()->first();
//                },
//            ]);
//
//        Transfer::factory()
//                ->count(1000)
//                ->create([
//                    'from_payment_id' => function () {
//                        return Payment::factory()
//                            ->create([
//                                'jar_id' => function () {
//                                    return Jar::inRandomOrder()->first();
//                                },
//                            ]);
//                    },
//                    'to_payment_id' => function () {
//                        return Payment::factory()
//                            ->create([
//                                'jar_id' => function () {
//                                    return Jar::inRandomOrder()->first();
//                                },
//                            ]);
//                    },
//                ]);
    }
}
