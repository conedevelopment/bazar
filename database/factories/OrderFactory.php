<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'discount' => 0,
        'currency' => 'usd',
    ];
});
