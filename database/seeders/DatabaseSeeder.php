<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\User;
use Hash;
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
        User::forceCreate([
            'name'     => 'Kirill Provorov',
            'email'    => 'kirill@provorov.dev',
            'password' => Hash::make('secret'),
        ]);

        Account::factory()
               ->count(3)
               ->hasJars(1, [
                   'name'    => 'Default',
                   'default' => true,
               ])
               ->hasJars(2)
               ->create();

        $this->call(PaymentSeeder::class);
    }
}
