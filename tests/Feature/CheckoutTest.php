<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\CartFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Models\User;
use Bazar\Notifications\AdminNewOrder;
use Bazar\Notifications\CustomerNewOrder;
use Bazar\Services\Checkout;
use Bazar\Tests\TestCase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

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
        Notification::fake();

        $response = (new Checkout($this->cart))->shipping(
            'local-pickup', AddressFactory::new()->make()->toArray()
        )->billing(
            AddressFactory::new()->make()->toArray()
        )->gateway('cash')->onSuccess(function ($order) {
            return 'Success';
        })->onFailure(function ($e, $order) {
            //
        })->process();

        Notification::assertSentTo($this->admin, AdminNewOrder::class);
        Notification::assertSentTo(
            new AnonymousNotifiable,
            CustomerNewOrder::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === $this->cart->address->email;
            }
        );

        $this->assertEquals('Success', $response);
    }

    /** @test */
    public function it_handles_failed_checkout()
    {
        $response = (new Checkout($this->cart))->shipping(
            'local-pickup', AddressFactory::new()->make()->toArray()
        )->billing(
            AddressFactory::new()->make()->toArray()
        )->gateway('fake')->onSuccess(function ($order) {
            //
        })->onFailure(function ($e, $order) {
            return 'Failure';
        })->process();

        $this->assertEquals('Failure', $response);
    }
}
