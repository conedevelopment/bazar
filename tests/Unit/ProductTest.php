<?php

namespace Bazar\Tests\Unit;

use Bazar\Casts\Inventory;
use Bazar\Casts\Prices;
use Bazar\Contracts\Breadcrumbable;
use Bazar\Models\Cart;
use Bazar\Models\Category;
use Bazar\Models\Medium;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\Variant;
use Bazar\Tests\TestCase;
use Illuminate\Support\Str;

class ProductTest extends TestCase
{
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create([
            'properties' => [
                'Size' => ['XS', 'S', 'M', 'L'],
                'Material' => ['Gold', 'Silver'],
            ],
        ]);
    }

    /** @test */
    public function it_belongs_to_orders()
    {
        $order = Order::factory()->create();

        $this->product->orders()->attach($order, ['price' => 100, 'tax' => 0, 'quantity' => 3]);

        $this->assertTrue(
            $this->product->orders->pluck('id')->contains($order->id)
        );
    }

    /** @test */
    public function it_belongs_to_carts()
    {
        $cart = Cart::factory()->create();

        $this->product->carts()->attach($cart, ['price' => 100, 'tax' => 0, 'quantity' => 3]);

        $this->assertTrue(
            $this->product->carts->pluck('id')->contains($cart->id)
        );
    }

    /** @test */
    public function it_belongs_to_categories()
    {
        $category = Category::factory()->create();

        $this->product->categories()->attach($category);

        $this->assertTrue(
            $this->product->categories->pluck('id')->contains($category->id)
        );
    }

    /** @test */
    public function it_has_media()
    {
        $medium = Medium::factory()->create();

        $this->product->media()->attach($medium);

        $this->assertTrue($this->product->media->pluck('id')->contains($medium->id));
    }

    /** @test */
    public function it_has_variants()
    {
        $variant = $this->product->variants()->save(
            Variant::factory()->make([
                'variation' => ['Height' => 100, 'Width' => 100],
            ])
        );

        $this->assertTrue($this->product->variants->pluck('id')->contains($variant->id));
        $this->assertSame($variant->id, $this->product->toVariant(['Height' => 100, 'Width' => 100])->id);
        $this->assertNull($this->product->toVariant(['Height' => 100, 'Width' => 200]));
    }

    /** @test */
    public function it_manages_inventory()
    {
        $this->assertInstanceOf(Inventory::class, $this->product->inventory);

        $this->product->inventory['sku'] = 'fake';
        $this->product->save();
        $this->assertDatabaseHas('bazar_products', ['inventory->sku' => 'fake']);
    }

    /** @test */
    public function it_manages_prices()
    {
        $this->assertInstanceOf(Prices::class, $this->product->prices);
        $this->assertEquals($this->product->prices['usd']['default'], $this->product->price('default', 'usd'));
        $this->assertSame($this->product->price(), $this->product->price);
        $this->assertSame(
            Str::currency($this->product->prices['usd']['default'], 'usd'), $this->product->formattedPrice('default', 'usd')
        );
        $this->assertSame($this->product->formattedPrice(), $this->product->formattedPrice);
        $this->assertFalse($this->product->free());
        $this->assertTrue($this->product->onSale());

        $this->product->prices['usd']['sale'] = 10;
        $this->product->save();
        $this->assertDatabaseHas('bazar_products', ['prices->usd->sale' => 10]);
    }

    /** @test */
    public function it_is_breadcrumbable()
    {
        $this->assertInstanceOf(Breadcrumbable::class, $this->product);
        $this->assertSame($this->product->name, $this->product->toBreadcrumb($this->app['request']));
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->product->newQuery()->where(function ($q) {
                $q->where('bazar_products.name', 'like', 'test%')
                    ->orWhere('bazar_products.inventory->sku', 'like', 'test%');
            })->toSql(),
            $this->product->newQuery()->search('test')->toSql()
        );

        $this->assertSame(
            $this->product->newQuery()->whereHas('categories', function ($q) {
                $q->where('bazar_categories.id', 1);
            })->toSql(),
            $this->product->newQuery()->category(1)->toSql()
        );

        $this->assertSame(
            $this->product->newQuery()->where('bazar_products.inventory->quantity', '=', 0)->toSql(),
            $this->product->newQuery()->outOfStock()->toSql()
        );

        $this->assertSame(
            $this->product->newQuery()->where('bazar_products.inventory->quantity', '>', 0)->toSql(),
            $this->product->newQuery()->inStock()->toSql()
        );
    }
}
