<?php

namespace Bazar\Rules;

use Bazar\Models\Order;
use Bazar\Models\Transaction;
use Illuminate\Contracts\Validation\Rule;

class TransactionAmount implements Rule
{
    /**
     * The amount to be checked.
     *
     * @var float
     */
    protected float $amount;

    /**
     * The transaction type.
     *
     * @var string
     */
    protected string $type = Transaction::PAYMENT;

    /**
     * Create a new rule instance.
     *
     * @param  \Bazar\Models\Order  $order
     * @param  string|null  $type
     * @return void
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
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return is_null($value) || (float) $value <= $this->amount;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->amount <= 0
            ? ($this->type === Transaction::PAYMENT ? __('The order is fully paid.') : __('The order is fully refunded.'))
            : __('The :attribute must be less than :value.', ['value' => $this->amount]);
    }
}
