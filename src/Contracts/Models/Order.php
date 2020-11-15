<?php

namespace Bazar\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

interface Order
{
    /**
     * Get the transactions for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany;

    /**
     * Get the currency attribute.
     *
     * @param  string|null  $value
     * @return string
     */
    public function getCurrencyAttribute(string $value = null): string;

    /**
     * Get all the payment transactions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPaymentsAttribute(): Collection;

    /**
     * Get all the refunds transactions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRefundsAttribute(): Collection;

    /**
     * Get the status name attribute.
     *
     * @return string
     */
    public function getStatusNameAttribute(): string;

    /**
     * Get the total paid amount.
     *
     * @return float
     */
    public function totalPaid(): float;

    /**
     * Get the total refunded amount.
     *
     * @return float
     */
    public function totalRefunded(): float;

    /**
     * Get the total payable amount.
     *
     * @return float
     */
    public function totalPayable(): float;

    /**
     * Get the total refundabke amount.
     *
     * @return float
     */
    public function totalRefundable(): float;

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
     * @return $this
     */
    public function status(string $status): Order;
}
