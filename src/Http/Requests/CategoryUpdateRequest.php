<?php

namespace Cone\Bazar\Http\Requests;

use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends FormRequest
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
                Rule::unique('bazar_categories')->ignoreModel($this->route('category')),
            ],
            'description' => ['nullable', 'string'],
            'media' => ['nullable', 'array'],
        ];
    }
}
