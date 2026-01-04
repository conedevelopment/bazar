<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Enums\Currency;
use Cone\Bazar\Models\Coupon;
use Cone\Bazar\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

interface Checkoutable extends Shippable
{
    /**
     * Get the user for the model.
     */
    public function user(): BelongsTo;

    /**
     * Get the items for the model.
     */
    public function items(): MorphMany;

    /**
     * Get the coupons for the model.
     */
    public function coupons(): MorphToMany;

    /**
     * Get the checkoutable items.
     */
    public function getItems(): Collection;

    /**
     * Get the checkoutable taxable items.
     */
    public function getTaxables(): Collection;

    /**
     * Get the checkoutable fee items.
     */
    public function getFees(): Collection;

    /**
     * Get the checkoutable line items.
     */
    public function getLineItems(): Collection;

    /**
     * Get the currency.
     */
    public function getCurrency(): Currency;

    /**
     * Get the total.
     */
    public function getTotal(): float;

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string;

    /**
     * Get the subtotal.
     */
    public function getSubtotal(): float;

    /**
     * Get the formatted subtotal.
     */
    public function getFormattedSubtotal(): string;

    /**
     * Get the checkoutable model's fee total.
     */
    public function getFeeTotal(): float;

    /**
     * Get the formatted fee total.
     */
    public function getFormattedFeeTotal(): string;

    /**
     * Get the tax.
     */
    public function getTax(): float;

    /**
     * Get the formatted tax.
     */
    public function getFormattedTax(): string;

    /**
     * Calculate the tax.
     */
    public function calculateTax(): float;

    /**
     * Find an item by its attributes or make a new instance.
     */
    public function findItem(array $attributes): ?Item;

    /**
     * Merge the given item into the collection.
     */
    public function mergeItem(Item $item): Item;

    /**
     * Sync the items.
     */
    public function syncItems(): void;

    /**
     * Determine whether the checkoutable models needs payment.
     */
    public function needsPayment(): bool;

    /**
     * Apply a coupon to the checkoutable model.
     */
    public function applyCoupon(string|Coupon $coupon): bool;

    /**
     * Remove a coupon from the checkoutable model.
     */
    public function removeCoupon(string|Coupon $coupon): void;

    /**
     * Get the applicable discount rules.
     */
    public function getApplicableDiscountRules(): Collection;
}
