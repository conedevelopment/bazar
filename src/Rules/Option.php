<?php

namespace Bazar\Rules;

use Bazar\Models\Product;
use Bazar\Models\Variation;
use Illuminate\Contracts\Validation\Rule;

class Option implements Rule
{
    /**
     * The product instance.
     *
     * @var \Bazar\Models\Product
     */
    protected $product;

    /**
     * The variation instance.
     *
     * @var \Bazar\Models\Variation|null
     */
    protected $variation;

    /**
     * Create a new rule instance.
     *
     * @param  \Bazar\Models\Product|int  $product
     * @param  \Bazar\Models\Variation|null  $variation
     * @return void
     */
    public function __construct($product, Variation $variation = null)
    {
        $this->variation = $variation;
        $this->product = $product instanceof Product ? $product : Product::find($product);
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
        return $this->product && $this->product->variations->reject(function ($variation) {
            return $this->variation && $variation->id === $this->variation->id;
        })->filter(function ($variation) use ($value) {
            $value = array_replace(array_fill_keys(array_keys($this->product->options), '*'), $value);

            return empty(array_diff($value, $variation->option));
        })->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('The :attribute must be a unique combination.');
    }
}
