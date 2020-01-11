<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Medium;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

$factory->define(Medium::class, function (Faker $faker) {
    return [
        'disk' => 'public',
        'name' => $name = Str::random(5),
        'file_name' => "{$name}.{$faker->fileExtension}",
        'mime_type' => Arr::random(['image/jpg', 'application/pdf']),
        'size' => mt_rand(100, 2000),
        'width' => 1600,
        'height' => 1200,
    ];
});
