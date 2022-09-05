<?php

namespace Database\Seeders;

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
        Account::factory()->hasJars(1, [
            'default' => true,
        ])->hasJars(2)->count(3)->create();
    }
}
