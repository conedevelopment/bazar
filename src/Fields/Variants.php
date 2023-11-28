<?php

namespace Cone\Bazar\Fields;

use Closure;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Illuminate\Http\Request;

class Variants extends HasMany
{
    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'metaData',
    ];

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label = null, Closure|string $modelAttribute = null, Closure|string $relation = null)
    {
        parent::__construct($label ?: __('Variants'), $modelAttribute ?: 'variants', $relation);

        $this->asSubResource();
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Alias'), 'alias'),

            Inventory::make(),
        ];
    }
}
