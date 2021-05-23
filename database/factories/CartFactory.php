<?php

namespace Bazar\Database\Factories;

use Bazar\Bazar;
use Bazar\Models\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'discount' => 0,
            'currency' => Bazar::getCurrency(),
        ];
    }
}
