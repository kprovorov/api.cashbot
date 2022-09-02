<?php

namespace Database\Factories;

use App\Enums\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(2),
            'amount'      => $this->faker->numberBetween(-100000, 100000),
            'currency'    => $this->faker->randomElement(Currency::cases()),
            'date'        => $this->faker->dateTimeBetween('now', '1 month'),
        ];
    }
}
