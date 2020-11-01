<?php

namespace Bazar\Tests\Unit;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\CategoryFactory;
use Bazar\Database\Factories\MediumFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariationFactory;
use Bazar\Models\Product;
use Bazar\Tests\TestCase;
use Illuminate\Support\Str;

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
                'option' => ['size' => 'S', 'material' => 'Gold'],
            ])
        );

        $this->assertTrue($this->product->variations->pluck('id')->contains($variation->id));
    }

    /** @test */
    public function it_is_stockable()
    {
        $this->assertEquals($this->product->prices['usd']['normal'], $this->product->price('normal', 'usd'));
        $this->assertSame($this->product->price(), $this->product->price);
        $this->assertSame(
            Str::currency($this->product->prices['usd']['normal'], 'usd'),
            $this->product->formattedPrice('normal', 'usd')
        );
        $this->assertSame($this->product->formattedPrice(), $this->product->formattedPrice);
        $this->assertFalse($this->product->free());
        $this->assertTrue($this->product->onSale());

        $this->assertSame(
            sprintf('%s mm', implode('x', $this->product->inventory('dimensions'))),
            $this->product->formattedDimensions('x')
        );
        $this->assertNull((new Product)->formattedDimensions());

        $this->assertSame(sprintf('%s g', $this->product->inventory('weight')), $this->product->formattedWeight('x'));
        $this->assertNull((new Product)->formattedWeight());

        $this->assertTrue($this->product->tracksQuantity());
        $this->assertTrue($this->product->available());
        $this->assertFalse($this->product->available(600));
        $this->assertSame(20, $this->product->inventory('quantity'));
        $this->product->incrementQuantity(10);
        $this->assertSame(30, $this->product->inventory('quantity'));
        $this->product->decrementQuantity(6);
        $this->assertSame(24, $this->product->inventory('quantity'));

        $this->assertFalse($this->product->virtual());
        $this->assertFalse($this->product->downloadable());
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
            $this->product->newQuery()->search('test')->toSql(),
            $this->product->newQuery()->where(function ($q) {
                $q->where('name', 'like', 'test%')
                    ->orWhere('inventory->sku', 'like', 'test%');
            })->toSql()
        );

        $this->assertSame(
            $this->product->newQuery()->category(1)->toSql(),
            $this->product->newQuery()->whereHas('categories', function ($q) {
                $q->where('categories.id', 1);
            })->toSql()
        );
    }
}
