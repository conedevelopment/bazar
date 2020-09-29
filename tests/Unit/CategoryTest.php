<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Database\Factories\CategoryFactory;
use Bazar\Database\Factories\MediumFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Tests\TestCase;

class CategoryTest extends TestCase
{
    protected $category;

    public function setUp(): void
    {
        parent::setUp();

        $this->category = CategoryFactory::new()->create();
    }

    /** @test */
    public function it_belongs_to_products()
    {
        $product = ProductFactory::new()->create();

        $this->category->products()->attach($product);

        $this->assertTrue(
            $this->category->products->pluck('id')->contains($product->id)
        );
    }

    /** @test */
    public function it_has_media()
    {
        $media = MediumFactory::new()->create();

        $this->category->media()->attach($media);

        $this->assertTrue($this->category->media->pluck('id')->contains($media->id));
    }

    /** @test */
    public function it_is_breadcrumbable()
    {
        $this->assertInstanceOf(Breadcrumbable::class, $this->category);
        $this->assertSame($this->category->name, $this->category->getBreadcrumbLabel($this->app['request']));
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->category->newQuery()->search('test')->toSql(),
            $this->category->newQuery()->where('name', 'like', 'test%')->toSql(),
        );
    }
}
