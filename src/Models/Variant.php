<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Casts\Inventory;
use Bazar\Casts\Prices;
use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Filterable;
use Bazar\Concerns\HasMedia;
use Bazar\Concerns\InteractsWithProxy;
use Bazar\Concerns\InteractsWithStock;
use Bazar\Contracts\Models\Variant as Contract;
use Bazar\Database\Factories\VariantFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Variant extends Model implements Contract
{
    use BazarRoutable;
    use Filterable;
    use HasFactory;
    use HasMedia;
    use InteractsWithProxy;
    use InteractsWithStock;
    use SoftDeletes;

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
        'variation' => '[]',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'variation' => 'json',
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
        'prices',
        'inventory',
        'variation',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_variants';

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
     * @return \Bazar\Database\Factories\VariantFactory
     */
    protected static function newFactory(): VariantFactory
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
     * Get the breadcrumb representation of the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function toBreadcrumb(Request $request): string
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
    public function price(string $type = 'default', ?string $currency = null): ?float
    {
        $currency = $currency ?: Bazar::currency();

        return $this->prices->get("{$currency}.{$type}") ?: $this->product->price($type, $currency);
    }

    /**
     * Get the formatted price by the given type and currency.
     *
     * @param  string  $type
     * @param  string|null  $currency
     * @return string|null
     */
    public function formattedPrice(string $type = 'default', ?string $currency = null): ?string
    {
        $currency = $currency ?: Bazar::currency();

        return $this->prices->format("{$currency}.{$type}") ?: $this->product->prices->format("{$currency}.{$type}");
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
}
