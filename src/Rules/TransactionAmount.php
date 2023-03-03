<?php

namespace Cone\Bazar\Rules;

use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Illuminate\Contracts\Validation\Rule;

class TransactionAmount implements Rule
{
    /**
     * The amount to be checked.
     */
    protected float $amount;

    /**
     * The transaction type.
     */
    protected string $type = Transaction::PAYMENT;

    /**
     * Create a new rule instance.
     */
    public function __construct(Order $order, ?string $type = null)
    {
        $this->type = $type ?: $this->type;

        $this->amount = $this->type === Transaction::PAYMENT
            ? $order->getTotalPayable()
            : $order->getTotalRefundable();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return is_null($value) || (float) $value <= $this->amount;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return $this->amount <= 0
            ? ($this->type === Transaction::PAYMENT ? __('The order is fully paid.') : __('The order is fully refunded.'))
            : __('The :attribute must be less than :value.', ['value' => $this->amount]);
    }
}
