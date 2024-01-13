<?php

namespace Cone\Bazar\Gateway;

use Cone\Bazar\Events\CheckoutFailed;
use Cone\Bazar\Events\CheckoutProcessed;
use Cone\Bazar\Events\PaymentCaptured;
use Cone\Bazar\Events\PaymentCaptureFailed;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Support\Driver as BaseDriver;
use Cone\Bazar\Support\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\URL;
use Throwable;

abstract class Driver extends BaseDriver
{
    /**
     * The driver name.
     */
    protected string $name;

    /**
     * Process the payment.
     */
    public function pay(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        return $order->pay($amount, $this->name, $attributes);
    }

    /**
     * Process the refund.
     */
    public function refund(Order $order, ?float $amount = null, array $attributes = []): Transaction
    {
        $transaction = $order->refund($amount, $this->name, $attributes);

        $order->markAs(Order::REFUNDED);

        return $transaction;
    }

    /**
     * Get the URL of the transaction.
     */
    public function getTransactionUrl(Transaction $transaction): ?string
    {
        return null;
    }

    /**
     * Get the URL for the payment capture.
     */
    public function getCaptureUrl(Order $order): string
    {
        return URL::route('bazar.gateway.capture', [
            'driver' => $this->name,
            'order' => $order->uuid,
        ]);
    }

    /**
     * Get the success URL.
     */
    public function getSuccessUrl(Order $order): string
    {
        return str_replace(['{order}'], [$order->uuid], $this->config['success_url']);
    }

    /**
     * Get the failure URL.
     */
    public function getFailureUrl(Order $order): string
    {
        return str_replace(['{order}'], [$order->uuid], $this->config['failure_url']);
    }

    /**
     * Resolve the order model for checkout.
     */
    public function resolveOrderForCheckout(Request $request): Order
    {
        return Cart::getModel()->toOrder();
    }

    /**
     * Handle the checkout request.
     */
    public function handleCheckout(Request $request): Response
    {
        $order = $this->resolveOrderForCheckout($request);

        try {
            $this->checkout($request, $order);

            CheckoutProcessed::dispatch($this->name, $order);

            $url = $this->getCaptureUrl($order);
        } catch (Throwable $exception) {
            report($exception);

            CheckoutFailed::dispatch($this->name, $order);

            $url = $this->getFailureUrl($order);
        }

        return new Response($url, $order->toArray());
    }

    /**
     * Checkout the order.
     */
    public function checkout(Request $request, Order $order): Order
    {
        return $order;
    }

    /**
     * Resolve the order model for payment capture.
     */
    public function resolveOrderForCapture(Request $request): Order
    {
        return Order::proxy()->newQuery()->where('uuid', $request->input('order'))->firstOrFail();
    }

    /**
     * Handle the capture request.
     */
    public function handleCapture(Request $request): Response
    {
        $order = $this->resolveOrderForCapture($request);

        try {
            $this->capture($request, $order);

            PaymentCaptured::dispatch($this->name, $order);

            $url = $this->getSuccessUrl($order);
        } catch (Throwable $exception) {
            report($exception);

            PaymentCaptureFailed::dispatch($this->name, $order);

            $url = $this->getFailureUrl($order);
        }

        return new Response($url, $order->toArray());
    }

    /**
     * Capture the payment.
     */
    public function capture(Request $request, Order $order): Order
    {
        $this->pay($order);

        return $order;
    }

    /**
     * Resolve the order model for notification.
     */
    public function resolveOrderForNotification(Request $request): Order
    {
        return Order::proxy()->newQuery()->where('uuid', $request->input('order'))->firstOrFail();
    }

    /**
     * Handle the notification request.
     */
    public function handleNotification(Request $request): Response
    {
        $order = $this->resolveOrderForNotification($request);

        $this->notification($request, $order);

        return (new Response())->respondWith(function (string $url, array $data): HttpResponse {
            return new HttpResponse('', HttpResponse::HTTP_NO_CONTENT);
        });
    }

    /**
     * Update the order update after the notification.
     */
    public function notification(Request $request, Order $order): Order
    {
        return $order;
    }
}
