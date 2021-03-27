<?php

namespace Bazar\Contracts\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Transaction
{
    /**
     * Get the order for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo;

    /**
     * Get the name of the gateway driver.
     *
     * @return string
     */
    public function getDriverNameAttribute(): string;

    /**
     * Get the URL of the transaction.
     *
     * @return string|null
     */
    public function getUrlAttribute(): ?string;

    /**
     * Determine if the payment is completed.
     *
     * @return bool
     */
    public function completed(): bool;

    /**
     * Determine if the payment is pending.
     *
     * @return bool
     */
    public function pending(): bool;

    /**
     * Mark the transaction as completed.
     *
     * @param \DateTimeInterface|null  $date
     * @return $this
     */
    public function markAsCompleted(DateTimeInterface $date = null): self;

    /**
     * Mark the transaction as pending.
     *
     * @return $this
     */
    public function markAsPending(): self;

    /**
     * Set the driver.
     *
     * @param  string  $driver
     * @return $this
     */
    public function driver(string $driver): self;
}
