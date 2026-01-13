<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Listeners;

use Cone\Bazar\Events\PaymentCaptured;
use Cone\Bazar\Listeners\RefreshInventory;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class RefreshInventoryTest extends TestCase
{
    protected RefreshInventory $listener;

    protected Order $order;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = new RefreshInventory();
        $this->order = Order::factory()->create();
        $this->product = Product::factory()->create();

        $this->product->setQuantity(100);

        $this->order->items()->create([
            'buyable_id' => $this->product->id,
            'buyable_type' => Product::class,
            'quantity' => 10,
            'price' => $this->product->price,
            'name' => $this->product->name,
        ]);
    }

    public function test_listener_decrements_inventory_on_payment_captured(): void
    {
        $initialQuantity = $this->product->getQuantity();

        $event = new PaymentCaptured($this->order->transactions()->create([
            'type' => 'payment',
            'driver' => 'cash',
            'amount' => $this->order->getTotal(),
            'completed_at' => now(),
        ]));

        $this->listener->handle($event);

        $this->product->refresh();

        $this->assertEquals($initialQuantity - 10, $this->product->getQuantity());
    }

    public function test_listener_handles_payment_captured_event(): void
    {
        $transaction = $this->order->transactions()->create([
            'type' => 'payment',
            'driver' => 'cash',
            'amount' => $this->order->getTotal(),
            'completed_at' => now(),
        ]);

        $event = new PaymentCaptured($transaction);

        $this->listener->handle($event);

        $this->assertTrue(true);
    }
}
