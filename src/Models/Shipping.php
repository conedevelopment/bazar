<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Concerns\Addressable;
use Cone\Bazar\Concerns\InteractsWithProxy;
use Cone\Bazar\Concerns\InteractsWithTaxes;
use Cone\Bazar\Contracts\Models\Shipping as Contract;
use Cone\Bazar\Database\Factories\ShippingFactory;
use Cone\Bazar\Support\Facades\Shipping as Manager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Throwable;

class Shipping extends Model implements Contract
{
    use Addressable;
    use HasFactory;
    use InteractsWithProxy;
    use InteractsWithTaxes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'driver_name',
        'formatted_total',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'tax' => 0,
        'cost' => 0,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tax' => 'float',
        'cost' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tax',
        'cost',
        'driver',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_shippings';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(static function (self $shipping): void {
            $shipping->driver = $shipping->driver ?: Manager::getDefaultDriver();
        });
    }

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
     * @return \Cone\Bazar\Database\Factories\ShippingFactory
     */
    protected static function newFactory(): ShippingFactory
    {
        return ShippingFactory::new();
    }

    /**
     * Get the shippable model for the shipping.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shippable(): MorphTo
    {
        return $this->morphTo()->withDefault(static function (): Cart {
            return Cart::proxy()->newInstance();
        });
    }

    /**
     * Get the driver attribute.
     *
     * @param  string|null  $value
     * @return string
     */
    public function getDriverAttribute(?string $value = null): string
    {
        return $value ?: Manager::getDefaultDriver();
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
     * Get the name of the shipping method.
     *
     * @return string
     */
    public function getDriverNameAttribute(): string
    {
        try {
            return Manager::driver($this->driver)->getName();
        } catch (Throwable $exception) {
            return $this->driver;
        }
    }

    /**
     * Get the price.
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->cost;
    }

    /**
     * Get the formatted price.
     *
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return Str::currency($this->getPrice(), $this->shippable->getCurrency());
    }

    /**
     * Get the shipping's total.
     *
     * @return float
     */
    public function getTotal(): float
    {
        return $this->getPrice() + $this->getTax();
    }

    /**
     * Get the shipping's formatted total.
     *
     * @return string
     */
    public function getFormattedTotal(): string
    {
        return Str::currency($this->getTotal(), $this->shippable->getCurrency());
    }

    /**
     * Get the shipping's net total.
     *
     * @return float
     */
    public function getNetTotal(): float
    {
        return $this->getPrice();
    }

    /**
     * Get the shipping's formatted net total.
     *
     * @return string
     */
    public function getFormattedNetTotal(): string
    {
        return Str::currency($this->getNetTotal(), $this->shippable->getCurrency());
    }

    /**
     * Get the quantity.
     *
     * @return float
     */
    public function getQuantity(): float
    {
        return 1;
    }

    /**
     * Calculate the cost.
     *
     * @param  bool  $update
     * @return float
     */
    public function calculateCost(bool $update = true): float
    {
        try {
            $this->cost = Manager::driver($this->driver)->calculate($this->shippable);

            if ($update) {
                $this->save();
            }
        } catch (Throwable $exception) {
            //
        }

        return $this->cost;
    }
}
