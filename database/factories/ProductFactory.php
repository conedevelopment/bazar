<?php

namespace Bazar\Database\Factories;

use Bazar\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->sentences(3, true),
            'options' => ['Size' => ['XS', 'S', 'M', 'L']],
            'prices' => [
                'usd' => [
                    'default' => $price = mt_rand(10, 1000) / 10,
                    'sale' => round($price * 0.8, 1),
                ],
                'eur' => [
                    'default' => $price = mt_rand(10, 1000) / 10,
                    'sale' => round($price * 0.8, 1),
                ]
            ],
            'inventory' => [
                'files' => [],
                'sku' => Str::random(5),
                'quantity' => 20,
                'weight' => 200,
                'virtual' => false,
                'downloadable' => false,
                'length' => 200,
                'width' => 300,
                'height' => 400,
            ],
        ];
    }
}
