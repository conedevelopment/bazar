<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Models\Category;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class CategoryTest extends TestCase
{
    protected $category;

    public function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create();
    }

    /** @test */
    public function it_belongs_to_products()
    {
        $product = Product::factory()->create();

        $this->category->products()->attach($product);

        $this->assertTrue(
            $this->category->products->pluck('id')->contains($product->id)
        );
    }
}
