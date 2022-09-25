<?php

namespace App\PaymentModule\Factories;

use App\Enums\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\PaymentModule\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(2),
            'amount' => $this->faker->numberBetween(-10, 10) * 1_000_000,
            'amount_converted' => $this->faker->numberBetween(-10, 10) * 1_000_000,
            'currency' => $this->faker->randomElement(Currency::cases()),
            'date' => $this->faker->dateTimeBetween('now', '1 month'),
            'hidden' => false,
        ];
    }

//    /**
//     * Indicate that the Payment is ............
//     *
//     * @return Factory
//     */
//    public function yourState(): Factory
//    {
//        return $this->state(function (array $attributes) {
//            return [
//                //
//            ];
//        });
//    }
}
