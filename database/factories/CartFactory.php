<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Cart;
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
     */
    public function definition(): array
    {
        return [
            'discount' => 0,
            'currency' => Bazar::getCurrency(),
        ];
    }
}
