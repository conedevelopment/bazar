<?php

namespace Bazar\Models;

use Bazar\Concerns\HasUuid;
use Bazar\Concerns\InteractsWithTaxes;
use Bazar\Contracts\Models\Item as Contract;
use Bazar\Contracts\Stockable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Item extends MorphPivot implements Contract
{
    use InteractsWithTaxes;
    use HasUuid;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'total',
        'net_total',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'tax' => 0,
        'price' => 0,
        'quantity' => 0,
        'properties' => '[]',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'quantity' => 'float',
        'properties' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tax',
        'price',
        'quantity',
        'properties',
    ];

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'product',
        'itemable',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_items';

    /**
     * The registered property resolver callbacks.
     *
     * @var array
     */
    protected static $propertyResolvers = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(static function (self $item): void {
            if ($item->itemable_type === Cart::class || is_subclass_of($item->itemable_type, Cart::class)) {
                $item->fillFromStockable()->resolveProperties()->tax(false);
            }
        });
    }

    /**
     * Define a property resolver.
     *
     * @param  string  $name
     * @param  callable  $callback
     * @return void
     */
    public static function resolvePropertyUsing(string $name, callable $callback): void
    {
        static::$propertyResolvers[$name] = $callback;
    }

    /**
     * Get the product for the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::getProxiedClass());
    }

    /**
     * Get the itemable model for the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the stockable attribute.
     *
     * @return \Bazar\Contracts\Stockable|null
     */
    public function getStockableAttribute(): ?Stockable
    {
        if (! $product = $this->product) {
            return null;
        }

        return $product->toVariant((array) $this->properties) ?: $product;
    }

    /**
     * Get the formatted price attribute.
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->formattedPrice();
    }

    /**
     * Get the total attribute.
     *
     * @return float
     */
    public function getTotalAttribute(): float
    {
        return $this->total();
    }

    /**
     * Get the formatted total attribute.
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->formattedTotal();
    }

    /**
     * Get the net total attribute.
     *
     * @return float
     */
    public function getNetTotalAttribute(): float
    {
        return $this->netTotal();
    }

    /**
     * Get the formatted net total attribute.
     *
     * @return string
     */
    public function getFormattedNetTotalAttribute(): string
    {
        return $this->formattedNetTotal();
    }

    /**
     * Get the item's price.
     *
     * @return float
     */
    public function price(): float
    {
        return $this->price;
    }

    /**
     * Get the item's formatted price.
     *
     * @return string
     */
    public function formattedPrice(): string
    {
        return Str::currency($this->price(), $this->itemable->currency);
    }

    /**
     * Get the item's total.
     *
     * @return float
     */
    public function total(): float
    {
        return ($this->price + $this->tax) * $this->quantity;
    }

    /**
     * Get the item's formatted total.
     *
     * @return string
     */
    public function formattedTotal(): string
    {
        return Str::currency($this->total(), $this->itemable->currency);
    }

    /**
     * Get the item's net total.
     *
     * @return float
     */
    public function netTotal(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get the item's formatted net total.
     *
     * @return string
     */
    public function formattedNetTotal(): string
    {
        return Str::currency($this->netTotal(), $this->itemable->currency);
    }

    /**
     * Fill the properties from the stockable model.
     *
     * @return $this
     */
    protected function fillFromStockable(): Item
    {
        if ($stockable = $this->stockable) {
            $this->price = $stockable->price('sale', $this->itemable->currency)
                        ?: $stockable->price('default', $this->itemable->currency);

            $stock = $stockable->inventory['quantity'] ?? null;

            $this->quantity = (is_null($stock) || $stock >= $this->quantity) ? $this->quantity : $stock;
        }

        return $this;
    }

    /**
     * Resolve the registered properties.
     *
     * @return $this
     */
    public function resolveProperties(): Item
    {
        foreach ((array) $this->properties as $name => $value) {
            if ($resolver = static::$propertyResolvers[$name] ?? null) {
                call_user_func_array($resolver, [$this, $value]);
            }
        }

        return $this;
    }
}
