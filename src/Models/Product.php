<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Casts\Inventory;
use Cone\Bazar\Casts\Prices;
use Cone\Bazar\Database\Factories\ProductFactory;
use Cone\Bazar\Interfaces\Itemable;
use Cone\Bazar\Interfaces\Models\Product as Contract;
use Cone\Bazar\Resources\ProductResource;
use Cone\Bazar\Traits\InteractsWithItemables;
use Cone\Bazar\Traits\InteractsWithStock;
use Cone\Bazar\Traits\Sluggable;
use Cone\Root\Interfaces\Resourceable;
use Cone\Root\Resources\Resource;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\InteractsWithProxy;
use Cone\Root\Traits\InteractsWithResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Product extends Model implements Contract, Resourceable
{
    use HasFactory;
    use HasMedia;
    use InteractsWithItemables;
    use InteractsWithProxy;
    use InteractsWithResource;
    use InteractsWithStock;
    use Sluggable;
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'formatted_price',
        'price',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'inventory' => '[]',
        'prices' => '[]',
        'properties' => '[]',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'inventory' => Inventory::class,
        'prices' => Prices::class,
        'properties' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'inventory',
        'name',
        'prices',
        'properties',
        'slug',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_products';

    /**
     * Get the proxied interface.
     *
     * @return string
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
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
            return $query->where($query->qualifyColumn('id'), $value);
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
     * @return \Cone\Bazar\Models\Variant|null
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
     * Get the item representation of the buyable instance.
     *
     * @param  \Cone\Bazar\Interfaces\Itemable  $itemable
     * @param  array  $attributes
     * @return \Cone\Bazar\Models\Item
     */
    public function toItem(Itemable $itemable, array $attributes = []): Item
    {
        if ($variant = $this->toVariant($attributes['properties'] ?? [])) {
            return $variant->toItem($itemable, $attributes);
        }

        return $this->items()->make(array_merge($attributes, [
            'name' => $this->name,
            'price' => $this->getPrice('sale', $itemable->getCurrency())
                    ?: $this->getPrice('default', $itemable->getCurrency())
        ]))->setRelation('buyable', $this);
    }

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource
    {
        return new ProductResource(static::class);
    }
}
