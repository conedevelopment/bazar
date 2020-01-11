<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Transaction;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'driver' => 'cash',
        'amount' => mt_rand(10, 1000) / 10,
        'type' => Arr::random(['refund', 'payment']),
    ];
});
