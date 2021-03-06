<?php

namespace Bazar\Http\Requests;

class ProductStoreRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'unique:bazar_products'],
            'description' => ['nullable', 'string'],
            'properties' => ['nullable', 'array'],
            'properties.*' => ['required', 'array'],
            'prices' => ['array'],
            'prices.*' => ['array'],
            'prices.*.*' => ['nullable', 'numeric', 'min:0'],
            'inventory' => ['array'],
            'inventory.sku' => ['nullable', 'unique:bazar_products,inventory->sku'],
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
