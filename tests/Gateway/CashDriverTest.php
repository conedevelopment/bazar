<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Gateway;

use Cone\Bazar\Gateway\CashDriver;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Tests\TestCase;

class CashDriverTest extends TestCase
{
    protected CashDriver $driver;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = new CashDriver(['enabled' => true]);

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

    public function test_cash_driver_has_correct_name(): void
    {
        $this->assertSame('Cash', $this->driver->getName());
    }

    public function test_cash_driver_can_pay(): void
    {
        $transaction = $this->driver->pay($this->order, 100);

        $this->assertEquals(100, $transaction->amount);
        $this->assertTrue($transaction->completed());
    }

    public function test_cash_driver_can_refund(): void
    {
        $this->driver->pay($this->order);

        $transaction = $this->driver->refund($this->order, 50);

        $this->assertEquals(50, $transaction->amount);
        $this->assertTrue($transaction->completed());
    }

    public function test_cash_driver_marks_transactions_as_completed(): void
    {
        $transaction = $this->driver->pay($this->order);

        $this->assertNotNull($transaction->completed_at);
    }
}
