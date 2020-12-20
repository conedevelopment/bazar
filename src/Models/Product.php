<?php

namespace Bazar\Models;

use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Filterable;
use Bazar\Concerns\HasMedia;
use Bazar\Concerns\InteractsWithStock;
use Bazar\Concerns\Sluggable;
use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Models\Product as Contract;
use Bazar\Contracts\Models\Variant;
use Bazar\Contracts\Stockable;
use Bazar\Proxies\Cart as CartProxy;
use Bazar\Proxies\Category as CategoryProxy;
use Bazar\Proxies\Order as OrderProxy;
use Bazar\Proxies\Variant as VariantProxy;
use Bazar\Support\Bags\Inventory;
use Bazar\Support\Bags\Prices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Product extends Model implements Breadcrumbable, Contract, Stockable
{
    use BazarRoutable, Filterable, InteractsWithStock, HasMedia, Sluggable, SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'price',
        'formatted_price',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'prices' => '[]',
        'options' => '[]',
        'inventory' => '[]',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'json',
        'prices' => Prices::class,
        'inventory' => Inventory::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'prices',
        'options',
        'inventory',
        'description',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_products';

    /**
     * Get the filter options for the model.
     *
     * @return array
     */
    public static function filters(): array
    {
        return [
            'state' => [
                'all' => __('All'),
                'available' => __('Available'),
                'trashed' => __('Trashed')
            ],
            'category' => array_map('__', CategoryProxy::query()->pluck('name', 'id')->toArray()),
        ];
    }

    /**
     * Get the orders for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function orders(): MorphToMany
    {
        return $this->morphedByMany(OrderProxy::getProxiedClass(), 'itemable', 'bazar_items')
                    ->withPivot(['id', 'price', 'tax', 'quantity', 'properties'])
                    ->withTimestamps()
                    ->as('item')
                    ->using(Item::class);
    }

    /**
     * Get the carts for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function carts(): MorphToMany
    {
        return $this->morphedByMany(CartProxy::getProxiedClass(), 'itemable', 'bazar_items')
                    ->withPivot(['id', 'price', 'tax', 'quantity', 'properties'])
                    ->withTimestamps()
                    ->as('item')
                    ->using(Item::class);
    }

    /**
     * Get the categories for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CategoryProxy::getProxiedClass(), 'bazar_category_product');
    }

    /**
     * Get the variants for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants(): HasMany
    {
        return $this->hasMany(VariantProxy::getProxiedClass());
    }

    /**
     * Get the variants attribute.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getVariantsAttribute(): Collection
    {
        return $this->getRelationValue('variants')->each(function (Variant $variant): void {
            $variant->setRelation(
                'product', $this->withoutRelations()->makeHidden('variants')
            )->makeHidden('product');
        });
    }

    /**
     * Get the variant of the given option.
     *
     * @param  array  $option
     * @return \Bazar\Contracts\Models\Variant|null
     */
    public function variant(array $option): ?Variant
    {
        return $this->variants->sortBy(static function (Variant $variant): int {
            return array_count_values($variant->option)['*'] ?? 0;
        })->first(function (Variant $variant) use ($option): bool {
            $option = array_replace(array_fill_keys(array_keys($this->options), '*'), $option);

            foreach ($variant->option as $key => $value) {
                if ($value === '*') {
                    $option[$key] = $value;
                }
            }

            return empty(array_diff(array_intersect_key($variant->option, $option), $option));
        });
    }

    /**
     * Get the breadcrumb label.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function getBreadcrumbLabel(Request $request): string
    {
        return $this->name;
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveChildRouteBinding($childType, $value, $field): ?Model
    {
        if ($childType === 'variant' && preg_match('/bazar/', Route::getCurrentRoute()->getName())) {
            return $this->variants()->where(
                $field ?: $this->variants()->getRelated()->getRouteKeyName(), $value
            )->withTrashed()->firstOrFail();
        }

        return parent::resolveChildRouteBinding($childType, $value, $field);
    }

    /**
     * Scope the query only to the given search term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where(static function (Builder $query) use ($value): Builder {
            return $query->where('name', 'like', "{$value}%")
                        ->orWhere('inventory->sku', 'like', "{$value}%");
        });
    }

    /**
     * Scope the query only to the given category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategory(Builder $query, int $value): Builder
    {
        return $query->whereHas('categories', static function (Builder $query) use ($value): Builder {
            return $query->where($query->getModel()->qualifyColumn('id'), $value);
        });
    }
}
