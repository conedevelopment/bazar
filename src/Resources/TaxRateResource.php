<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\TaxRate;
use Cone\Root\Fields\Boolean;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TaxRateResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = TaxRate::class;

    /**
     * The group for the resource.
     */
    protected string $group = 'Shop';

    /**
     * Get the model for the resource.
     */
    public function getModel(): string
    {
        return $this->model::getProxiedClass();
    }

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Name'), 'name')
                ->rules(['required', 'string', 'max:256'])
                ->searchable()
                ->sortable()
                ->required(),

            Number::make(__('Rate'), 'value')
                ->required()
                ->rules(['required', 'numeric', 'min:0'])
                ->step(1)
                ->min(0)
                ->suffix('%')
                ->format(static function (Request $request, Model $model): string {
                    return $model->formattedValue;
                }),

            Boolean::make(__('Shipping'), 'shipping')
                ->help(__('If the box is checked, the tax rate is applied for the shipping costs.'))
        ];
    }
}
