<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Cart;
use Faker\Generator as Faker;

$factory->define(Cart::class, function (Faker $faker) {
    return [
        'discount' => 0,
    ];
});
