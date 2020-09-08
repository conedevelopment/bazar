<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\CategoryFactory;
use Bazar\Database\Factories\MediumFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariationFactory;
use Bazar\Tests\TestCase;

class ProductTest extends TestCase
{
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = ProductFactory::new()->create([
            'options' => ['size' => ['XS', 'S', 'M', 'L'], 'material' => ['Gold', 'Silver']],
        ]);
    }

    /** @test */
    public function a_product_belongs_to_categories()
    {
        $category = CategoryFactory::new()->create();

        $this->product->categories()->attach($category);

        $this->assertTrue(
            $this->product->categories->pluck('id')->contains($category->id)
        );
    }

    /** @test */
    public function a_product_has_media()
    {
        $medium = MediumFactory::new()->create();

        $this->product->media()->attach($medium);

        $this->assertTrue($this->product->media->pluck('id')->contains($medium->id));
    }

    /** @test */
    public function a_product_has_variations()
    {
        $variation = $this->product->variations()->save(
            VariationFactory::new()->make([
                'option' => ['size' => 'S', 'material' => 'Gold'],
            ])
        );

        $this->assertTrue($this->product->variations->pluck('id')->contains($variation->id));
    }
}
