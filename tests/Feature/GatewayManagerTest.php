<?php

namespace Cone\Bazar\Tests\Feature;

use Cone\Bazar\Gateway\CashDriver;
use Cone\Bazar\Gateway\Driver;
use Cone\Bazar\Gateway\Manager;
use Cone\Bazar\Gateway\TransferDriver;
use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Notifications\AdminNewOrder;
use Cone\Bazar\Notifications\CustomerNewOrder;
use Cone\Bazar\Tests\TestCase;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

class GatewayManagerTest extends TestCase
{
    protected $manager;

    protected $cart;

    protected $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();
        $this->cart->address()->save(Address::factory()->make());
        $this->cart->shipping->save();
        $this->cart->shipping->address()->save(Address::factory()->make());

        $this->order = Order::factory()->create();

        Product::factory()->count(3)->create()->each(function ($product) {
            $this->cart->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => mt_rand(1, 5),
                'tax' => 0,
                'price' => $product->price,
                'name' => $product->name,
            ]);
            $this->order->items()->create([
                'buyable_id' => $product->id,
                'buyable_type' => Product::class,
                'quantity' => mt_rand(1, 5),
                'tax' => 0,
                'price' => $product->price,
                'name' => $product->name,
            ]);
        });

        $this->manager = $this->app->make(Manager::class);
        $this->manager->extend('custom-driver', function () {
            return new CustomGatewayDriver();
        });
        $this->manager->extend('failing-driver', function () {
            return new FailingDriver();
        });
    }

    /** @test */
    public function it_can_list_available_drivers_for_itemable_model()
    {
        $this->assertEquals(
            ['cash', 'transfer', 'custom-driver', 'failing-driver'],
            array_keys($this->manager->getAvailableDrivers($this->cart))
        );
    }

    /** @test */
    public function it_has_cash_driver()
    {
        $driver = $this->manager->driver('cash');
        $this->assertInstanceOf(CashDriver::class, $driver);
        $this->assertSame('Cash', $driver->getName());

        $payment = $driver->pay($this->order, 1);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order);
        $this->assertTrue($this->order->refresh()->paid());
        $this->assertNull($driver->getTransactionUrl($payment));

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertTrue($this->order->refresh()->refunded());
        $this->assertNull($driver->getTransactionUrl($payment));

        $driver->disable();
        $this->assertTrue($driver->disabled());
        $this->assertFalse($driver->available($this->order));
        $driver->enable();
        $this->assertTrue($driver->enabled());
        $this->assertTrue($driver->available($this->order));
    }

    /** @test */
    public function it_has_transfer_driver()
    {
        $driver = $this->manager->driver('transfer');
        $this->assertInstanceOf(TransferDriver::class, $driver);
        $this->assertSame('Transfer', $driver->getName());

        $payment = $driver->pay($this->order, 1);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order);
        $this->assertTrue($this->order->refresh()->paid());
        $this->assertNull($driver->getTransactionUrl($payment));

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertTrue($this->order->refresh()->refunded());
        $this->assertNull($driver->getTransactionUrl($payment));

        $driver->disable();
        $this->assertTrue($driver->disabled());
        $this->assertFalse($driver->available($this->order));
        $driver->enable();
        $this->assertTrue($driver->enabled());
        $this->assertTrue($driver->available($this->order));
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
        $this->assertSame('fake-url', $driver->getTransactionUrl($payment));

        $refund = $driver->refund($this->order, 1);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order);
        $this->assertTrue($this->order->refresh()->refunded());
        $this->assertSame('fake-url', $driver->getTransactionUrl($payment));
    }

    /** @test */
    public function it_can_process_checkout()
    {
        Notification::fake();

        $order = $this->manager->driver('cash')->checkout($this->app['request'], $this->cart);

        $this->assertInstanceOf(Order::class, $order);

        // Notification::assertSentTo($this->admin, AdminNewOrder::class);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            CustomerNewOrder::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === $this->cart->address->email;
            }
        );
    }

    /** @test */
    public function it_handles_failed_checkout()
    {
        $order = $this->manager->driver('failing-driver')->checkout($this->app['request'], $this->cart);

        $this->assertSame('failed', $order->refresh()->status);
    }
}

class CustomGatewayDriver extends Driver
{
    public function getTransactionUrl(Transaction $transaction): ?string
    {
        return 'fake-url';
    }

    public function pay(Order $order, float $amount = null): Transaction
    {
        return $order->pay($amount, 'custom-driver');
    }

    public function refund(Order $order, float $amount = null): Transaction
    {
        return $order->refund($amount, 'custom-driver');
    }
}

class FailingDriver extends Driver
{
    public function pay(Order $order, float $amount = null): Transaction
    {
        throw new Exception;
    }

    public function refund(Order $order, float $amount = null): Transaction
    {
        throw new Exception;
    }
}
