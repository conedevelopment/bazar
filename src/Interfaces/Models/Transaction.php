<?php

namespace Cone\Bazar\Interfaces\Models;

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
}
