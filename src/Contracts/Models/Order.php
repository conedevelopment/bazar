<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Discountable;
use Bazar\Contracts\Itemable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

interface Order extends Breadcrumbable, Discountable, Itemable
{
    /**
     * Get the transactions for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany;

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
     * Create a payment transaction for the order.
     *
     * @param  float|null  $amount
     * @param  string|null  $driver
     * @param  array  $attributes
     * @return \Bazar\Models\Transaction
     */
    public function pay(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction;

    /**
     * Create a refund transaction for the order.
     *
     * @param  float|null  $amount
     * @param  string|null  $driver
     * @param  array  $attributes
     * @return \Bazar\Models\Transaction
     */
    public function refund(?float $amount = null, ?string $driver = null, array $attributes = []): Transaction;

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
    public function status(string $status): self;
}
