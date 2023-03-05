<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Bazar\Models\Variant;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\Media;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Builder;

class Variants extends HasMany
{
    /**
     * Create a new variants field instance.
     */
    public function __construct(string $label = null, string $name = null, Closure|string $relation = null)
    {
        parent::__construct(
            $label ?: __('Variants'), $name ?: 'variants', $relation
        );

        $this->asSubResource();
        $this->hiddenOnDisplay();
        $this->display('alias');
    }

    /**
     * Define the fields for the object.
     */
    public function fields(RootRequest $request): array
    {
        return [
            Text::make(__('Alias'), 'alias')->rules(['required']),

            BelongsToMany::make(__('Properties'), 'propertyValues')
                ->withQuery(static function (RootRequest $request, Builder $query, Variant $model): Builder {
                    return $query->whereIn(
                        $query->getModel()->getQualifiedKeyName(),
                        $model->parent->propertyValues()->select('bazar_property_values.id')
                    )->with('property');
                })
                ->groupOptionsBy('property.name')
                ->display('name'),

            Inventory::make(__('Inventory'), 'inventory')
                ->hiddenOnDisplay(),

            Media::make(__('Media')),
        ];
    }
}
