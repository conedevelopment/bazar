<?php

namespace Bazar\Tests\Feature;

use Bazar\Cart\Checkout;
use Bazar\Cart\CookieDriver;
use Bazar\Cart\Manager;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariantFactory;
use Bazar\Events\CartTouched;
use Bazar\Models\Cart;
use Bazar\Models\Shipping;
use Bazar\Support\Facades\Cart as CartFacade;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CartDriverTest extends TestCase
{
    protected $manager, $product, $variant;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = $this->app->make(Manager::class);
        $this->product = ProductFactory::new()->create(['prices' => ['usd' => ['default' => 100]]]);
        $this->variant = $this->product->variants()->save(VariantFactory::new()->make([
            'variation' => ['Size' => 'S'],
            'prices' => ['usd' => ['default' => 150]],
        ]));

        $this->manager->add($this->product, 2, ['Size' => 'L']);
        $this->manager->add($this->product, 1, ['Size' => 'S']);
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
    public function it_has_cart_driver()
    {
        $this->assertInstanceOf(CookieDriver::class, $this->manager->driver('cookie'));
        $this->assertInstanceOf(Cart::class, $this->manager->driver('cookie')->model());
    }

    /** @test */
    public function it_can_add_products()
    {
        Event::fake(CartTouched::class);

        $this->manager->add($this->product, 2, ['Size' => 'L']);

        $this->assertEquals(5, $this->manager->count());
        $this->assertEquals(2, $this->manager->items()->count());
        $this->assertEquals(2, $this->manager->products()->count());

        $product = $this->manager->item($this->product, ['Size' => 'L']);
        $this->assertEquals(100, $product->price);
        $this->assertEquals(4, $product->quantity);

        $variant = $this->manager->item($this->product, ['Size' => 'S']);
        $this->assertEquals(150, $variant->price);
        $this->assertEquals(1, $variant->quantity);

        Event::assertDispatched(CartTouched::class, function ($event) {
            return $event->cart->id === $this->manager->model()->id;
        });
    }

    /** @test */
    public function it_can_remove_items()
    {
        $item = $this->manager->item($this->product, ['Size' => 'L']);
        $this->manager->remove($item);

        $this->assertEquals(1, $this->manager->count());
        $this->assertEquals(1, $this->manager->items()->count());
    }

    /** @test */
    public function it_can_be_updated()
    {
        $item = $this->manager->item($this->product, ['Size' => 'L']);
        $this->manager->update([$item->id => ['quantity' => 10]]);

        $this->assertEquals(11, $this->manager->count());
        $this->assertEquals(2, $this->manager->items()->count());
    }

    /** @test */
    public function it_can_be_emptied()
    {
        $this->assertTrue($this->manager->isNotEmpty());
        $this->manager->empty();
        $this->assertTrue($this->manager->isEmpty());
    }

    /** @test */
    public function it_has_shipping()
    {
        $this->assertInstanceOf(Shipping::class, $this->manager->shipping());
    }

    /** @test */
    public function it_has_total()
    {
        $this->assertEquals(
            $this->manager->model()->total, $this->manager->total()
        );
    }

    /** @test */
    public function it_has_tax()
    {
        $this->assertEquals(
            $this->manager->model()->tax, $this->manager->tax()
        );
    }

    /** @test */
    public function it_has_discount()
    {
        $this->assertEquals(
            $this->manager->model()->discount, $this->manager->discount()
        );
    }

    /** @test */
    public function it_can_checkout()
    {
        $this->assertInstanceOf(
            Checkout::class, $this->manager->checkout()
        );
    }
}
