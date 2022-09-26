<?php

namespace App\AccountModule\Seeders;

use App\AccountModule\Models\Account;
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
               ->count(10)
               ->hasJars(1, [
                   'name' => 'Default',
                   'default' => true,
               ])
               ->hasJars(2)
               ->create();
    }
}
