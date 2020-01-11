<?php

namespace Bazar\Http\Requests;

use Bazar\Rules\Option;
use Illuminate\Validation\Rule;

class VariationUpdateRequest extends FormRequest
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
                Rule::unique('variations')->where(function ($query) {
                    return $query->where('product_id', $this->route('product')->id);
                })->ignoreModel($this->route('variation')),
            ],
            'option' => [
                'required',
                'array',
                new Option($this->route('product'), $this->route('variation')),
            ],
            'option.*' => ['required', 'string'],
            'prices' => ['nullable', 'array'],
            'prices.*' => ['array'],
            'prices.*.normal' => ['nullable', 'numeric', 'min:0'],
            'prices.*.sale' => ['nullable', 'numeric', 'min:0'],
            'inventory' => ['array'],
            'inventory.sku' => [
                'nullable',
                'string',
                Rule::unique('variations', 'inventory->sku')->ignoreModel($this->route('variation')),
            ],
            'inventory.quantity' => ['nullable', 'numeric', 'min:0'],
            'inventory.weight' => ['nullable', 'numeric', 'min:0'],
            'inventory.dimensions' => ['array'],
            'inventory.dimensions.*' => ['nullable', 'numeric'],
            'media' => ['nullable', 'array'],
        ];
    }
}
