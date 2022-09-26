<?php

namespace App\AccountModule\Factories;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\AccountModule\Models\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Str::ucfirst($this->faker->word()),
            'currency' => $this->faker->randomElement(Currency::cases()),
            'balance' => $this->faker->numberBetween(0, 1000000),
        ];
    }

//    /**
//     * Indicate that the Account is ............
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
