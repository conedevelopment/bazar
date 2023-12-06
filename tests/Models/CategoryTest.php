<?php

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Category;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class CategoryTest extends TestCase
{
    protected Category $category;

    public function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create();
    }

    public function test_category_belongs_to_products(): void
    {
        $product = Product::factory()->create();

        $this->category->products()->attach($product);

        $this->assertTrue(
            $this->category->products->pluck('id')->contains($product->id)
        );
    }
}
