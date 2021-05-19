<?php

namespace Bazar\Tests\Feature;

use Bazar\Contracts\Shippable;
use Bazar\Contracts\Shipping\Manager;
use Bazar\Models\Order;
use Bazar\Shipping\Driver;
use Bazar\Shipping\LocalPickupDriver;
use Bazar\Tests\TestCase;

class ShippingDriverTest extends TestCase
{
    protected $manager, $order;

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
    public function it_has_local_pickup_driver()
    {
        $driver = $this->manager->driver('local-pickup');
        $this->assertInstanceOf(LocalPickupDriver::class, $driver);
        $this->assertSame('Local Pickup', $driver->getName());

        $this->assertEquals(0, $driver->calculate($this->order));
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
