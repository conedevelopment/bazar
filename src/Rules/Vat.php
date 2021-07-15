<?php

namespace Cone\Bazar\Rules;

use Illuminate\Contracts\Validation\Rule;

class Vat implements Rule
{
    /**
     * The country specific VAT patterns.
     *
     * @var array
     */
    protected $patterns = [
        '(AT)?U[0-9]{8}',
        '(BE)?0[0-9]{9}',
        '(BG)?[0-9]{9,10}',
        '(CY)?[0-9]{8}L',
        '(CZ)?[0-9]{8,10}',
        '(DE)?[0-9]{9}',
        '(DK)?[0-9]{8}',
        '(EE)?[0-9]{9}',
        '(EL|GR)?[0-9]{9}',
        '(ES)?[0-9A-Z][0-9]{7}[0-9A-Z]',
        '(FI)?[0-9]{8}',
        '(FR)?[0-9A-Z]{2}[0-9]{9}',
        '(GB)?([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})',
        '(HU)?[0-9]{8}',
        '(IE)?[0-9]S[0-9]{5}L',
        '(IT)?[0-9]{11}',
        '(LT)?([0-9]{9}[0-9]{12})',
        '(LU)?[0-9]{8}',
        '(LV)?[0-9]{11}',
        '(MT)?[0-9]{8}',
        '(NL)?[0-9]{9}B[0-9]{2}',
        '(PL)?[0-9]{10}',
        '(PT)?[0-9]{9}',
        '(RO)?[0-9]{2,10}',
        '(SE)?[0-9]{12}',
        '(SI)?[0-9]{8}',
        '(SK)?[0-9]{10}',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match('/^('.implode('|', $this->patterns).')$/', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('The :attribute must be a valid VAT number.');
    }
}
