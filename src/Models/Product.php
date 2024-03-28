<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\ProductFactory;
use Cone\Bazar\Interfaces\Itemable;
use Cone\Bazar\Interfaces\Models\Product as Contract;
use Cone\Bazar\Traits\HasPrices;
use Cone\Bazar\Traits\HasProperties;
use Cone\Bazar\Traits\InteractsWithItemables;
use Cone\Bazar\Traits\InteractsWithStock;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\HasMetaData;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Product extends Model implements Contract
{
    use HasFactory;
    use HasMedia;
    use HasMetaData;
    use HasPrices;
    use HasProperties;
    use InteractsWithItemables;
    use InteractsWithProxy;
    use InteractsWithStock;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'description',
        'name',
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
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the categories for the product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::getProxiedClass(), 'bazar_category_product');
    }

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(Variant::getProxiedClass());
    }

    /**
     * Scope the query only to the models that are out of stock.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->whereHas('metaData', static function (Builder $query): Builder {
            return $query->where($query->qualifyColumn('key'), 'quantity')
                ->where($query->qualifyColumn('value'), 0);
        });
    }

    /**
     * Scope the query only to the models that are in stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas('metaData', static function (Builder $query): Builder {
            return $query->where($query->qualifyColumn('key'), 'quantity')
                ->where($query->qualifyColumn('value'), '>', 0);
        });
    }

    /**
     * Get the variant of the given option.
     */
    public function toVariant(array $variation): ?Variant
    {
        return $this->variants()
            ->getQuery()
            ->whereHas(
                'propertyValues',
                static function (Builder $query) use ($variation): Builder {
                    return $query->whereIn($query->qualifyColumn('value'), $variation)
                        ->whereHas('property', static function (Builder $query) use ($variation): Builder {
                            return $query->whereIn($query->qualifyColumn('slug'), array_keys($variation));
                        });
                },
                '=',
                function (QueryBuilder $query): QueryBuilder {
                    return $query->selectRaw('count(*)')
                        ->from('bazar_buyable_property_value')
                        ->whereIn('bazar_buyable_property_value.buyable_id', $this->variants()->select('bazar_variants.id'))
                        ->where('bazar_buyable_property_value.buyable_type', Variant::getProxiedClass());
                }
            )
            ->first();
    }

    /**
     * Get the item representation of the buyable instance.
     */
    public function toItem(Itemable $itemable, array $attributes = []): Item
    {
        if ($variant = $this->toVariant($attributes['properties'] ?? [])) {
            return $variant->toItem($itemable, $attributes);
        }

        return $this->items()->make(array_merge([
            'name' => $this->name,
            'price' => $this->getPrice($itemable->getCurrency()),
            'quantity' => 1,
        ], $attributes))->setRelation('buyable', $this);
    }
}
