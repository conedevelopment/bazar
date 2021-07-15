<?php

namespace Cone\Bazar\Http\Requests;

use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Rules\TransactionAmount;
use Cone\Bazar\Support\Facades\Gateway;
use Illuminate\Validation\Rule;

class TransactionStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(Transaction::REFUND, Transaction::PAYMENT)],
            'amount' => [
                'nullable',
                'numeric',
                'min:0',
                new TransactionAmount($this->route('order'), $this->input('type')),
            ],
            'driver' => [
                'required',
                Rule::in(array_keys(Gateway::getAvailableDrivers($this->route('order')))),
            ],
            'key' => ['nullable', 'unique:bazar_transactions'],
        ];
    }
}
