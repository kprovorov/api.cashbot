<?php

namespace App\AccountModule\Seeders;

use App\AccountModule\Models\Jar;
use Illuminate\Database\Seeder;

class JarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Jar::factory()->create();
    }
}
