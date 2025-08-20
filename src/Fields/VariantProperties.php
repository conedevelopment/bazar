<?php

declare(strict_types=1);

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Root\Fields\MorphToMany;
use Cone\Root\Fields\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class VariantProperties extends MorphToMany
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.fieldset';

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'property',
    ];

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        parent::__construct($label, $modelAttribute, $relation);

        $this->display('name');
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        $value = (array) $request->input($this->getRequestKey());

        $value = array_filter($value, static function (mixed $value): bool {
            return ! is_null($value);
        });

        return $this->mergePivotValues(array_values($value));
    }

    /**
     * Resolve the property fields.
     */
    public function resolvePropertyFields(Request $request, Model $model): array
    {
        $values = $this->resolveRelatableQuery($request, $model)->get();

        $value = $this->resolveValue($request, $model);

        return $values->groupBy('property_id')
            ->map(function (Collection $group) use ($request, $model, $value): array {
                return Select::make($group->first()->property->name, $this->modelAttribute.'.'.$group->first()->property->slug)
                    ->value(static function () use ($value, $group): ?int {
                        return $value->firstWhere('property_id', $group->first()->property_id)?->getKey();
                    })
                    ->options($group->pluck('name', 'id')->toArray())
                    ->nullable()
                    ->toInput($request, $model);
            })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRelatableQuery(Request $request, Model $model): Builder
    {
        $query = parent::resolveRelatableQuery($request, $model);

        $product = $model->relationLoaded('product')
            ? $model->product
            : $model->product()->make()->forceFill(['id' => $model->product_id]);

        return $query->whereIn(
            $query->qualifyColumn('id'),
            $product->propertyValues()->select('bazar_property_values.id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'fields' => $this->resolvePropertyFields($request, $model),
        ]);
    }
}
