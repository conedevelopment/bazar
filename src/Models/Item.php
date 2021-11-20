<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Traits\HasUuid;
use Cone\Bazar\Traits\InteractsWithTaxes;
use Cone\Bazar\Interfaces\Models\Item as Contract;
use Cone\Bazar\Database\Factories\ItemFactory;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Item extends Model implements Contract
{
    use HasFactory;
    use HasUuid;
    use InteractsWithProxy;
    use InteractsWithTaxes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'net_total',
        'total',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'price' => 0,
        'properties' => '[]',
        'quantity' => 0,
        'tax' => 0,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'properties' => 'json',
        'quantity' => 'float',
        'tax' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'properties',
        'quantity',
        'tax',
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
        'itemable',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_items';

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
        return ItemFactory::new();
    }

    /**
     * Get the buyable model for the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function buyable(): MorphTo
    {
        return $this->morphTo();
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
     * Get the formatted price attribute.
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->getFormattedPrice();
    }

    /**
     * Get the total attribute.
     *
     * @return float
     */
    public function getTotalAttribute(): float
    {
        return $this->getTotal();
    }

    /**
     * Get the formatted total attribute.
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->getFormattedTotal();
    }

    /**
     * Get the net total attribute.
     *
     * @return float
     */
    public function getNetTotalAttribute(): float
    {
        return $this->getNetTotal();
    }

    /**
     * Get the formatted net total attribute.
     *
     * @return string
     */
    public function getFormattedNetTotalAttribute(): string
    {
        return $this->getFormattedNetTotal();
    }

    /**
     * Get the price.
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Get the formatted price.
     *
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return Str::currency($this->getPrice(), $this->itemable->getCurrency());
    }

    /**
     * Get the total.
     *
     * @return float
     */
    public function getTotal(): float
    {
        return ($this->getPrice() + $this->getTax()) * $this->getQuantity();
    }

    /**
     * Get the formatted total.
     *
     * @return string
     */
    public function getFormattedTotal(): string
    {
        return Str::currency($this->getTotal(), $this->itemable->getCurrency());
    }

    /**
     * Get the net total.
     *
     * @return float
     */
    public function getNetTotal(): float
    {
        return $this->getPrice() * $this->getQuantity();
    }

    /**
     * Get the formatted net total.
     *
     * @return string
     */
    public function getFormattedNetTotal(): string
    {
        return Str::currency($this->getNetTotal(), $this->itemable->getCurrency());
    }

    /**
     * Get the quantity.
     *
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }
}
