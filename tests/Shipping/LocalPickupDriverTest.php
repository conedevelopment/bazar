<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Shipping;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Shipping\LocalPickupDriver;
use Cone\Bazar\Tests\TestCase;

class LocalPickupDriverTest extends TestCase
{
    protected LocalPickupDriver $driver;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = new LocalPickupDriver(['enabled' => true]);

        $this->order = Order::factory()->create();

        Product::factory()->count(2)->create()->each(function ($product) {
            $this->order->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => 2,
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });
    }

    public function test_local_pickup_driver_has_correct_name(): void
    {
        $this->assertSame('Local Pickup', $this->driver->getName());
    }

    public function test_local_pickup_driver_calculates_zero_fee(): void
    {
        $fee = $this->driver->calculate($this->order);

        $this->assertEquals(0, $fee);
    }

    public function test_local_pickup_driver_is_available(): void
    {
        $this->assertTrue($this->driver->available($this->order));
    }

    public function test_local_pickup_driver_can_be_disabled(): void
    {
        $this->driver->disable();

        $this->assertTrue($this->driver->disabled());
        $this->assertFalse($this->driver->available($this->order));
    }

    public function test_local_pickup_driver_can_be_enabled(): void
    {
        $this->driver->disable();
        $this->driver->enable();

        $this->assertTrue($this->driver->enabled());
        $this->assertTrue($this->driver->available($this->order));
    }
}
