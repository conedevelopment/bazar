<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Support;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Support\Driver;
use Cone\Bazar\Tests\TestCase;

class DriverTest extends TestCase
{
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->order = Order::factory()->create();
    }

    public function test_driver_can_be_enabled(): void
    {
        $driver = new ConcreteDriver(['enabled' => true]);

        $this->assertTrue($driver->enabled());
        $this->assertFalse($driver->disabled());
    }

    public function test_driver_can_be_disabled(): void
    {
        $driver = new ConcreteDriver(['enabled' => false]);

        $this->assertTrue($driver->disabled());
        $this->assertFalse($driver->enabled());
    }

    public function test_driver_defaults_to_disabled(): void
    {
        $driver = new ConcreteDriver();

        $this->assertTrue($driver->disabled());
    }

    public function test_driver_can_be_enabled_manually(): void
    {
        $driver = new ConcreteDriver();

        $driver->enable();

        $this->assertTrue($driver->enabled());
    }

    public function test_driver_can_be_disabled_manually(): void
    {
        $driver = new ConcreteDriver(['enabled' => true]);

        $driver->disable();

        $this->assertTrue($driver->disabled());
    }

    public function test_driver_availability_depends_on_enabled_state(): void
    {
        $driver = new ConcreteDriver(['enabled' => true]);

        $this->assertTrue($driver->available($this->order));

        $driver->disable();

        $this->assertFalse($driver->available($this->order));
    }

    public function test_driver_has_name(): void
    {
        $driver = new ConcreteDriver();

        $this->assertSame('Concrete', $driver->getName());
    }

    public function test_driver_accepts_config(): void
    {
        $driver = new ConcreteDriver(['enabled' => true, 'custom' => 'value']);

        $this->assertTrue($driver->enabled());
    }
}

class ConcreteDriver extends Driver
{
    //
}
