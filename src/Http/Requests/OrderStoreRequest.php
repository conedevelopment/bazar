<?php

namespace Bazar\Http\Requests;

use Bazar\Bazar;
use Bazar\Models\Order;
use Bazar\Support\Facades\Shipping;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user' => ['nullable', 'array'],
            'user.id' => [
                'nullable',
                Rule::exists('users', 'id'),
            ],
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'numeric'],
            'items.*.tax' => ['required', 'numeric', 'min:0'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'currency' => [
                'required',
                Rule::in(Bazar::getCurrencies()),
            ],
            'status' => [
                'required',
                Rule::in(Order::proxy()::statuses()),
            ],
            'address' => ['required', 'array'],
            'address.first_name' => ['required', 'string'],
            'address.last_name' => ['required', 'string'],
            'address.email' => ['required', 'string', 'email'],
            'address.phone' => ['required', 'string'],
            'address.country' => ['required', 'string'],
            'address.state' => ['required', 'string'],
            'address.city' => ['required', 'string'],
            'address.postcode' => ['required', 'string'],
            'address.company' => ['nullable', 'string'],
            'address.address' => ['required', 'string'],
            'address.address_secondary' => ['nullable', 'string'],
            'shipping' => ['required', 'array'],
            'shipping.driver' => [
                'required',
                'string',
                Rule::in(array_keys(Shipping::getAvailableDrivers())),
            ],
            'shipping.cost' => ['nullable', 'numeric', 'min:0'],
            'shipping.tax' => ['nullable', 'numeric', 'min:0'],
            'shipping.address.first_name' => ['required', 'string'],
            'shipping.address.last_name' => ['required', 'string'],
            'shipping.address.email' => ['required', 'string', 'email'],
            'shipping.address.phone' => ['required', 'string'],
            'shipping.address.country' => ['required', 'string'],
            'shipping.address.state' => ['required', 'string'],
            'shipping.address.city' => ['required', 'string'],
            'shipping.address.postcode' => ['required', 'string'],
            'shipping.address.company' => ['nullable', 'string'],
            'shipping.address.address' => ['required', 'string'],
            'shipping.address.address_secondary' => ['nullable', 'string'],
        ];
    }
}
