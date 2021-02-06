<?php

namespace Bazar\Tests\Feature;

use Bazar\Contracts\Shippable;
use Bazar\Contracts\Shipping\Manager;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Shipping\Driver;
use Bazar\Shipping\LocalPickupDriver;
use Bazar\Tests\TestCase;

class ShippingDriverTest extends TestCase
{
    protected $manager, $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = OrderFactory::new()->create();

        $this->manager = $this->app->make(Manager::class);
        $this->manager->extend('custom-driver', function () {
            return new CustomShippingDriver();
        });
    }

    /** @test */
    public function it_can_list_enabled_drivers()
    {
        $this->manager->driver('local-pickup')->disable();
        $this->assertEquals(['custom-driver'], array_keys($this->manager->enabled()));
        $this->assertEquals(['local-pickup'], array_keys($this->manager->disabled()));

        $this->manager->driver('local-pickup')->enable();
        $this->assertEquals(['local-pickup', 'custom-driver'], array_keys($this->manager->enabled()));
        $this->assertEmpty(array_keys($this->manager->disabled()));
    }

    /** @test */
    public function it_can_check_if_the_given_driver_is_registered()
    {
        $this->assertTrue($this->manager->has('local-pickup'));
        $this->assertTrue($this->manager->has('custom-driver'));
        $this->assertFalse($this->manager->has('fake-driver'));
    }

    /** @test */
    public function it_has_local_pickup_driver()
    {
        $driver = $this->manager->driver('local-pickup');
        $this->assertInstanceOf(LocalPickupDriver::class, $driver);
        $this->assertSame('Local Pickup', $driver->name());
        $this->assertSame('local-pickup', $driver->id());

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
