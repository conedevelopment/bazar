<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Gateway\Driver;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Support\Facades\Gateway;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\Select;
use Cone\Root\Http\Requests\RootRequest;

class Transactions extends HasMany
{
    /**
     * Create a new transactions field instance.
     */
    public function __construct(string $label = null, string $name = null, Closure|string $relation = null)
    {
        parent::__construct(
            $label ?: __('Transactions'), $name ?: 'transactions', $relation
        );

        $this->asSubResource();
        $this->hiddenOnIndex();
        $this->display('driver_name');
    }

    /**
     * Define the fields for the object.
     */
    public function fields(RootRequest $request): array
    {
        return [
            Currency::make(__('Amount'), 'amount')
                ->step(0.1)
                ->currency(static function (RootRequest $request, Transaction $transaction): string {
                    return $transaction->parent->currency;
                }),

            Select::make(__('Driver'), 'driver')
                ->options(array_map(static function (Driver $driver): string {
                    return $driver->getName();
                }, Gateway::getAvailableDrivers())),

            Select::make(__('Type'), 'type')
                ->options([
                    Transaction::PAYMENT => __('Payment'),
                    Transaction::REFUND => __('Refund'),
                ]),

            Date::make(__('Completed At'), 'completed_at')
                ->withTime(),
        ];
    }
}
