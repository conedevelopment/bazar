<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->word(),
            'slug' => Str::slug($name),
        ];
    }
}
