<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Variation;
use Faker\Generator as Faker;

$factory->define(Variation::class, function (Faker $faker) {
    return [
        'option' => ['Size' => 'XS'],
        'prices' => ['usd' => ['normal' => mt_rand(10, 1000) / 10]],
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
