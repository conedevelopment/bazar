<?php

namespace Cone\Bazar\Http\Requests;

use Cone\Bazar\Rules\MatchingPassword;

class PasswordUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return ! is_null($this->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                new MatchingPassword($this->user()),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
