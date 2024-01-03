<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\ItemFactory;
use Cone\Bazar\Interfaces\Buyable;
use Cone\Bazar\Interfaces\Models\Item as Contract;
use Cone\Bazar\Traits\InteractsWithTaxes;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Number;

class Item extends Model implements Contract
{
    use HasFactory;
    use HasUuids;
    use InteractsWithProxy;
    use InteractsWithTaxes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
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
        'quantity' => 0,
        'tax' => 0,
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
        'tax' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'buyable_id',
        'buyable_type',
        'name',
        'price',
        'properties',
        'quantity',
        'tax',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'itemable',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_items';

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
    protected static function newFactory(): Factory
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
     * Get the itemable model for the item.
     */
    public function itemable(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    /**
     * Get the formatted price attribute.
     */
    protected function formattedPrice(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedPrice(),
        );
    }

    /**
     * Get the total attribute.
     */
    protected function total(): Attribute
    {
        return new Attribute(
            get: fn (): float => $this->getTotal(),
        );
    }

    /**
     * Get the formatted total attribute.
     */
    protected function formattedTotal(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedTotal(),
        );
    }

    /**
     * Get the subtotal attribute.
     */
    protected function subtotal(): Attribute
    {
        return new Attribute(
            get: fn (): float => $this->getSubtotal(),
        );
    }

    /**
     * Get the formatted subtotal attribute.
     */
    protected function formattedSubtotal(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedSubtotal(),
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
        return Number::currency($this->getPrice(), $this->itemable->getCurrency());
    }

    /**
     * Get the total.
     */
    public function getTotal(): float
    {
        return ($this->getPrice() + $this->getTax()) * $this->getQuantity();
    }

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string
    {
        return Number::currency($this->getTotal(), $this->itemable->getCurrency());
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
        return Number::currency($this->getSubtotal(), $this->itemable->getCurrency());
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
}
