<?php

namespace Bazar\Database\Factories;

use Bazar\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'tax' => mt_rand(10, 100),
            'name' => $this->faker->company(),
            'price' => mt_rand(10, 100),
            'quantity' => mt_rand(1, 10),
        ];
    }
}
