<?php

namespace Bazar\Models;

use Bazar\Casts\Inventory;
use Bazar\Casts\Prices;
use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Filterable;
use Bazar\Concerns\HasMedia;
use Bazar\Concerns\InteractsWithProxy;
use Bazar\Concerns\InteractsWithStock;
use Bazar\Concerns\Sluggable;
use Bazar\Contracts\Models\Product as Contract;
use Bazar\Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Product extends Model implements Contract
{
    use BazarRoutable;
    use HasFactory;
    use InteractsWithProxy;
    use InteractsWithStock;
    use HasMedia;
    use Sluggable;
    use SoftDeletes;
    use Filterable {
        Filterable::filters as defaultFilters;
    }

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
        'inventory' => '[]',
        'properties' => '[]',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'json',
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
        'inventory',
        'properties',
        'description',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_products';

    /**
     * Get the proxied contract.
     *
     * @return string
     */
    public static function getProxiedContract(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Bazar\Database\Factories\ProductFactory
     */
    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    /**
     * Get the filter options for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function filters(Request $request): array
    {
        return array_merge(static::defaultFilters($request), [
            'category' => array_map('__', Category::proxy()->newQuery()->pluck('id', 'name')->toArray()),
        ]);
    }

    /**
     * Get the items for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::getProxiedClass());
    }

    /**
     * Get the products for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::getProxiedClass(), Item::getProxiedClass(), 'product_id', 'id', 'id', 'itemable_id')
                    ->where('itemable_type', Order::getProxiedClass());
    }

    /**
     * Get the carts for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function carts(): HasManyThrough
    {
        return $this->hasManyThrough(Cart::getProxiedClass(), Item::getProxiedClass(), 'product_id', 'id', 'id', 'itemable_id')
                    ->where('itemable_type', Cart::getProxiedClass());
    }

    /**
     * Get the categories for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::getProxiedClass(), 'bazar_category_product');
    }

    /**
     * Get the variants for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants(): HasMany
    {
        return $this->hasMany(Variant::getProxiedClass());
    }

    /**
     * Get the variants attribute.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getVariantsAttribute(): Collection
    {
        return $this->getRelationValue('variants')->each(function (Variant $variant): void {
            $variant->setRelation('product', $this->withoutRelations()->makeHidden('variants'))
                    ->makeHidden('product');
        });
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
            return $this->variants()
                        ->where($field ?: $this->variants()->getRelated()->getRouteKeyName(), $value)
                        ->withTrashed()
                        ->first();
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
            return $query->where($query->qualifyColumn('name'), 'like', "{$value}%")
                         ->orWhere($query->qualifyColumn('inventory->sku'), 'like', "{$value}%");
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

    /**
     * Scope the query only to the models that are out of stock.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('inventory->quantity'), '=', 0);
    }

    /**
     * Scope the query only to the models that are in stock.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('inventory->quantity'), '>', 0);
    }

    /**
     * Get the variant of the given option.
     *
     * @param  array  $variation
     * @return \Bazar\Models\Variant|null
     */
    public function toVariant(array $variation): ?Variant
    {
        return $this->variants->sortBy(static function (Variant $variant): int {
            return array_count_values($variant->variation)['*'] ?? 0;
        })->first(function (Variant $variant) use ($variation): bool {
            $variation = array_replace(array_fill_keys(array_keys($this->properties), '*'), $variation);

            foreach ($variant->variation as $key => $value) {
                if ($value === '*') {
                    $variation[$key] = $value;
                }
            }

            return empty(array_diff_assoc(array_intersect_key($variant->variation, $variation), $variation));
        });
    }

    /**
     * Get the breadcrumb representation of the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function toBreadcrumb(Request $request): string
    {
        return $this->name;
    }
}
