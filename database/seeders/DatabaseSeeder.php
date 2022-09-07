<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(AccountSeeder::class);

        Account::factory()
               ->count(3)
               ->hasJars(1, [
                   'name'    => 'Default',
                   'default' => true,
               ])
               ->create();

        $this->call(PaymentSeeder::class);
    }
}
