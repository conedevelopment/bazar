<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Shipping;
use Bazar\Support\Facades\Shipping as Manager;
use Faker\Generator as Faker;

$factory->define(Shipping::class, function (Faker $faker) {
    return [
        'tax' => mt_rand(0, 20),
        'cost' => mt_rand(0, 100),
        'driver' => Manager::getDefaultDriver(),
    ];
});
