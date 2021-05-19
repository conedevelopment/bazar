<?php

namespace Bazar\Tests\Feature;

use Bazar\Events\CheckoutFailed;
use Bazar\Events\CheckoutProcessing;
use Bazar\Gateway\Driver;
use Bazar\Models\Address;
use Bazar\Models\Cart;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\Transaction;
use Bazar\Notifications\AdminNewOrder;
use Bazar\Notifications\CustomerNewOrder;
use Bazar\Support\Facades\Gateway;
use Bazar\Tests\TestCase;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

class CheckoutTest extends TestCase
{
    protected $cart;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();
        $this->cart->address()->save(Address::factory()->make());
        $this->cart->shipping->save();
        $this->cart->shipping->address()->save(Address::factory()->make());

        $this->cart->products()->attach(
            Product::factory()->count(3)->create()->mapWithKeys(function ($product) {
                [$quantity, $tax, $price] = [mt_rand(1, 5), 0, $product->price];

                return [$product->id => compact('price', 'tax', 'quantity')];
            })->all()
        );
    }

    /** @test */
    public function it_can_process_checkout()
    {
        Event::fake(CheckoutProcessing::class);
        Notification::fake();

        $order = Gateway::driver('cash')->checkout($this->app['request'], $this->cart);

        $this->assertInstanceOf(Order::class, $order);

        Event::assertDispatched(CheckoutProcessing::class);

        Notification::assertSentTo($this->admin, AdminNewOrder::class);

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
        Gateway::extend('failing', function () {
            return new FailingDriver();
        });

        Event::fake(CheckoutFailed::class);

        $order = Gateway::driver('failing')->checkout($this->app['request'], $this->cart);

        Event::assertDispatched(CheckoutFailed::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }
}

class FailingDriver Extends Driver
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
