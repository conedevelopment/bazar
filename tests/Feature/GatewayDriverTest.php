<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Exceptions\TransactionFailedException;
use Bazar\Gateway\CashDriver;
use Bazar\Gateway\Driver;
use Bazar\Gateway\Manager;
use Bazar\Gateway\TransferDriver;
use Bazar\Models\Order;
use Bazar\Models\Transaction;
use Bazar\Tests\TestCase;

class GatewayDriverTest extends TestCase
{
    protected $manager, $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->order = OrderFactory::new()->create();
        $products = ProductFactory::new()->count(3)->create()->mapWithKeys(function ($product) {
            return [$product->id => ['quantity' => mt_rand(1, 5), 'tax' => 0, 'price' => $product->price]];
        });
        $this->order->products()->attach($products->all());

        $this->manager = $this->app->make(Manager::class);
        $this->manager->extend('custom-driver', function () {
            return new CustomGatewayDriver;
        });
    }

    /** @test */
    public function it_can_list_enabled_drivers()
    {
        $this->manager->driver('cash')->disable();
        $this->assertEquals(['transfer', 'custom-driver'], array_keys($this->manager->methods()));

        $this->manager->driver('cash')->enable();
        $this->assertEquals(['cash', 'transfer', 'custom-driver'], array_keys($this->manager->methods()));
    }

    /** @test */
    public function it_can_check_if_the_diven_driver_is_registered()
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

        $payment = $driver->pay($this->order, 1);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order);
        $this->assertEquals($this->order->totalPaid() - 1, $payment->amount);
        $this->expectException(TransactionFailedException::class);
        $driver->pay($this->order);

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertEquals($this->order->totalRefunded(), $refund->amount);
        $this->expectException(TransactionFailedException::class);
        $driver->refund($this->order);
    }

    /** @test */
    public function it_has_transfer_driver()
    {
        $driver = $this->manager->driver('transfer');
        $this->assertInstanceOf(TransferDriver::class, $driver);

        $payment = $driver->pay($this->order, 1);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order);
        $this->assertEquals($this->order->totalPaid() - 1, $payment->amount);
        $this->expectException(TransactionFailedException::class);
        $driver->pay($this->order);

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertEquals($this->order->totalRefunded(), $refund->amount);
        $this->expectException(TransactionFailedException::class);
        $driver->refund($this->order);
    }

    /** @test */
    public function it_can_have_custom_driver()
    {
        $driver = $this->manager->driver('custom-driver');
        $this->assertInstanceOf(CustomGatewayDriver::class, $driver);

        $payment = $driver->pay($this->order, 1);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order);
        $this->assertEquals($this->order->totalPaid() - 1, $payment->amount);
        $this->expectException(TransactionFailedException::class);
        $driver->pay($this->order);

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertEquals($this->order->totalRefunded(), $refund->amount);
        $this->expectException(TransactionFailedException::class);
        $driver->refund($this->order);
    }
}

class CustomGatewayDriver Extends Driver
{
    public function pay(Order $order, float $amount = null): Transaction
    {
        $transaction = $this->transaction($order, 'payment', $amount);

        $transaction->save();

        return $transaction;
    }

    public function refund(Order $order, float $amount = null): Transaction
    {
        $transaction = $this->transaction($order, 'refund', $amount);

        $transaction->save();

        return $transaction;
    }
}
