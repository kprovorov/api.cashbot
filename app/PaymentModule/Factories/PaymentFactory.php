<?php

namespace App\PaymentModule\Factories;

use App\Enums\Currency;
use App\Enums\RepeatUnit;
use App\PaymentModule\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\PaymentModule\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(2),
            'amount' => $this->faker->numberBetween(1, 10) * 1_000_000,
            'currency' => $this->faker->randomElement(Currency::cases()),
            'date' => $this->faker->dateTimeBetween('now', '1 month'),
            'group' => $this->faker->uuid(),
            'auto_apply' => false,
            'repeat_unit' => RepeatUnit::NONE,
            'repeat_interval' => 1,
        ];
    }

    /**
     * Indicate that the Payment is repeatable
     */
    public function repeatable(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'repeat_unit' => $this->faker->randomElement([
                    RepeatUnit::DAY,
                    RepeatUnit::WEEK,
                    RepeatUnit::MONTH,
                    RepeatUnit::QUARTER,
                    RepeatUnit::YEAR,
                ]),
            ];
        });
    }
}
