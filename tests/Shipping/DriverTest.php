<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Shipping;

use Cone\Bazar\Interfaces\Shippable;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Shipping\Driver;
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
        $driver = new ConcreteShippingDriver(['enabled' => true]);

        $this->assertTrue($driver->enabled());
    }

    public function test_driver_can_be_disabled(): void
    {
        $driver = new ConcreteShippingDriver(['enabled' => false]);

        $this->assertTrue($driver->disabled());
    }

    public function test_driver_availability_depends_on_enabled_state(): void
    {
        $driver = new ConcreteShippingDriver(['enabled' => true]);

        $this->assertTrue($driver->available($this->order));

        $driver->disable();

        $this->assertFalse($driver->available($this->order));
    }

    public function test_driver_has_name(): void
    {
        $driver = new ConcreteShippingDriver();

        $this->assertSame('Concrete Shipping', $driver->getName());
    }

    public function test_driver_can_calculate_shipping_fee(): void
    {
        $driver = new ConcreteShippingDriver();

        $fee = $driver->calculate($this->order);

        $this->assertEquals(10, $fee);
    }
}

class ConcreteShippingDriver extends Driver
{
    protected string $name = 'concrete-shipping';

    public function calculate(Shippable $model): float
    {
        return 10;
    }
}
