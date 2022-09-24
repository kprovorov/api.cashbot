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
        Account::factory()
               ->count(3)
               ->hasJars(1, [
                   'name'    => 'Default',
                   'default' => true,
               ])
               ->hasJars(2)
               ->create();
    }
}
