<?php

namespace Database\Seeders;

use App\AccountModule\Seeders\AccountSeeder;
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
        $this->call(AdminSeeder::class);
        $this->call(AccountSeeder::class);
    }
}
