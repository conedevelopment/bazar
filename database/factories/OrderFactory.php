<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'discount' => 0,
            'currency' => 'usd',
        ];
    }
}
