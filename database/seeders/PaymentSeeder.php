<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Jar;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            Payment::factory()->count(3)->create([
                'jar_id'   => $jar->id,
                'currency' => $jar->account->currency,
            ]);
        });
    }
}
