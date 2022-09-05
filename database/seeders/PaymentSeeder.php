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
        Jar::all()->each(function (Jar $jar) {
            Payment::factory()->count(5)->create([
                'jar_id'   => $jar->id,
                'currency' => $jar->account->currency,
            ]);
        });
    }
}
