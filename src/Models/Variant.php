<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Bazar;
use Cone\Bazar\Casts\Inventory;
use Cone\Bazar\Casts\Prices;
use Cone\Bazar\Database\Factories\VariantFactory;
use Cone\Bazar\Interfaces\Itemable;
use Cone\Bazar\Interfaces\Models\Variant as Contract;
use Cone\Bazar\Traits\InteractsWithItemables;
use Cone\Bazar\Traits\InteractsWithStock;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model implements Contract
{
    use HasFactory;
    use HasMedia;
    use InteractsWithItemables;
    use InteractsWithProxy;
    use InteractsWithStock;
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
        'variation' => '[]',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'inventory' => Inventory::class,
        'prices' => Prices::class,
        'variation' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'alias',
        'inventory',
        'prices',
        'variation',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_variants';

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
        return VariantFactory::new();
    }

    /**
     * Get the product for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::getProxiedClass());
    }

    /**
     * Get the alias attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getAliasAttribute(?string $value = null): ?string
    {
        return $this->exists ? ($value ?: "#{$this->id}") : $value;
    }

    /**
     * Get the variation attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getVariationAttribute(string $value): array
    {
        return $this->relationLoaded('product')
            ? array_replace(
                array_fill_keys(array_keys($this->product->properties), '*'),
                $this->castAttribute('variation', $value)
            )
            : $this->castAttribute('variation', $value);
    }

    /**
     * Get the price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return float|null
     */
    public function getPrice(string $type = 'default', ?string $currency = null): ?float
    {
        $currency = $currency ?: Bazar::getCurrency();

        return $this->prices->get("{$currency}.{$type}")
            ?: $this->product->getPrice($type, $currency);
    }

    /**
     * Get the formatted price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return string|null
     */
    public function getFormattedPrice(string $type = 'default', ?string $currency = null): ?string
    {
        $currency = $currency ?: Bazar::getCurrency();

        return $this->prices->format("{$currency}.{$type}")
            ?: $this->product->prices->format("{$currency}.{$type}");
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
        return $query->where($query->qualifyColumn('alias'), 'like', "{$value}%");
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
        return $this->items()->make(array_merge($attributes, [
            'name' => sprintf('%s - %s', $this->name, $this->alias),
            'price' => $this->getPrice('sale', $itemable->getCurrency())
                    ?: $this->getPrice('default', $itemable->getCurrency())
        ]))->setRelation('buyable', $this);
    }
}
