<?php

namespace Bazar\Http\Requests;

use Bazar\Rules\Option;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class VariantUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'alias' => [
                'nullable',
                'string',
                Rule::unique('bazar_variants')->where(function (Builder $query): Builder {
                    return $query->where('product_id', $this->route('product')->id);
                })->ignoreModel($this->route('variant')),
            ],
            'option' => [
                'required',
                'array',
                new Option($this->route('product'), $this->route('variant')),
            ],
            'option.*' => ['required', 'string'],
            'prices' => ['nullable', 'array'],
            'prices.*' => ['array'],
            'prices.*.*' => ['nullable', 'numeric', 'min:0'],
            'inventory' => ['array'],
            'inventory.sku' => [
                'nullable',
                'string',
                Rule::unique('bazar_variants', 'inventory->sku')->ignoreModel($this->route('variant')),
            ],
            'inventory.quantity' => ['nullable', 'numeric', 'min:0'],
            'inventory.weight' => ['nullable', 'numeric', 'min:0'],
            'inventory.length' => ['nullable', 'numeric', 'min:0'],
            'inventory.width' => ['nullable', 'numeric', 'min:0'],
            'inventory.height' => ['nullable', 'numeric', 'min:0'],
            'media' => ['nullable', 'array'],
        ];
    }
}
