<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->sentences(2, true),
    ];
});
