<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'description' => $faker->sentences(3, true),
        'options' => ['Size' => ['XS', 'S', 'M', 'L']],
        'prices' => ['usd' => [
            'normal' => $price = mt_rand(10, 1000) / 10,
            'sale' => round($price * 0.8, 1),
        ]],
        'inventory' => [
            'files' => [],
            'sku' => $faker->swiftBicNumber,
            'quantity' => 20,
            'weight' => 200,
            'virtual' => false,
            'downloadable' => false,
            'dimensions' => ['length' => 200, 'width' => 300, 'height' => 400],
        ],
    ];
});
