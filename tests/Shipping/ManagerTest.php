<?php

namespace Cone\Bazar\Tests\Shipping;

use Cone\Bazar\Interfaces\Shippable;
use Cone\Bazar\Interfaces\Shipping\Manager;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Shipping\Driver;
use Cone\Bazar\Shipping\LocalPickupDriver;
use Cone\Bazar\Tests\TestCase;

class ManagerTest extends TestCase
{
    protected Manager $manager;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->order = Order::factory()->create();

        $this->manager = $this->app->make(Manager::class);
        $this->manager->extend('custom-driver', function () {
            return new CustomShippingDriver(['enabled' => true]);
        });
    }

    public function test_shipping_can_list_available_drivers_for_checkoutable_model(): void
    {
        $this->assertEquals(
            ['local-pickup', 'custom-driver'],
            array_keys($this->manager->getAvailableDrivers($this->order))
        );
    }

    public function test_shipping_has_local_pickup_driver(): void
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

    public function test_shipping_can_have_custom_driver(): void
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
