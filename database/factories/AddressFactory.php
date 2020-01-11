<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Bazar\Models\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'country' => $faker->countryCode,
        'city' => $faker->city,
        'address' => $faker->streetAddress,
        'address_secondary' => null,
        'postcode' => $faker->postcode,
        'company' => $faker->company,
        'state' => $faker->state,
        'phone' => $faker->phoneNumber,
        'email' => $faker->email,
        'alias' => null,
        'custom' => [
            'vat' => null,
        ],
    ];
});
