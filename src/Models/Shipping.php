<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\ShippingFactory;
use Cone\Bazar\Interfaces\Models\Shipping as Contract;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Support\Facades\Shipping as Manager;
use Cone\Bazar\Traits\Addressable;
use Cone\Bazar\Traits\InteractsWithTaxes;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Throwable;

class Shipping extends Model implements Contract
{
    use Addressable;
    use HasFactory;
    use InteractsWithProxy;
    use InteractsWithTaxes;

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'fee' => 0,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fee' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fee',
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
     */
    protected static function booted(): void
    {
        static::creating(static function (self $shipping): void {
            $shipping->driver = $shipping->driver ?: Manager::getDefaultDriver();
        });
    }

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
    protected static function newFactory(): ShippingFactory
    {
        return ShippingFactory::new();
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the shippable model for the shipping.
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
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function driver(): Attribute
    {
        return new Attribute(
            get: static function (?string $value = null): string {
                return $value ?: Manager::getDefaultDriver();
            }
        );
    }

    /**
     * Get the total attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<float, never>
     */
    protected function total(): Attribute
    {
        return new Attribute(
            get: function (): float {
                return $this->getTotal();
            }
        );
    }

    /**
     * Get the formatted total attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedTotal(): Attribute
    {
        return new Attribute(
            get: function (): string {
                return $this->getFormattedTotal();
            }
        );
    }

    /**
     * Get the subtotal attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<float, never>
     */
    protected function subtotal(): Attribute
    {
        return new Attribute(
            get: function (): float {
                return $this->getSubtotal();
            }
        );
    }

    /**
     * Get the formatted subtotal attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedSubtotal(): Attribute
    {
        return new Attribute(
            get: function (): string {
                return $this->getFormattedSubtotal();
            }
        );
    }

    /**
     * Get the name of the shipping method.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function driverName(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): string {
                try {
                    return Manager::driver($attributes['driver'])->getName();
                } catch (Throwable $exception) {
                    return $attributes['driver'];
                }
            }
        );
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return $this->driverName;
    }

    /**
     * Get the tax base.
     */
    public function getTaxBase(): float
    {
        return $this->fee;
    }

    /**
     * Get the price.
     */
    public function getPrice(): float
    {
        return $this->fee;
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPrice(): string
    {
        return (new Currency($this->getPrice(), $this->shippable->getCurrency()))->format();
    }

    /**
     * Get the gross price.
     */
    public function getGrossPrice(): float
    {
        return $this->getPrice() + $this->getTax();
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedGrossPrice(): string
    {
        return (new Currency($this->getGrossPrice(), $this->shippable->getCurrency()))->format();
    }

    /**
     * Get the shipping's total.
     */
    public function getTotal(): float
    {
        return $this->getGrossPrice() * $this->getQuantity();
    }

    /**
     * Get the shipping's formatted total.
     */
    public function getFormattedTotal(): string
    {
        return (new Currency($this->getTotal(), $this->shippable->getCurrency()))->format();
    }

    /**
     * Get the shipping's subtotal.
     */
    public function getSubtotal(): float
    {
        return $this->getPrice();
    }

    /**
     * Get the shipping's formatted subtotal.
     */
    public function getFormattedSubtotal(): string
    {
        return (new Currency($this->getSubtotal(), $this->shippable->getCurrency()))->format();
    }

    /**
     * Get the formatted tax total.
     */
    public function getFormattedTax(): string
    {
        return (new Currency($this->getTax(), $this->shippable->getCurrency()))->format();
    }

    /**
     * Get the formatted tax total.
     */
    public function getFormattedTaxTotal(): string
    {
        return (new Currency($this->getTaxTotal(), $this->shippable->getCurrency()))->format();
    }

    /**
     * Get the quantity.
     */
    public function getQuantity(): float
    {
        return 1;
    }

    /**
     * Calculate the fee.
     */
    public function calculateFee(): float
    {
        try {
            $this->fill([
                'fee' => Manager::driver($this->driver)->calculate($this->shippable),
            ])->save();
        } catch (Throwable $exception) {
            //
        }

        return $this->fee;
    }

    /**
     * Calculate the taxes.
     */
    public function calculateTaxes(): float
    {
        $taxes = TaxRate::proxy()
            ->newQuery()
            ->applicableForShipping()
            ->get()
            ->mapWithKeys(function (TaxRate $taxRate): array {
                return [$taxRate->getKey() => ['value' => $taxRate->calculate($this)]];
            });

        $this->taxes()->sync($taxes);

        return $this->getTaxTotal();
    }
}
