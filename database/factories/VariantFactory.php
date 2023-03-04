<?php

namespace Cone\Bazar\Database\Factories;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Variant;
use Cone\Root\Models\Meta;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Variant::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(static function (Variant $variant): void {
            $variant->setRelation('metas', $variant->metas()->makeMany([
                ['key' => 'price_'.Bazar::getCurrency(), 'value' => mt_rand(10, 100)],
            ]));
        })->afterCreating(static function (Variant $variant): void {
            $variant->metas->each(static function (Meta $meta) use ($variant) {
                $meta->setAttribute('metable_id', $variant->getKey())->save();
            });
        });
    }
}
