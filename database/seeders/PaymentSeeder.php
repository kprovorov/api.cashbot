<?php

namespace Database\Seeders;

use App\Models\Jar;
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
        Jar::each(function (Jar $jar) {
            Payment::factory()->count(30)->create([
                'jar_id' => $jar->id,
                'currency' => $jar->account->currency,
            ]);
        });
    }
}
