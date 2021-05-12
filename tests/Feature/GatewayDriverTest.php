<?php

namespace Bazar\Tests\Feature;

use Bazar\Gateway\CashDriver;
use Bazar\Gateway\Driver;
use Bazar\Gateway\Manager;
use Bazar\Gateway\TransferDriver;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\Transaction;
use Bazar\Tests\TestCase;

class GatewayDriverTest extends TestCase
{
    protected $manager, $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = Order::factory()->create();
        $products = Product::factory()->count(3)->create()->mapWithKeys(function ($product) {
            return [$product->id => ['quantity' => mt_rand(1, 5), 'tax' => 0, 'price' => $product->price]];
        });
        $this->order->products()->attach($products->all());

        $this->manager = $this->app->make(Manager::class);
        $this->manager->extend('custom-driver', function () {
            return new CustomGatewayDriver();
        });
    }

    /** @test */
    public function it_can_list_enabled_drivers()
    {
        $this->manager->driver('cash')->disable();
        $this->assertEquals(['transfer', 'custom-driver'], array_keys($this->manager->enabled()));
        $this->assertEquals(['cash'], array_keys($this->manager->disabled()));

        $this->manager->driver('cash')->enable();
        $this->assertEquals(['cash', 'transfer', 'custom-driver'], array_keys($this->manager->enabled()));
        $this->assertEmpty(array_keys($this->manager->disabled()));
    }

    /** @test */
    public function it_can_check_if_the_given_driver_is_registered()
    {
        $this->assertTrue($this->manager->has('cash'));
        $this->assertTrue($this->manager->has('custom-driver'));
        $this->assertFalse($this->manager->has('fake-driver'));
    }

    /** @test */
    public function it_has_cash_driver()
    {
        $driver = $this->manager->driver('cash');
        $this->assertInstanceOf(CashDriver::class, $driver);
        $this->assertSame('Cash', $driver->name());
        $this->assertSame('cash', $driver->id());

        $payment = $driver->pay($this->order, 1);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order);
        $this->assertTrue($this->order->refresh()->paid());
        $this->assertNull($driver->transactionUrl($payment));

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertTrue($this->order->refresh()->refunded());
        $this->assertNull($driver->transactionUrl($payment));
    }

    /** @test */
    public function it_has_transfer_driver()
    {
        $driver = $this->manager->driver('transfer');
        $this->assertInstanceOf(TransferDriver::class, $driver);
        $this->assertSame('Transfer', $driver->name());
        $this->assertSame('transfer', $driver->id());

        $payment = $driver->pay($this->order, 1);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order);
        $this->assertTrue($this->order->refresh()->paid());
        $this->assertNull($driver->transactionUrl($payment));

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertTrue($this->order->refresh()->refunded());
        $this->assertNull($driver->transactionUrl($payment));
    }

    /** @test */
    public function it_can_have_custom_driver()
    {
        $driver = $this->manager->driver('custom-driver');
        $this->assertInstanceOf(CustomGatewayDriver::class, $driver);

        $payment = $driver->pay($this->order, 1);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order);
        $this->assertTrue($this->order->refresh()->paid());
        $this->assertSame('fake-url', $driver->transactionUrl($payment));

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertTrue($this->order->refresh()->refunded());
        $this->assertSame('fake-url', $driver->transactionUrl($payment));
    }

    /** @test */
    public function it_can_checkout_using_a_driver()
    {
        $driver = $this->manager->driver('cash');

        $cart = Cart::factory()->create();

        $order = $driver->checkout($this->app['request'], $cart);

        $this->assertInstanceOf(Order::class, $order);
    }
}

class CustomGatewayDriver Extends Driver
{
    public function transactionUrl(Transaction $transaction): ?string
    {
        return 'fake-url';
    }

    public function pay(Order $order, float $amount = null): Transaction
    {
        return $order->pay($amount, $this->id());
    }

    public function refund(Order $order, float $amount = null): Transaction
    {
        return $order->refund($amount, $this->id());
    }
}
