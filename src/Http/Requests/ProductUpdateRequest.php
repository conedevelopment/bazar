<?php

namespace Bazar\Http\Requests;

use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => [
                'required',
                'string',
                Rule::unique('bazar_products')->ignoreModel($this->route('product')),
            ],
            'description' => ['nullable', 'string'],
            'options' => ['nullable', 'array'],
            'options.*' => ['required', 'array'],
            'prices' => ['array'],
            'prices.*' => ['array'],
            'prices.*.*' => ['nullable', 'numeric', 'min:0'],
            'inventory' => ['array'],
            'inventory.sku' => [
                'nullable',
                'string',
                Rule::unique('bazar_products', 'inventory->sku')->ignoreModel($this->route('product')),
            ],
            'inventory.quantity' => ['nullable', 'numeric', 'min:0'],
            'inventory.weight' => ['nullable', 'numeric', 'min:0'],
            'inventory.length' => ['nullable', 'numeric', 'min:0'],
            'inventory.width' => ['nullable', 'numeric', 'min:0'],
            'inventory.height' => ['nullable', 'numeric', 'min:0'],
            'inventory.virtual' => ['bool'],
            'inventory.downloadable' => ['bool'],
            'inventory.files' => ['required_if:inventory.downloadable,true', 'array'],
            'inventory.files.*' => ['array'],
            'inventory.files.*.name' => ['required', 'string'],
            'inventory.files.*.url' => ['required', 'string', 'url'],
            'inventory.files.*.limit' => ['nullable', 'numeric', 'min:1'],
            'inventory.files.*.expiration' => ['nullable', 'numeric', 'min:1'],
            'media' => ['nullable', 'array'],
            'categories' => ['nullable', 'array'],
        ];
    }
}
