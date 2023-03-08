<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Product;
use Cone\Root\Models\Meta;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->unique()->company(),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentences(3, true),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(static function (Product $product): void {
            $product->setRelation('metaData', $product->metaData()->makeMany([
                ['key' => 'price_'.Bazar::getCurrency(), 'value' => mt_rand(10, 100)],
            ]));
        })->afterCreating(static function (Product $product): void {
            $product->metaData->each(static function (Meta $meta) use ($product) {
                $meta->setAttribute('metable_id', $product->getKey())->save();
            });
        });
    }
}
