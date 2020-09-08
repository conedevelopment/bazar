<?php

namespace Bazar\Tests\Feature;

use Bazar\Cart\CookieDriver;
use Bazar\Cart\Manager;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariationFactory;
use Bazar\Events\CartTouched;
use Bazar\Models\Cart;
use Bazar\Models\Shipping;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CartDriverTest extends TestCase
{
    protected $cart, $product, $variation;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = $this->app->make(Manager::class);
        $this->product = ProductFactory::new()->create(['prices' => ['usd' => ['normal' => 100]]]);
        $this->variation = $this->product->variations()->save(VariationFactory::new()->make([
            'option' => ['Size' => 'S'],
            'prices' => ['usd' => ['normal' => 150]],
        ]));

        $this->cart->add($this->product, 2, ['option' => ['Size' => 'L']]);
        $this->cart->add($this->product, 1, ['option' => ['Size' => 'S']]);
    }

    /** @test */
    public function a_cart_can_be_resolved_via_cookie_driver()
    {
        $this->assertInstanceOf(CookieDriver::class, $this->cart->driver('cookie'));
        $this->assertInstanceOf(Cart::class, $this->cart->driver('cookie')->model());
    }

    /** @test */
    public function a_product_can_be_added_to_cart()
    {
        Event::fake(CartTouched::class);

        $this->cart->add($this->product, 2, ['option' => ['Size' => 'L']]);

        $this->assertEquals(5, $this->cart->count());
        $this->assertEquals(2, $this->cart->items()->count());

        $product = $this->cart->item($this->product, ['option' => ['Size' => 'L']]);
        $this->assertEquals(100, $product->price);
        $this->assertEquals(4, $product->quantity);

        $variation = $this->cart->item($this->product, ['option' => ['Size' => 'S']]);
        $this->assertEquals(150, $variation->price);
        $this->assertEquals(1, $variation->quantity);

        Event::assertDispatched(CartTouched::class, function ($event) {
            return $event->cart->id === $this->cart->model()->id;
        });
    }

    /** @test */
    public function an_item_can_be_removed_from_cart()
    {
        $item = $this->cart->item($this->product, ['option' => ['Size' => 'L']]);
        $this->cart->remove($item);

        $this->assertEquals(1, $this->cart->count());
        $this->assertEquals(1, $this->cart->items()->count());
    }

    /** @test */
    public function a_cart_can_be_updated()
    {
        $item = $this->cart->item($this->product, ['option' => ['Size' => 'L']]);
        $this->cart->update([$item->id => ['quantity' => 10]]);

        $this->assertEquals(11, $this->cart->count());
        $this->assertEquals(2, $this->cart->items()->count());
    }

    /** @test */
    public function a_cart_can_be_emptied()
    {
        $this->assertTrue($this->cart->isNotEmpty());
        $this->cart->empty();
        $this->assertTrue($this->cart->isEmpty());
    }

    /** @test */
    public function a_cart_has_shipping()
    {
        $this->assertInstanceOf(Shipping::class, $this->cart->shipping());
    }

    /** @test */
    public function a_cart_has_total()
    {
        $this->assertEquals(
            $this->cart->model()->total, $this->cart->total()
        );
    }

    /** @test */
    public function a_cart_has_tax()
    {
        $this->assertEquals(
            $this->cart->model()->tax, $this->cart->tax()
        );
    }

    /** @test */
    public function a_cart_has_discount()
    {
        $this->assertEquals(
            $this->cart->model()->discount, $this->cart->discount()
        );
    }
}
