<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'slug' => Str::random(),
            'description' => $this->faker->sentences(2, true),
        ];
    }
}
