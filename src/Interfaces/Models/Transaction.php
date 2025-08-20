<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Transaction
{
    /**
     * Get the order for the transaction.
     */
    public function order(): BelongsTo;

    /**
     * Determine if the payment is completed.
     */
    public function completed(): bool;

    /**
     * Determine if the payment is pending.
     */
    public function pending(): bool;

    /**
     * Mark the transaction as completed.
     */
    public function markAsCompleted(): void;

    /**
     * Mark the transaction as pending.
     */
    public function markAsPending(): void;
}
