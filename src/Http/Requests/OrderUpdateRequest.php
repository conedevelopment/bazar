<?php

namespace Cone\Bazar\Http\Requests;

use Cone\Bazar\Models\Order;
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
                Rule::in(Order::proxy()::statuses()),
            ],
        ];
    }
}
