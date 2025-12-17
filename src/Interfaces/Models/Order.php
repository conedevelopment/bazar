<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Cone\Bazar\Interfaces\Checkoutable;
use Cone\Bazar\Interfaces\Discountable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notification;

interface Order extends Checkoutable, Discountable
{
    /**
     * Get the cart for the order.
     */
    public function cart(): HasOne;

    /**
     * Get the transactions for the order.
     */
    public function transactions(): HasMany;

    /**
     * Get the base transaction for the order.
     */
    public function transaction(): HasOne;

    /**
     * Create a payment transaction for the order.
     *
     * @return \Cone\Bazar\Models\Transaction
     */
    public function pay(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction;

    /**
     * Create a refund transaction for the order.
     *
     * @return \Cone\Bazar\Models\Transaction
     */
    public function refund(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction;

    /**
     * Get the label for the order.
     */
    public function getLabel(): string;

    /**
     * Get the total paid amount.
     */
    public function getTotalPaid(): float;

    /**
     * Get the total refunded amount.
     */
    public function getTotalRefunded(): float;

    /**
     * Get the total payable amount.
     */
    public function getTotalPayable(): float;

    /**
     * Get the total refundabke amount.
     */
    public function getTotalRefundable(): float;

    /**
     * Determine if the order is fully paid.
     */
    public function paid(): bool;

    /**
     * Determine if the order is payable.
     */
    public function payable(): bool;

    /**
     * Determine if the order is fully refunded.
     */
    public function refunded(): bool;

    /**
     * Determine if the order is refundable.
     */
    public function refundable(): bool;

    /**
     * Set the status by the given value.
     */
    public function markAs(string $status): void;

    /**
     * Get the notifiable object.
     */
    public function getNotifiable(): object;

    /**
     * Send the given notification.
     */
    public function sendNotification(Notification $notification): void;

    /**
     * Send the order details notification.
     */
    public function sendOrderDetailsNotification(): void;
}
