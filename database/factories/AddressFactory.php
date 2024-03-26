<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'address_secondary' => null,
            'address' => $this->faker->streetAddress(),
            'alias' => null,
            'city' => $this->faker->city(),
            'company' => $this->faker->company(),
            'country' => $this->faker->countryCode(),
            'email' => $this->faker->email(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'postcode' => $this->faker->postcode(),
            'state' => $this->faker->state(),
            'tax_id' => Str::random(),
        ];
    }
}
