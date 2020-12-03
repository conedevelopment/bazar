<?php

namespace Bazar\Database\Factories;

use Bazar\Models\Meta;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MetaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Meta::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'key' => Str::slug($this->faker->name),
            'value' => mt_rand(10, 1000),
        ];
    }
}
