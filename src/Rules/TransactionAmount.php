<?php

namespace Cone\Bazar\Rules;

use Closure;
use Cone\Bazar\Models\Transaction;
use Illuminate\Contracts\Validation\ValidationRule;

class TransactionAmount implements ValidationRule
{
    /**
     * The transaction instance.
     */
    protected Transaction $transaction;

    /**
     * Create a new rule instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $max = $this->transaction->type === Transaction::PAYMENT
            ? $this->transaction->order->getTotalPayable()
            : $this->transaction->order->getTotalRefundable();

        if (is_null($value) || (float) $value > $max) {
            call_user_func($fail, match (true) {
                $max <= 0 && $this->type === Transaction::PAYMENT => __('The order is fully paid.'),
                $max <= 0 && $this->type === Transaction::REFUND => __('The order is fully refunded.'),
                default => __('The :attribute must be less than :value.', ['value' => $max]),
            });
        }
    }
}
