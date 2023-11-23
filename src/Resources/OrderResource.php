<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Variant;
use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\MorphMany;
use Cone\Root\Fields\MorphTo;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Select;
use Cone\Root\Fields\Text;
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
     * The relations to eager load on every query.
     */
    protected array $with = [
        'user',
    ];

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
                ->with(['buyable', 'itemable'])
                ->withFields(static function (): array {
                    return [
                        MorphTo::make(__('Buyable Item'), 'buyable')
                            ->required()
                            ->types([
                                Product::class,
                                Variant::class,
                            ])
                            ->display(static function (Model $relatable): ?string {
                                return (string) match ($relatable::class) {
                                    Product::class => $relatable->name,
                                    Variant::class => $relatable->alias,
                                    default => $relatable->getKey(),
                                };
                            }),

                        Text::make(__('Name'), 'name')
                            ->required(),

                        Number::make(__('Price'), 'price')
                            ->min(0)
                            ->required()
                            ->format(static function (Request $request, Model $model, ?float $value): string {
                                return  Str::currency($value, $model->itemable->currency);
                            }),

                        Number::make(__('TAX'), 'tax')
                            ->min(0)
                            ->required()
                            ->format(static function (Request $request, Model $model, ?float $value): string {
                                return  Str::currency($value, $model->itemable->currency);
                            }),

                        Number::make(__('Quantity'), 'quantity')
                            ->required()
                            ->min(0),
                    ];
                }),
        ];
    }
}
