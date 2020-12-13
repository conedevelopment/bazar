<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\CategoryFactory;
use Bazar\Database\Factories\MediumFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariationFactory;
use Bazar\Support\Bags\Inventory;
use Bazar\Support\Bags\Price;
use Bazar\Support\Bags\Prices;
use Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class ProductTest extends TestCase
{
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = ProductFactory::new()->create([
            'options' => [
                'Size' => ['XS', 'S', 'M', 'L'],
                'Material' => ['Gold', 'Silver'],
            ],
        ]);
    }

    /** @test */
    public function it_belongs_to_orders()
    {
        $order = OrderFactory::new()->create();

        $this->product->orders()->attach($order, ['price' => 100, 'tax' => 0, 'quantity' => 3]);

        $this->assertTrue(
            $this->product->orders->pluck('id')->contains($order->id)
        );
    }

    /** @test */
    public function it_belongs_to_carts()
    {
        $cart = CartFactory::new()->create();

        $this->product->carts()->attach($cart, ['price' => 100, 'tax' => 0, 'quantity' => 3]);

        $this->assertTrue(
            $this->product->carts->pluck('id')->contains($cart->id)
        );
    }

    /** @test */
    public function it_belongs_to_categories()
    {
        $category = CategoryFactory::new()->create();

        $this->product->categories()->attach($category);

        $this->assertTrue(
            $this->product->categories->pluck('id')->contains($category->id)
        );
    }

    /** @test */
    public function it_has_media()
    {
        $medium = MediumFactory::new()->create();

        $this->product->media()->attach($medium);

        $this->assertTrue($this->product->media->pluck('id')->contains($medium->id));
    }

    /** @test */
    public function it_has_variations()
    {
        $variation = $this->product->variations()->save(
            VariationFactory::new()->make([
                'option' => ['Size' => 'S', 'Material' => 'Gold'],
            ])
        );

        $this->assertTrue($this->product->variations->pluck('id')->contains($variation->id));
    }

    /** @test */
    public function it_manages_inventory()
    {
        $this->assertInstanceOf(Inventory::class, $this->product->inventory);

        $this->product->inventory->sku = 'fake';
        $this->product->save();
        $this->assertDatabaseHas('bazar_products', ['inventory->sku' => 'fake']);
    }

    /** @test */
    public function it_manages_prices()
    {
        $this->assertInstanceOf(Prices::class, $this->product->prices);
        $this->assertInstanceOf(Price::class, $this->product->prices->usd);
        $this->assertEquals($this->product->prices->usd->default, $this->product->price('default', 'usd'));
        $this->assertSame($this->product->price(), $this->product->price);
        $this->assertSame(
            Str::currency($this->product->prices->usd->default, 'usd'), $this->product->formattedPrice('default', 'usd')
        );
        $this->assertSame($this->product->formattedPrice(), $this->product->formattedPrice);
        $this->assertFalse($this->product->free());
        $this->assertTrue($this->product->onSale());

        $this->product->prices->usd->sale = 10;
        $this->product->save();
        $this->assertDatabaseHas('bazar_products', ['prices->usd->sale' => 10]);
    }

    /** @test */
    public function it_is_breadcrumbable()
    {
        $this->assertInstanceOf(Breadcrumbable::class, $this->product);
        $this->assertSame($this->product->name, $this->product->getBreadcrumbLabel($this->app['request']));
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->product->newQuery()->where(function ($q) {
                $q->where('name', 'like', 'test%')
                    ->orWhere('inventory->sku', 'like', 'test%');
            })->toSql(),
            $this->product->newQuery()->search('test')->toSql()
        );

        $this->assertSame(
            $this->product->newQuery()->whereHas('categories', function ($q) {
                $q->where('bazar_categories.id', 1);
            })->toSql(),
            $this->product->newQuery()->category(1)->toSql()
        );
    }
}
