<?php

namespace Cone\Bazar\Fields;

use Cone\Bazar\Shipping\Driver;
use Cone\Bazar\Support\Facades\Shipping as Manager;
use Cone\Root\Fields\HasOne;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Select;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Support\Str;

class Shipping extends HasOne
{
    /**
     * {@inheritdoc}
     */
    public function fields(RootRequest $request): array
    {
        return [
            Select::make(__('Driver'), 'driver')
                ->options(array_map(static function (Driver $driver): string {
                    return $driver->getName();
                }, Manager::getAvailableDrivers())),

            Number::make(__('Cost'), 'cost')
                ->step(0.1)
                ->format(static function (RootRequest $request, Shipping $model, mixed $value): string {
                    return Str::currency($value, $model->parent->currency);
                }),

            Number::make(__('Tax'), 'tax')
                ->step(0.1)
                ->format(static function (RootRequest $request, Shipping $model, mixed $value): string {
                    return Str::currency($value, $model->parent->currency);
                }),
        ];
    }
}
