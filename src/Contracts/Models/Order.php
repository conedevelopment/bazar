<?php

namespace Cone\Bazar\Contracts\Models;

use Cone\Bazar\Contracts\Discountable;
use Cone\Bazar\Contracts\Itemable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface Order extends Discountable, Itemable
{
    /**
     * Get the cart for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart(): HasOne;

    /**
     * Get the transactions for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany;

    /**
     * Create a payment transaction for the order.
     *
     * @param  float|null  $amount
     * @param  string|null  $driver
     * @param  array  $attributes
     * @return \Cone\Bazar\Models\Transaction
     */
    public function pay(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction;

    /**
     * Create a refund transaction for the order.
     *
     * @param  float|null  $amount
     * @param  string|null  $driver
     * @param  array  $attributes
     * @return \Cone\Bazar\Models\Transaction
     */
    public function refund(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction;

    /**
     * Get the total paid amount.
     *
     * @return float
     */
    public function getTotalPaid(): float;

    /**
     * Get the total refunded amount.
     *
     * @return float
     */
    public function getTotalRefunded(): float;

    /**
     * Get the total payable amount.
     *
     * @return float
     */
    public function getTotalPayable(): float;

    /**
     * Get the total refundabke amount.
     *
     * @return float
     */
    public function getTotalRefundable(): float;

    /**
     * Determine if the order is fully paid.
     *
     * @return bool
     */
    public function paid(): bool;

    /**
     * Determine if the order is fully refunded.
     *
     * @return bool
     */
    public function refunded(): bool;

    /**
     * Set the status by the given value.
     *
     * @param  string  $status
     * @return void
     */
    public function markAs(string $status): void;
}
