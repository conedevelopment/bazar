<?php

namespace Cone\Bazar\Rules;

use Closure;
use Cone\Bazar\Enums\TransactionType;
use Cone\Bazar\Models\Order;
use Illuminate\Contracts\Validation\ValidationRule;

class TransactionAmount implements ValidationRule
{
    /**
     * The amount to be checked.
     */
    protected float $amount;

    /**
     * The transaction type.
     */
    protected TransactionType $type;

    /**
     * Create a new rule instance.
     */
    public function __construct(Order $order, TransactionType $type = TransactionType::Payment)
    {
        $this->type = $type;

        $this->amount = $type === TransactionType::Payment
            ? $order->getTotalPayable()
            : $order->getTotalRefundable();
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null($value) || (float) $value > $this->amount) {
            call_user_func($fail, match (true) {
                $this->amount <= 0 && $this->type === TransactionType::Payment => __('The order is fully paid.'),
                $this->amount <= 0 && $this->type === TransactionType::Refund => __('The order is fully refunded.'),
                default => __('The :attribute must be less than :value.', ['value' => $this->amount]),
            });
        }
    }
}
