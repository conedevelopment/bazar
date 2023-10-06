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
    public function __construct(Product $product, Variant $variant = null)
    {
        $this->product = $product;
        $this->variant = $variant;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $variant = $this->product->toVariant($value);

        if (! is_null($variant) && ! $variant->is($this->variant)) {
            call_user_func_array($fail, [__('The :attribute must be a unique combination.')]);
        }
    }
}
