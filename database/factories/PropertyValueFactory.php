<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Models\PropertyValue;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyValue::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'value' => $this->faker->word(),
        ];
    }
}
