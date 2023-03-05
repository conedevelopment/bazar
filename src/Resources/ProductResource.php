<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Fields\Inventory;
use Cone\Bazar\Fields\Prices;
use Cone\Bazar\Fields\Variants;
use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Editor;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Media;
use Cone\Root\Fields\Text;
use Cone\Root\Filters\TrashStatus;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'metas',
    ];

    /**
     * Define the filters for the resource.
     */
    public function filters(RootRequest $request): array
    {
        return array_merge(parent::filters($request), [
            TrashStatus::make(),
        ]);
    }

    /**
     * Define the fields for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(parent::fields($request), [
            ID::make(),

            Text::make(__('Name'), 'name')->sortable(),

            Editor::make(__('Description'), 'description')
                ->withMedia()
                ->hiddenOnIndex(),

            BelongsToMany::make(__('Categories'), 'categories')
                ->display('name')
                ->searchable()
                ->async(),

            Media::make(__('Media'), 'media')
                ->display('name')
                ->hiddenOnIndex(),

            Prices::make(),

            Inventory::make(),

            Variants::make(),

            BelongsToMany::make(__('Properties'), 'propertyValues')
                ->withQuery(static function (RootRequest $request, Builder $query): Builder {
                    return $query->with('property');
                })
                ->groupOptionsBy('property.name')
                ->display('name'),
        ]);
    }
}
