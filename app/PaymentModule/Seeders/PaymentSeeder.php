<?php

namespace App\PaymentModule\Seeders;

use App\PaymentModule\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Payment::factory()->create();
    }
}
