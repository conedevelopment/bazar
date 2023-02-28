<?php

namespace Cone\Bazar\Tests\Feature;

use Cone\Bazar\Interfaces\Shippable;
use Cone\Bazar\Interfaces\Shipping\Manager;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Shipping\Driver;
use Cone\Bazar\Shipping\LocalPickupDriver;
use Cone\Bazar\Tests\TestCase;

class ShippingManagerTest extends TestCase
{
    protected $manager;

    protected $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = Order::factory()->create();

        $this->manager = $this->app->make(Manager::class);
        $this->manager->extend('custom-driver', function () {
            return new CustomShippingDriver();
        });
    }

    /** @test */
    public function it_can_list_available_drivers_for_itemable_model()
    {
        $this->assertEquals(
            ['local-pickup', 'custom-driver'],
            array_keys($this->manager->getAvailableDrivers($this->order))
        );
    }

    /** @test */
    public function it_has_local_pickup_driver()
    {
        $driver = $this->manager->driver('local-pickup');
        $this->assertInstanceOf(LocalPickupDriver::class, $driver);
        $this->assertSame('Local Pickup', $driver->getName());

        $this->assertEquals(0, $driver->calculate($this->order));

        $driver->disable();
        $this->assertTrue($driver->disabled());
        $this->assertFalse($driver->available($this->order));
        $driver->enable();
        $this->assertTrue($driver->enabled());
        $this->assertTrue($driver->available($this->order));
    }

    /** @test */
    public function it_can_have_custom_driver()
    {
        $driver = $this->manager->driver('custom-driver');
        $this->assertInstanceOf(CustomShippingDriver::class, $driver);

        $this->assertEquals(100, $driver->calculate($this->order));
    }
}

class CustomShippingDriver extends Driver
{
    public function calculate(Shippable $model): float
    {
        return 100;
    }
}
