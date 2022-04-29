<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Casts\Inventory;
use Cone\Bazar\Casts\Prices;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Category;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Variant;
use Cone\Bazar\Tests\TestCase;
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

        $item = Item::factory()->make([
            'price' => 100,
            'tax' => 0,
            'quantity' => 3,
            'name' => $this->product->name,
        ])->itemable()->associate($order);

        $this->product->items()->save($item);

        $this->assertTrue(
            $this->product->orders->pluck('id')->contains($order->id)
        );
    }

    /** @test */
    public function it_belongs_to_carts()
    {
        $cart = Cart::factory()->create();

        $item = Item::factory()->make([
            'price' => 100,
            'tax' => 0,
            'quantity' => 3,
            'name' => $this->product->name,
        ])->itemable()->associate($cart);

        $this->product->items()->save($item);

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
        $this->assertEquals($this->product->prices['usd']['default'], $this->product->getPrice('default', 'usd'));
        $this->assertSame($this->product->getPrice(), $this->product->price);
        $this->assertSame(
            Str::currency($this->product->prices['usd']['default'], 'usd'), $this->product->getFormattedPrice('default', 'usd')
        );
        $this->assertSame($this->product->getFormattedPrice(), $this->product->formattedPrice);
        $this->assertFalse($this->product->free());
        $this->assertTrue($this->product->onSale());

        $this->product->prices['usd']['sale'] = 10;
        $this->product->save();
        $this->assertDatabaseHas('bazar_products', ['prices->usd->sale' => 10]);
    }

    /** @test */
    public function it_has_query_scopes()
    {
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
