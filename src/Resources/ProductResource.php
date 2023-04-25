<?php

namespace Cone\Bazar\Resources;

use Cone\Root\Fields\BelongsToMany as BelongsToManyField;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Media;
use Cone\Root\Fields\Text;
use Cone\Root\Filters\TrashStatus;
use Cone\Root\Relations\BelongsToMany;
use Cone\Root\Relations\HasMany;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductResource extends Resource
{
    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'metaData',
    ];

    /**
     * Define the filters for the resource.
     */
    public function filters(Request $request): array
    {
        return array_merge(parent::filters($request), [
            TrashStatus::make(),
        ]);
    }

    /**
     * Define the fields for the resource.
     */
    public function fields(Request $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),

            Text::make(__('Name'), 'name')->sortable(),

            Editor::make(__('Description'), 'description')
                ->withMedia(),

            BelongsToManyField::make(__('Categories'), 'categories')
                ->display('name')
                ->searchable()
                ->async(),

            Media::make(__('Media'), 'media')
                ->display('name'),

            BelongsToManyField::make(__('Properties'), 'propertyValues')
                ->withRelatableQuery(static function (Request $request, Builder $query): Builder {
                    return $query->with('property');
                })
                ->groupOptionsBy('property.name')
                ->display('name'),
        ]);
    }

    /**
     * Define the relations for the resource.
     */
    public function relations(Request $request): array
    {
        return array_merge(parent::relations($request), [
            HasMany::make(__('Variants'), 'variants')
                ->withFields([
                    Text::make(__('Alias'), 'alias')->rules(['required']),
                ]),
            BelongsToMany::make(__('Categories'), 'categories')
                ->withFields([
                    //
                ]),
        ]);
    }
}
