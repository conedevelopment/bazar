<?php

namespace Cone\Bazar\Tests\Feature;

use Cone\Bazar\Cart\CookieDriver;
use Cone\Bazar\Cart\Manager;
use Cone\Bazar\Cart\SessionDriver;
use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Property;
use Cone\Bazar\Models\PropertyValue;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Models\Variant;
use Cone\Bazar\Support\Facades\Cart as CartFacade;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CartManagerTest extends TestCase
{
    protected Manager $manager;

    protected Product $product;

    protected Variant $variant;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = $this->app->make(Manager::class);

        $this->product = Product::factory()->create();

        $this->variant = Variant::factory()->for($this->product, 'product')->create();

        $property = Property::factory()->create(['name' => 'Size', 'slug' => 'size']);
        $property->values()->saveMany([
            PropertyValue::factory()->make(['value' => 'L']),
            PropertyValue::factory()->make(['value' => 'S']),
        ]);

        $this->product->propertyValues()->attach($property->values);
        $this->variant->propertyValues()->attach($property->values->where('value', 'S'));

        $this->manager->addItem($this->product, 2, ['size' => 'L']);
        $this->manager->addItem($this->product, 1, ['size' => 'S']);
    }

    /** @test */
    public function a_manager_can_be_resolved_via_facade()
    {
        $this->mock(Manager::class, function ($mock) {
            return $mock->shouldReceive('getModel')
                ->once()
                ->andReturn('Fake Cart');
        });

        $this->assertSame('Fake Cart', CartFacade::getModel());
    }

    /** @test */
    public function a_manager_has_cookie_driver()
    {
        $this->assertInstanceOf(CookieDriver::class, $this->manager->driver('cookie'));
        $this->assertInstanceOf(Cart::class, $this->manager->driver('cookie')->getModel());
    }

    /** @test */
    public function a_manager_has_session_driver()
    {
        $this->session([]);

        $this->app['request']->setLaravelSession($this->app['session']);

        $this->assertInstanceOf(SessionDriver::class, $this->manager->driver('session'));
        $this->assertInstanceOf(Cart::class, $this->manager->driver('session')->getModel());
    }

    /** @test */
    public function a_manager_can_add_products()
    {
        $this->manager->addItem($this->product, 2, ['size' => 'L']);

        $this->assertEquals(5, $this->manager->count());
        $this->assertEquals(2, $this->manager->getItems()->count());

        $product = $this->manager->getModel()->findItem([
            'properties' => ['size' => 'L'],
        ]);
        $this->assertEquals($this->product->price, $product->price);
        $this->assertEquals(4, $product->quantity);

        $variant = $this->manager->getModel()->findItem([
            'properties' => ['size' => 'S'],
        ]);

        $this->assertEquals($this->variant->price, $variant->price);
        $this->assertEquals(1, $variant->quantity);
    }

    /** @test */
    public function a_manager_can_remove_items()
    {
        $item = $this->manager->getModel()->findItem([
            'properties' => ['size' => 'L'],
        ]);
        $this->manager->removeItem($item->id);

        $this->assertEquals(1, $this->manager->count());
        $this->assertEquals(1, $this->manager->getItems()->count());

        $this->manager->removeItems($this->manager->getItems()->pluck('id')->toArray());
        $this->assertEquals(0, $this->manager->count());
        $this->assertEquals(0, $this->manager->getItems()->count());
    }

    /** @test */
    public function a_manager_can_update_items()
    {
        $item = $this->manager->getModel()->findItem([
            'properties' => ['size' => 'L'],
        ]);
        $this->manager->updateItem($item->id, ['quantity' => 10]);

        $this->assertEquals(11, $this->manager->count());
        $this->assertEquals(2, $this->manager->getItems()->count());

        $data = $this->manager->getItems()->mapWithKeys(function ($item) {
            return [$item->id => ['quantity' => 3]];
        })->toArray();

        $this->manager->updateItems($data);
        $this->assertEquals(6, $this->manager->count());
        $this->assertEquals(2, $this->manager->getItems()->count());
    }

    /** @test */
    public function a_manager_can_be_emptied()
    {
        $this->assertTrue($this->manager->isNotEmpty());
        $this->manager->empty();
        $this->assertTrue($this->manager->isEmpty());
    }

    /** @test */
    public function a_manager_has_shipping()
    {
        $this->assertInstanceOf(Shipping::class, $this->manager->getShipping());
    }

    /** @test */
    public function a_manager_updates_shipping()
    {
        $this->manager->updateShipping(['first_name' => 'Test'], 'local-pickup');

        $this->assertSame('Test', $this->manager->getShipping()->address->first_name);
    }

    /** @test */
    public function a_manager_has_billing()
    {
        $this->assertInstanceOf(Address::class, $this->manager->getBilling());
    }

    /** @test */
    public function a_manager_updates_billing()
    {
        $this->manager->updateBilling(['first_name' => 'Test']);

        $this->assertSame('Test', $this->manager->getBilling()->first_name);
    }

    /** @test */
    public function a_manager_has_getTotal()
    {
        $this->assertEquals(
            $this->manager->getModel()->total, $this->manager->getTotal()
        );
    }

    /** @test */
    public function a_manager_has_calculates_tax()
    {
        $this->assertEquals(
            $this->manager->getModel()->tax, $this->manager->calculateTax()
        );
    }

    /** @test */
    public function a_manager_has_calculates_discount()
    {
        $this->assertEquals(
            $this->manager->getModel()->discount, $this->manager->calculateDiscount()
        );
    }

    /** @test */
    public function a_manager_can_checkout()
    {
        Event::fake([CheckoutProcessed::class]);

        $this->manager->checkout('cash');

        Event::assertDispatched(CheckoutProcessed::class);
    }

    /** @test */
    public function a_manager_can_sync_items()
    {
        $this->assertEquals($this->product->price * 2 + $this->variant->price, $this->manager->getTotal());
    }
}
