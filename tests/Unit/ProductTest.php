<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Category;
use Bazar\Models\Medium;
use Bazar\Models\Product;
use Bazar\Models\Variation;
use Bazar\Tests\TestCase;

class ProductTest extends TestCase
{
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = factory(Product::class)->create([
            'options' => ['size' => ['XS', 'S', 'M', 'L'], 'material' => ['Gold', 'Silver']],
        ]);
    }

    /** @test */
    public function a_product_belongs_to_categories()
    {
        $category = factory(Category::class)->create();

        $this->product->categories()->attach($category);

        $this->assertTrue(
            $this->product->categories->pluck('id')->contains($category->id)
        );
    }

    /** @test */
    public function a_product_has_media()
    {
        $medium = factory(Medium::class)->create();

        $this->product->media()->attach($medium);

        $this->assertTrue($this->product->media->pluck('id')->contains($medium->id));
    }

    /** @test */
    public function a_product_has_variations()
    {
        $variation = $this->product->variations()->save(
            factory(Variation::class)->make([
                'option' => ['size' => 'S', 'material' => 'Gold'],
            ])
        );

        $this->assertTrue($this->product->variations->pluck('id')->contains($variation->id));
    }
}
