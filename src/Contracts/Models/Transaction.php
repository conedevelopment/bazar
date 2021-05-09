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
     * @return void
     */
    public function markAsCompleted(?DateTimeInterface $date = null): void;

    /**
     * Mark the transaction as pending.
     *
     * @return void
     */
    public function markAsPending(): void;

    /**
     * Set the driver.
     *
     * @param  string  $driver
     * @return $this
     */
    public function driver(string $driver): self;
}
