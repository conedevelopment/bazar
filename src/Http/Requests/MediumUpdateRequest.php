<?php

namespace Cone\Bazar\Http\Requests;

class MediumUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'properties' => ['nullable', 'array'],
            'properties.alt' => ['nullable', 'string'],
            'properties.title' => ['nullable', 'string'],
        ];
    }
}
