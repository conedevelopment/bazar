<?php

namespace Bazar\Http\Requests;

use Bazar\Models\Order;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(array_keys(Order::statuses())),
            ],
        ];
    }
}
