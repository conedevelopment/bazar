<?php

namespace Bazar\Database\Factories;

use Bazar\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'country' => $this->faker->countryCode,
            'city' => $this->faker->city,
            'address' => $this->faker->streetAddress,
            'address_secondary' => null,
            'postcode' => $this->faker->postcode,
            'company' => $this->faker->company,
            'state' => $this->faker->state,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'alias' => null,
            'custom' => ['vat' => null],
        ];
    }
}
