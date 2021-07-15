<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Facades\Shipping as Manager;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shipping::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'tax' => mt_rand(0, 20),
            'cost' => mt_rand(0, 100),
            'driver' => Manager::getDefaultDriver(),
        ];
    }
}
