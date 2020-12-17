<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Filterable;
use Bazar\Concerns\HasMedia;
use Bazar\Concerns\InteractsWithStock;
use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Models\Variant as Contract;
use Bazar\Contracts\Stockable;
use Bazar\Proxies\Product as ProductProxy;
use Bazar\Support\Bags\Inventory;
use Bazar\Support\Bags\Prices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Variant extends Model implements Breadcrumbable, Contract, Stockable
{
    use BazarRoutable, Filterable, InteractsWithStock, HasMedia, SoftDeletes;

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
        'option' => '[]',
        'prices' => '[]',
        'inventory' => '[]',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'option' => 'json',
        'prices' => Prices::class,
        'inventory' => Inventory::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'alias',
        'option',
        'prices',
        'inventory',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_variants';

    /**
     * Get the product for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::getProxiedClass());
    }

    /**
     * Get the alias attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getAliasAttribute(string $value = null): ?string
    {
        return $this->exists ? ($value ?: "#{$this->id}") : $value;
    }

    /**
     * Get the option attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getOptionAttribute(string $value): array
    {
        $value = $this->castAttribute('option', $value);

        return $this->relationLoaded('product') ? array_replace(
            array_fill_keys(array_keys($this->product->options), '*'), $value
        ) : $value;
    }

    /**
     * Get the breadcrumb label.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function getBreadcrumbLabel(Request $request): string
    {
        return $this->alias;
    }

    /**
     * Get the price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return float|null
     */
    public function price(string $type = 'default', string $currency = null): ?float
    {
        $currency = $currency ?: Bazar::currency();

        $price = $this->prices[$currency][$type];

        return $price ?: $this->product->price($type, $currency);
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
        return $query->where('alias', 'like', "{$value}%");
    }
}
