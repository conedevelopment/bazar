<?php

namespace Bazar\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class AddressStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'alias' => [
                'nullable',
                'string',
                Rule::unique('addresses')->where(function (Builder $query) {
                    return $query->where([
                        ['addressable_type', get_class($this->route('user'))],
                        ['addressable_id', $this->route('user')->id]
                    ]);
                }),
            ],
            'phone' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],
            'postcode' => ['required', 'string'],
            'company' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'address_secondary' => ['nullable', 'string'],
            'default' => ['required', 'boolean'],
        ];
    }
}
