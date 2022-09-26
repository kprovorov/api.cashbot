<?php

namespace App\TransferModule\Factories;

use App\TransferModule\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transfer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }

//    /**
//     * Indicate that the Transfer is ............
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
