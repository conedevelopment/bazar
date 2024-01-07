<?php

namespace Cone\Bazar\Events;

use Cone\Bazar\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;

class TransactionCompleted
{
    use Dispatchable;

    /**
     * The transaction instance.
     */
    public Transaction $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
