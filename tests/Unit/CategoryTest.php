<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Category;
use Bazar\Models\Medium;
use Bazar\Models\Product;
use Bazar\Tests\TestCase;

class CategoryTest extends TestCase
{
    protected $category;

    public function setUp(): void
    {
        parent::setUp();

        $this->category = factory(Category::class)->create();
    }

    /** @test */
    public function a_category_belongs_to_products()
    {
        $product = factory(Product::class)->create();

        $this->category->products()->attach($product);

        $this->assertTrue(
            $this->category->products->pluck('id')->contains($product->id)
        );
    }

    /** @test */
    public function a_category_has_media()
    {
        $media = factory(Medium::class)->create();

        $this->category->media()->attach($media);

        $this->assertTrue($this->category->media->pluck('id')->contains($media->id));
    }
}
