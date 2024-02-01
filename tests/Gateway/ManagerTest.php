<?php

namespace Cone\Bazar\Tests\Gateway;

use Cone\Bazar\Events\CheckoutFailed;
use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Gateway\CashDriver;
use Cone\Bazar\Gateway\Driver;
use Cone\Bazar\Gateway\Manager;
use Cone\Bazar\Gateway\TransferDriver;
use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Tests\TestCase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;

class ManagerTest extends TestCase
{
    protected Manager $manager;

    protected Cart $cart;

    protected Order $order;

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

    public function test_gateway_can_list_available_drivers_for_itemable_model(): void
    {
        $this->assertEquals(
            ['cash', 'transfer', 'custom-driver', 'failing-driver'],
            array_keys($this->manager->getAvailableDrivers($this->cart))
        );
    }

    public function test_gateway_has_cash_driver(): void
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

    public function test_gateway_has_transfer_driver(): void
    {
        $driver = $this->manager->driver('transfer');
        $this->assertInstanceOf(TransferDriver::class, $driver);
        $this->assertSame('Transfer', $driver->getName());

        $payment = $driver->pay($this->order, 1, ['completed_at' => Date::now()]);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order, null, ['completed_at' => Date::now()]);
        $this->assertTrue($this->order->refresh()->paid());
        $this->assertNull($driver->getTransactionUrl($payment));

        $refund = $driver->refund($this->order, 1, ['completed_at' => Date::now()]);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order, null, ['completed_at' => Date::now()]);
        $this->assertTrue($this->order->refresh()->refunded());
        $this->assertNull($driver->getTransactionUrl($payment));

        $driver->disable();
        $this->assertTrue($driver->disabled());
        $this->assertFalse($driver->available($this->order));
        $driver->enable();
        $this->assertTrue($driver->enabled());
        $this->assertTrue($driver->available($this->order));
    }

    public function test_gateway_can_have_custom_driver(): void
    {
        $driver = $this->manager->driver('custom-driver');
        $this->assertInstanceOf(CustomGatewayDriver::class, $driver);

        $payment = $driver->pay($this->order, 1, ['completed_at' => Date::now()]);
        $this->assertEquals(1, $payment->amount);
        $payment = $driver->pay($this->order, null, ['completed_at' => Date::now()]);
        $this->assertTrue($this->order->refresh()->paid());
        $this->assertSame('fake-url', $driver->getTransactionUrl($payment));

        $refund = $driver->refund($this->order, 1, ['completed_at' => Date::now()]);
        $this->assertEquals(1, $refund->amount);
        $refund = $driver->refund($this->order, null, ['completed_at' => Date::now()]);
        $this->assertTrue($this->order->refresh()->refunded());
        $this->assertSame('fake-url', $driver->getTransactionUrl($payment));
    }

    public function test_gateway_can_process_checkout(): void
    {
        Event::fake([CheckoutProcessed::class]);

        $this->manager->driver('cash')->handleCheckout($this->app['request'], $this->order);

        Event::assertDispatched(CheckoutProcessed::class);
    }

    public function test_gateway_handles_failed_checkout(): void
    {
        Event::fake([CheckoutFailed::class]);

        $this->manager->driver('failing-driver')->handleCheckout($this->app['request'], $this->order);

        Event::assertDispatched(CheckoutFailed::class);
    }
}

class CustomGatewayDriver extends Driver
{
    protected string $name = 'custom-driver';

    public function getTransactionUrl(Transaction $transaction): ?string
    {
        return 'fake-url';
    }
}

class FailingDriver extends Driver
{
    protected string $name = 'failing';

    public function checkout(Request $request, Order $order): Order
    {
        throw new Exception();
    }

    public function refund(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        throw new Exception();
    }
}
