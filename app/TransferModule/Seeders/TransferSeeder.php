<?php

namespace App\TransferModule\Seeders;

use App\TransferModule\Models\Transfer;
use Illuminate\Database\Seeder;

class TransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transfer::factory()->create();
    }
}
