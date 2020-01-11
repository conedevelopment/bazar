<?php

namespace Bazar\Http\Requests;

use Illuminate\Validation\Rule;

class CategoryStoreRequest extends FormRequest
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
                'nullable',
                'string',
                Rule::unique('categories'),
            ],
            'description' => ['nullable', 'string'],
            'media' => ['nullable', 'array'],
        ];
    }
}
