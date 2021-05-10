<?php

namespace Bazar\Http\Requests;

use Bazar\Models\Transaction;
use Bazar\Rules\TransactionAmount;
use Illuminate\Support\Collection;
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
                Rule::in(
                    $this->route('order')->transactions->pluck('driver')->push('manual')->unique()->when(
                        $this->input('type') === Transaction::PAYMENT, static function (Collection $collection): Collection {
                            return $collection->filter(static function (string $driver): bool {
                                return $driver === 'manual';
                            });
                        }
                    )->values()
                ),
            ],
        ];
    }
}
