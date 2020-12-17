<?php

namespace Bazar\Database\Factories;

use Bazar\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VariantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Variant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'option' => ['Size' => 'XS'],
            'prices' => ['usd' => ['default' => mt_rand(10, 1000) / 10]],
            'inventory' => [
                'files' => [],
                'sku' => Str::random(5),
                'quantity' => 20,
                'weight' => 200,
                'virtual' => false,
                'downloadable' => false,
                'dimensions' => ['length' => 200, 'width' => 300, 'height' => 400],
            ],
        ];
    }
}
