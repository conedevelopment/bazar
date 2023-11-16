<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Order;
use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\MorphMany;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Select;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Order::class;

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            BelongsTo::make(__('Customer'), 'user')
                ->display('name'),

            Select::make(__('Currency'), 'currency')
                ->options(Bazar::getCurrencies()),

            MorphMany::make(__('Products'), 'items')
                ->display('name')
                ->asSubResource()
                ->withFields(static function (Request $request): array {
                    return [
                        BelongsTo::make(__('Product'), 'buyable')
                            ->display('name'),

                        Number::make(__('Price'), 'price')
                            ->min(0)
                            ->format(static function (Request $request, Model $model, ?float $value): string {
                                return  Str::currency($value, $model->itemable->currency);
                            }),

                        Number::make(__('TAX'), 'tax')
                            ->min(0)
                            ->format(static function (Request $request, Model $model, ?float $value): string {
                                return  Str::currency($value, $model->itemable->currency);
                            }),

                        Number::make(__('Quantity'), 'quantity')
                            ->min(0),
                    ];
                }),
        ];
    }
}
