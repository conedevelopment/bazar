<?php

namespace Bazar\Models;

use Bazar\Concerns\InteractsWithTaxes;
use Bazar\Contracts\Models\Cart;
use Bazar\Contracts\Stockable;
use Bazar\Contracts\Taxable;
use Bazar\Proxies\Product as ProductProxy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class Item extends MorphPivot implements Taxable
{
    use InteractsWithTaxes;

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
        'properties' => '{"option": {}}',
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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'product',
        'itemable',
    ];

    /**
     * The registered property resolver callbacks.
     *
     * @var array
     */
    protected static $propertyResolvers = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_items';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(static function (self $item): void {
            $item->id = Uuid::uuid4();
        });

        static::saving(static function (self $item): void {
            $item->resolveProperties()->tax(false);
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
        return $this->belongsTo(ProductProxy::getProxiedClass());
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
     * @return \Bazar\Contracts\Stockable
     */
    public function getStockableAttribute(): Stockable
    {
        // Determine if product or its variation

        return $this->product;
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
     * Get the option property of the item.
     *
     * @return array
     */
    public function getOptionAttribute(): array
    {
        return $this->properties['option'] ?? [];
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
     * Resolve the option property.
     *
     * @return void
     */
    protected function resolveOptionProperty(): void
    {
        $item = $this->product->variation($this->option) ?: $this->product;

        $stock = $item->inventory->quantity;

        $this->price = $item->price('sale', $this->itemable->currency) ?: $item->price('default', $this->itemable->currency);

        $this->quantity = (is_null($stock) || $stock >= $this->quantity) ? $this->quantity : $stock;
    }

    /**
     * Resolve the registered properties.
     *
     * @return $this
     */
    public function resolveProperties(): Item
    {
        if (in_array(Cart::class, class_implements($this->itemable_type))) {
            $this->resolveOptionProperty();

            foreach ((array) $this->properties as $name => $value) {
                if ($name !== 'option' && ($resolver = static::$propertyResolvers[$name] ?? null)) {
                    call_user_func_array($resolver, [$this, $value]);
                }
            }
        }

        return $this;
    }
}
