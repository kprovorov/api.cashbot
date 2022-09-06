<?php

namespace Database\Seeders;

use App\Enums\Currency;
use App\Models\Account;
use App\Models\Jar;
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
        $monoBlack = Account::forceCreate([
            'name'     => 'Mono Black',
            'currency' => Currency::UAH,
            'balance'  => 0,
        ]);

        $monoWhite = Account::forceCreate([
            'name'     => 'Mono White',
            'currency' => Currency::UAH,
            'balance'  => 0,
        ]);

        $monoUSD = Account::forceCreate([
            'name'     => 'Mono',
            'currency' => Currency::USD,
            'balance'  => 0,
        ]);

        $monoEUR = Account::forceCreate([
            'name'     => 'Mono',
            'currency' => Currency::EUR,
            'balance'  => 0,
        ]);

        $monoFOPUAH = Account::forceCreate([
            'name'     => 'Mono FOP',
            'currency' => Currency::UAH,
            'balance'  => 0,
        ]);

        $monoFOPUSD = Account::forceCreate([
            'name'     => 'Mono FOP',
            'currency' => Currency::USD,
            'balance'  => 0,
        ]);

        $monoFOPEUR = Account::forceCreate([
            'name'     => 'Mono FOP',
            'currency' => Currency::EUR,
            'balance'  => 0,
        ]);

        $revolutEUR = Account::forceCreate([
            'name'     => 'Revolut',
            'currency' => Currency::EUR,
            'balance'  => 0,
        ]);

        $revolutUSD = Account::forceCreate([
            'name'     => 'Revolut',
            'currency' => Currency::USD,
            'balance'  => 0,
        ]);

        $wiseEUR = Account::forceCreate([
            'name'     => 'Wise',
            'currency' => Currency::EUR,
            'balance'  => 0,
        ]);

        $wiseFOPEUR = Account::forceCreate([
            'name'     => 'Wise FOP',
            'currency' => Currency::EUR,
            'balance'  => 0,
        ]);

        $accounts = collect([
            $monoBlack,
            $monoWhite,
            $monoUSD,
            $monoEUR,
            $monoFOPUAH,
            $monoFOPUSD,
            $monoFOPEUR,
            $revolutEUR,
            $revolutUSD,
            $wiseEUR,
            $wiseFOPEUR,
        ]);

        $accounts->each(fn(Account $account) => $account->jars()->create([
            'name'    => 'Default',
            'default' => true,
        ]));

        foreach (['Backup', 'Travel', 'Investing'] as $jarName) {
            $monoFOPUSD->jars()->create([
                'name' => $jarName,
            ]);
        }
    }
}
