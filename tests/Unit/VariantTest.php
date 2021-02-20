<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariantFactory;
use Bazar\Tests\TestCase;

class VariantTest extends TestCase
{
    protected $variant, $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = ProductFactory::new()->create();

        $this->variant = VariantFactory::new()->make();
        $this->variant->product()->associate($this->product);
        $this->variant->save();
    }

    /** @test */
    public function it_belongs_to_a_product()
    {
        $this->assertEquals($this->product->id, $this->variant->product_id);
    }

    /** @test */
    public function it_has_alias_attribute()
    {
        $variant = VariantFactory::new()->make(['alias' => 'Fake']);

        $this->assertSame('Fake', $variant->alias);

        $variant->alias = null;
        $variant->product()->associate($this->product);
        $variant->save();

        $this->assertSame("#{$variant->id}", $variant->alias);
    }

    /** @test */
    public function it_is_breadcrumbable()
    {
        $this->assertInstanceOf(Breadcrumbable::class, $this->variant);
        $this->assertSame($this->variant->alias, $this->variant->toBreadcrumb($this->app['request']));
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->variant->newQuery()->where('alias', 'like', 'test%')->toSql(),
            $this->variant->newQuery()->search('test')->toSql()
        );
    }
}
