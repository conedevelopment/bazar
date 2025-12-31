<?php

declare(strict_types=1);

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Variant;
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
        'checkoutable',
        'taxes',
    ];

    /**
     * The buyable types.
     */
    protected array $buyableTypes = [];

    /**
     * The buyable searchable columns.
     */
    protected array $buyableSearchableColumns = [];

    /**
     * The buyable item display resolver.
     */
    protected ?Closure $buyableItemDisplayResolver = null;

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Products'), $modelAttribute ?: 'items', $relation);

        $this->display('name');
        $this->asSubResource();

        $this->buyableTypes([
            Product::getProxiedClass(),
            Variant::getProxiedClass(),
        ]);

        $this->buyableSearchableColumns([
            Product::getProxiedClass() => ['id', 'name', 'slug', 'description'],
            Variant::getProxiedClass() => ['id', 'alias', 'description'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            MorphTo::make(__('Buyable Item'), 'buyable')
                ->required()
                ->async()
                ->searchable(columns: $this->buyableSearchableColumns)
                ->types($this->buyableTypes)
                ->display(function (Model $buyable) use ($request): string {
                    return $this->resolveBuyableItemDisplay($request, $buyable);
                }),

            Text::make(__('Name'), 'name')
                ->required(),

            Number::make(__('Price'), 'price')
                ->min(0)
                ->required()
                ->format(static function (Request $request, Model $model, ?float $value): string {
                    return $model->checkoutable->getCurrency()->format($value ?? 0);
                }),

            Number::make(__('Tax'), function (Request $request, Model $model): float {
                return $model->getTax();
            })->format(static function (Request $request, Model $model, float $value): string {
                return $model->checkoutable->getCurrency()->format($value ?? 0);
            }),

            Number::make(__('Quantity'), 'quantity')
                ->required()
                ->default(1)
                ->rules(['required', 'numeric', 'gt:0'])
                ->min(0),

            Number::make(__('Total'), function (Request $request, Model $model): float {
                return $model->getTotal();
            })->format(static function (Request $request, Model $model, float $value): string {
                return $model->checkoutable->getCurrency()->format($value ?? 0);
            }),
        ];
    }

    /**
     * Set the buyable item display resolver.
     */
    public function displayBuyableItem(Closure $callback): static
    {
        $this->buyableItemDisplayResolver = $callback;

        return $this;
    }

    /**
     * Resolve the buyable item display.
     */
    public function resolveBuyableItemDisplay(Request $request, Model $buyable): string
    {
        $callback = $this->buyableItemDisplayResolver ?: static function (Request $request, Model $buyable): string {
            return (string) match ($buyable::class) {
                Product::getProxiedClass() => $buyable->name,
                Variant::getProxiedClass() => $buyable->alias,
                default => $buyable->getKey(),
            };
        };

        return call_user_func_array($callback, [$request, $buyable]);
    }

    /**
     * Set the buyable types.
     */
    public function buyableTypes(array $types): static
    {
        $this->buyableTypes = array_merge($this->buyableTypes, $types);

        return $this;
    }

    /**
     * Set the buyable searchable columns.
     */
    public function buyableSearchableColumns(array $columns): static
    {
        $this->buyableSearchableColumns = array_merge($this->buyableSearchableColumns, $columns);

        return $this;
    }
}
