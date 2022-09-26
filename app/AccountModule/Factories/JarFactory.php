<?php

namespace App\AccountModule\Factories;

use App\AccountModule\Models\Jar;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class JarFactory extends Factory
{
    protected $model = Jar::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Str::title($this->faker->word()),
            'default' => false,
        ];
    }

//    /**
//     * Indicate that the Jar is ............
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
