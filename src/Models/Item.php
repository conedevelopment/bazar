<?php

namespace Bazar\Models;

use Bazar\Concerns\InteractsWithTaxes;
use Bazar\Contracts\Taxable;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
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
    protected $table = 'items';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(static function (Item $item) {
            $item->id = Uuid::uuid4();
        });

        static::saving(static function (Item $item) {
            if ($item->itemable_type === Cart::class && $item->product instanceof Product) {
                foreach (array_replace(['option' => []], $item->properties) as $name => $value) {
                    if ($resolver = static::$propertyResolvers[$name] ?? null) {
                        call_user_func_array($resolver, [$item, $value]);
                    }
                }
            }

            $item->tax(false);
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
        return $this->property('option', []);
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
        return Str::currency($this->price(), $this->pivotParent->currency);
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
        return Str::currency($this->total(), $this->pivotParent->currency);
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
        return Str::currency($this->netTotal(), $this->pivotParent->currency);
    }

    /**
     * Get the property by its name.
     *
     * @param  string  $name
     * @param  mixed  $default
     * @return mixed
     */
    public function property(string $name, $default = null)
    {
        return $this->properties[$name] ?? $default;
    }
}
