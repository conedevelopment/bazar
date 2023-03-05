<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Interfaces\Models\Shipping as Model;
use Cone\Bazar\Shipping\Driver;
use Cone\Bazar\Support\Facades\Shipping as Manager;
use Cone\Root\Fields\MorphOne;
use Cone\Root\Fields\Select;
use Cone\Root\Http\Requests\RootRequest;

class Shipping extends MorphOne
{
    /**
     * Create a new shipping field instance.
     */
    public function __construct(string $label = null, string $name = null, Closure|string $relation = null)
    {
        parent::__construct(
            $label ?: __('Shipping'), $name ?: 'shipping', $relation
        );

        $this->asSubResource();
        $this->display('driver_name');
    }

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

            Currency::make(__('Cost'), 'cost')
                ->step(0.1)
                ->currency(static function (RootRequest $request, Model $model): string {
                    return $model->parent->currency;
                }),

            Currency::make(__('Tax'), 'tax')
                ->step(0.1)
                ->currency(static function (RootRequest $request, Model $model): string {
                    return $model->parent->currency;
                }),
        ];
    }
}
