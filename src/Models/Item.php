<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\ItemFactory;
use Cone\Bazar\Interfaces\Buyable;
use Cone\Bazar\Interfaces\Models\Item as Contract;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Traits\InteractsWithTaxes;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Item extends Model implements Contract
{
    use HasFactory;
    use HasUuids;
    use InteractsWithProxy;
    use InteractsWithTaxes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'subtotal',
        'total',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'price' => 0,
        'properties' => '[]',
        'quantity' => 1,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'properties' => 'json',
        'quantity' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'buyable_id',
        'buyable_type',
        'name',
        'price',
        'properties',
        'quantity',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'checkoutable',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_items';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(static function (self $item): void {
            $item->taxes()->delete();
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
    protected static function newFactory(): ItemFactory
    {
        return ItemFactory::new();
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the buyable model for the item.
     */
    public function buyable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the checkoutable model for the item.
     */
    public function checkoutable(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    /**
     * Get the formatted price attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedPrice(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedPrice(),
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
            get: fn (): float => $this->getTotal()
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
            get: fn (): string => $this->getFormattedTotal()
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
            get: fn (): float => $this->getSubtotal()
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
            get: fn (): string => $this->getFormattedSubtotal()
        );
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the price.
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPrice(): string
    {
        return (new Currency($this->getPrice(), $this->checkoutable->getCurrency()))->format();
    }

    /**
     * Get the total.
     */
    public function getTotal(): float
    {
        return ($this->getPrice() + $this->getTaxTotal()) * $this->getQuantity();
    }

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string
    {
        return (new Currency($this->getTotal(), $this->checkoutable->getCurrency()))->format();
    }

    /**
     * Get the subtotal.
     */
    public function getSubtotal(): float
    {
        return $this->getPrice() * $this->getQuantity();
    }

    /**
     * Get the formatted subtotal.
     */
    public function getFormattedSubtotal(): string
    {
        return (new Currency($this->getSubtotal(), $this->checkoutable->getCurrency()))->format();
    }

    /**
     * Get the tax base.
     */
    public function getTaxBase(): float
    {
        return $this->price;
    }

    /**
     * Get the formatted tax.
     */
    public function getFormattedTaxTotal(): string
    {
        return (new Currency($this->getTaxTotal(), $this->checkoutable->getCurrency()))->format();
    }

    /**
     * Get the quantity.
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * Determine if the item is a line item.
     */
    public function isLineItem(): bool
    {
        return $this->buyable instanceof Buyable;
    }

    /**
     * Determine if the item is a fee.
     */
    public function isFee(): bool
    {
        return ! $this->isLineItem();
    }

    /**
     * Calculate the taxes.
     */
    public function calculateTaxes(): float
    {
        $this->buyable->taxRates->each(function (TaxRate $taxRate): void {
            $taxRate->calculate($this);
        });

        return $this->getTaxTotal();
    }
}
