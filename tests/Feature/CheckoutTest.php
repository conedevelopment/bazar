<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Events\CartTouched;
use Bazar\Events\CheckoutFailed;
use Bazar\Events\CheckoutFailing;
use Bazar\Events\CheckoutProcessed;
use Bazar\Events\CheckoutProcessing;
use Bazar\Services\Checkout;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CheckoutTest extends TestCase
{
    protected $cart;

    public function setUp(): void
    {
        parent::setUp();

        $this->cart = CartFactory::new()->create();

        $this->cart->products()->attach(
            ProductFactory::new()->count(3)->create()->mapWithKeys(function ($product) {
                [$quantity, $tax, $price] = [mt_rand(1, 5), 0, $product->price];

                return [$product->id => compact('price', 'tax', 'quantity')];
            })->all()
        );
    }

    /** @test */
    public function it_can_process_checkout()
    {
        Event::fake([
            CartTouched::class,
            CheckoutProcessed::class,
            CheckoutProcessing::class,
        ]);

        $response = (new Checkout($this->cart))->shipping(
            'local-pickup', AddressFactory::new()->make()->toArray()
        )->billing(
            AddressFactory::new()->make()->toArray()
        )->gateway('cash')->onSuccess(function ($order) {
            return 'Success';
        })->onFailure(function ($e, $order) {
            //
        })->process();

        Event::assertDispatched(CartTouched::class, function ($event) {
            return $event->cart->id === $this->cart->id;
        });

        Event::assertDispatched(CheckoutProcessing::class, function ($event) {
            return $this->cart->total() === $event->order->total();
        });

        Event::assertDispatched(CheckoutProcessed::class, function ($event) {
            return $this->cart->total() === $event->order->total();
        });

        $this->assertEquals('Success', $response);
    }

    /** @test */
    public function it_cannot_process_checkout()
    {
        Event::fake([
            CartTouched::class,
            CheckoutFailed::class,
            CheckoutFailing::class,
        ]);

        $response = (new Checkout($this->cart))->shipping(
            'local-pickup', AddressFactory::new()->make()->toArray()
        )->billing(
            AddressFactory::new()->make()->toArray()
        )->gateway('fake')->onSuccess(function ($order) {
            //
        })->onFailure(function ($e, $order) {
            return 'Failure';
        })->process();

        Event::assertDispatched(CartTouched::class, function ($event) {
            return $event->cart->id === $this->cart->id;
        });

        Event::assertDispatched(CheckoutFailing::class, function ($event) {
            return $this->cart->total() === $event->order->total();
        });

        Event::assertDispatched(CheckoutFailed::class, function ($event) {
            return $this->cart->total() === $event->order->total();
        });

        $this->assertEquals('Failure', $response);
    }
}
