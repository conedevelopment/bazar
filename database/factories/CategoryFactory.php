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
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->word(),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentences(2, true),
        ];
    }
}
