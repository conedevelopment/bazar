<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Variant;
use Cone\Bazar\Support\Currency;
use Cone\Root\Fields\MorphMany;
use Cone\Root\Fields\MorphTo;
use Cone\Root\Fields\Number;
use Cone\Root\Fields\Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Items extends MorphMany
{
    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'buyable',
        'itemable',
    ];

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Products'), $modelAttribute ?: 'items', $relation);

        $this->display('name');
        $this->asSubResource();
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            MorphTo::make(__('Buyable Item'), 'buyable')
                ->required()
                ->types([
                    Product::getProxiedClass(),
                    Variant::getProxiedClass(),
                ])
                ->display(static function (Model $relatable): ?string {
                    return (string) match ($relatable::class) {
                        Product::getProxiedClass() => $relatable->name,
                        Variant::getProxiedClass() => $relatable->alias,
                        default => $relatable->getKey(),
                    };
                }),

            Text::make(__('Name'), 'name')
                ->required(),

            Number::make(__('Price'), 'price')
                ->min(0)
                ->required()
                ->format(static function (Request $request, Model $model, ?float $value): string {
                    return (new Currency($value, $model->itemable->currency))->format();
                }),

            Number::make(__('TAX'), 'tax')
                ->min(0)
                ->required()
                ->format(static function (Request $request, Model $model, ?float $value): string {
                    return (new Currency($value, $model->itemable->currency))->format();
                }),

            Number::make(__('Quantity'), 'quantity')
                ->required()
                ->min(0),
        ];
    }
}
