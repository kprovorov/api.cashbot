<?php

namespace App\PaymentModule\Seeders;

use App\PaymentModule\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::factory()->create();
    }
}
