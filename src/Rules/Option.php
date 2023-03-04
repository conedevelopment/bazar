<?php

namespace Cone\Bazar\Rules;

use Closure;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Variant;
use Illuminate\Contracts\Validation\ValidationRule;

class Option implements ValidationRule
{
    /**
     * The product instance.
     */
    protected Product $product;

    /**
     * The variant instance.
     */
    protected ?Variant $variant = null;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Product $product, ?Variant $variant = null)
    {
        $this->product = $product;
        $this->variant = $variant;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return $this->product->variants->reject(function (Variant $variant): bool {
            return $this->variant && $variant->id === $this->variant->id;
        })->filter(function (Variant $variant) use ($value): bool {
            $value = array_replace(array_fill_keys(array_keys($this->product->properties), '*'), $value);

            return empty(array_diff($value, $variant->variation));
        })->isEmpty();
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('The :attribute must be a unique combination.');
    }
}
