<?php

namespace Cone\Bazar\Rules;

use Closure;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
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
    protected ?string $type = null;

    /**
     * Create a new rule instance.
     */
    public function __construct(Order $order, string $type = Transaction::PAYMENT)
    {
        $this->type = $type;

        $this->amount = $type === Transaction::PAYMENT
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
                $this->amount <= 0 && $this->type === Transaction::PAYMENT => __('The order is fully paid.'),
                $this->amount <= 0 && $this->type === Transaction::REFUND => __('The order is fully refunded.'),
                default => __('The :attribute must be less than :value.', ['value' => $this->amount]),
            });
        }
    }
}
