<?php

namespace Bazar\Cart;

use Bazar\Models\Cart;
use Bazar\Models\Shipping;
use Bazar\Events\CartTouched;
use Bazar\Events\CheckoutFailed;
use Bazar\Events\CheckoutFailing;
use Bazar\Events\CheckoutProcessed;
use Bazar\Events\CheckoutProcessing;
use Bazar\Models\Order;
use Bazar\Support\Facades\Gateway;
use Throwable;

class Checkout
{
    /**
     * The cart instance.
     *
     * @var \Bazar\Models\Cart
     */
    protected $cart;

    /**
     * The success callback.
     *
     * @var callable
     */
    protected $onSuccess;

    /**
     * The failure callback.
     *
     * @var callable
     */
    protected $onFailure;

    /**
     * The payment gateway driver.
     *
     * @var string
     */
    protected $gateway;

    /**
     * Create a new checkout instance.
     *
     * @param  \Bazar\Models\Cart  $cart
     * @return void
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
        $this->gateway = Gateway::getDefaultDriver();

        $this->onFailure = static function (): void {};
        $this->onSuccess = static function (): void {};
    }

    /**
     * Set the payment gateway.
     *
     * @param  string  $gateway
     * @return $this
     */
    public function gateway(string $gateway): Checkout
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Set the billing data.
     *
     * @param  array  $billing
     * @return $this
     */
    public function billing(array $billing): Checkout
    {
        $this->cart->address->fill($billing)->save();

        return $this;
    }

    /**
     * Set the shipping driver and details.
     *
     * @param  string  $driver
     * @param  array  $details
     * @return $this
     */
    public function shipping(string $driver, array $details = []): Checkout
    {
        tap($this->cart->shipping->driver($driver), static function (Shipping $shipping): void {
            if (! $shipping->exists) {
                $shipping->save();
            }
        })->address->fill(
            array_replace_recursive($this->cart->address->toArray(), $details)
        )->save();

        return $this;
    }

    /**
     * Set the success callback.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function onSuccess(callable $callback): Checkout
    {
        $this->onSuccess = $callback;

        return $this;
    }

    /**
     * Set the failure callback.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function onFailure(callable $callback): Checkout
    {
        $this->onFailure = $callback;

        return $this;
    }

    /**
     * Process the checkout.
     *
     * @return mixed
     */
    public function process()
    {
        $order = $this->prepare();

        try {
            Gateway::driver($this->gateway)->pay($order);

            CheckoutProcessing::dispatch($order);

            $response = call_user_func_array($this->onSuccess, [$order]);

            CheckoutProcessed::dispatch($order);
        } catch (Throwable $exception) {
            CheckoutFailing::dispatch($order);

            $response = call_user_func_array($this->onFailure, [$exception, $order]);

            CheckoutFailed::dispatch($order);
        } finally {
            return $response;
        }
    }

    /**
     * Prepare the order.
     *
     * @return \Bazar\Models\Order
     */
    protected function prepare(): Order
    {
        CartTouched::dispatch($this->cart);

        return Order::proxy()::createFrom($this->cart);
    }
}
