<?php

namespace Bazar\Tests\Feature;

use Bazar\Cart\CookieDriver;
use Bazar\Cart\Manager;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariantFactory;
use Bazar\Events\CartTouched;
use Bazar\Models\Cart;
use Bazar\Models\Shipping;
use Bazar\Services\Checkout;
use Bazar\Support\Facades\Cart as CartFacade;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CartDriverTest extends TestCase
{
    protected $cart, $product, $variant;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = $this->app->make(Manager::class);
        $this->product = ProductFactory::new()->create(['prices' => ['usd' => ['default' => 100]]]);
        $this->variant = $this->product->variants()->save(VariantFactory::new()->make([
            'option' => ['Size' => 'S'],
            'prices' => ['usd' => ['default' => 150]],
        ]));

        $this->cart->add($this->product, 2, ['option' => ['Size' => 'L']]);
        $this->cart->add($this->product, 1, ['option' => ['Size' => 'S']]);
    }

    /** @test */
    public function it_can_be_resolved_via_facade()
    {
        $this->mock(Manager::class, function ($mock) {
            return $mock->shouldReceive('model')
                ->once()
                ->andReturn('Fake Cart');
        });

        $this->assertSame('Fake Cart', CartFacade::model());
    }

    /** @test */
    public function it_can_be_retrieved_via_cookie_driver()
    {
        $this->assertInstanceOf(CookieDriver::class, $this->cart->driver('cookie'));
        $this->assertInstanceOf(Cart::class, $this->cart->driver('cookie')->model());
    }

    /** @test */
    public function it_can_add_products()
    {
        Event::fake(CartTouched::class);

        $this->cart->add($this->product, 2, ['option' => ['Size' => 'L']]);

        $this->assertEquals(5, $this->cart->count());
        $this->assertEquals(2, $this->cart->items()->count());
        $this->assertEquals(2, $this->cart->products()->count());

        $product = $this->cart->item($this->product, ['option' => ['Size' => 'L']]);
        $this->assertEquals(100, $product->price);
        $this->assertEquals(4, $product->quantity);

        $variant = $this->cart->item($this->product, ['option' => ['Size' => 'S']]);
        $this->assertEquals(150, $variant->price);
        $this->assertEquals(1, $variant->quantity);

        Event::assertDispatched(CartTouched::class, function ($event) {
            return $event->cart->id === $this->cart->model()->id;
        });
    }

    /** @test */
    public function it_can_remove_items()
    {
        $item = $this->cart->item($this->product, ['option' => ['Size' => 'L']]);
        $this->cart->remove($item);

        $this->assertEquals(1, $this->cart->count());
        $this->assertEquals(1, $this->cart->items()->count());
    }

    /** @test */
    public function it_can_be_updated()
    {
        $item = $this->cart->item($this->product, ['option' => ['Size' => 'L']]);
        $this->cart->update([$item->id => ['quantity' => 10]]);

        $this->assertEquals(11, $this->cart->count());
        $this->assertEquals(2, $this->cart->items()->count());
    }

    /** @test */
    public function it_can_be_emptied()
    {
        $this->assertTrue($this->cart->isNotEmpty());
        $this->cart->empty();
        $this->assertTrue($this->cart->isEmpty());
    }

    /** @test */
    public function it_has_shipping()
    {
        $this->assertInstanceOf(Shipping::class, $this->cart->shipping());
    }

    /** @test */
    public function it_has_total()
    {
        $this->assertEquals(
            $this->cart->model()->total, $this->cart->total()
        );
    }

    /** @test */
    public function it_has_tax()
    {
        $this->assertEquals(
            $this->cart->model()->tax, $this->cart->tax()
        );
    }

    /** @test */
    public function it_has_discount()
    {
        $this->assertEquals(
            $this->cart->model()->discount, $this->cart->discount()
        );
    }

    /** @test */
    public function it_can_checkout()
    {
        $this->assertInstanceOf(
            Checkout::class, $this->cart->checkout()
        );
    }
}
