<?php

namespace Bazar\Rules;

use Bazar\Contracts\Models\Product;
use Bazar\Contracts\Models\Variant;
use Illuminate\Contracts\Validation\Rule;

class Option implements Rule
{
    /**
     * The product instance.
     *
     * @var \Bazar\Contracts\Models\Product
     */
    protected $product;

    /**
     * The variant instance.
     *
     * @var \Bazar\Contracts\Models\Variant|null
     */
    protected $variant;

    /**
     * Create a new rule instance.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  \Bazar\Contracts\Models\Variant|null  $variant
     * @return void
     */
    public function __construct(Product $product, Variant $variant = null)
    {
        $this->product = $product;
        $this->variant = $variant;
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
        return $this->product->variants->reject(function (Variant $variant): bool {
            return $this->variant && $variant->id === $this->variant->id;
        })->filter(function (Variant $variant) use ($value): bool {
            $value = array_replace(array_fill_keys(array_keys($this->product->options), '*'), $value);

            return empty(array_diff($value, $variant->option));
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
