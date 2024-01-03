<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Bazar;
use Cone\Bazar\Database\Factories\CartFactory;
use Cone\Bazar\Interfaces\Models\Cart as Contract;
use Cone\Bazar\Traits\Addressable;
use Cone\Bazar\Traits\InteractsWithDiscounts;
use Cone\Bazar\Traits\InteractsWithItems;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Number;

class Cart extends Model implements Contract
{
    use Addressable;
    use HasFactory;
    use InteractsWithDiscounts;
    use InteractsWithItems;
    use InteractsWithProxy;

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'currency' => null,
        'discount' => 0,
        'locked' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount' => 'float',
        'locked' => 'bool',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'currency',
        'discount',
        'locked',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_carts';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(static function (self $cart): void {
            $cart->currency = $cart->currency ?: Bazar::getCurrency();
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
    protected static function newFactory(): Factory
    {
        return CartFactory::new();
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the order for the cart.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::getProxiedClass())
            ->withDefault(function (Order $order): Order {
                return $order->fill($this->toArray());
            });
    }

    /**
     * Get the address for the model.
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::getProxiedClass(), 'addressable')
            ->withDefault(function (Address $address): Address {
                return $address->fill($this->user?->address?->toArray() ?: []);
            });
    }

    /**
     * Get the discount rate.
     */
    public function getDiscountRate(): float
    {
        return $this->getSubtotal() > 0 ? ($this->getDiscount() / $this->getSubtotal()) * 100 : 0;
    }

    /**
     * Get the formatted discount rate.
     */
    public function getFormattedDiscountRate(): string
    {
        return Number::percentage($this->getDiscountRate());
    }

    /**
     * Lock the cart.
     */
    public function lock(): void
    {
        if (! $this->locked) {
            $this->setAttribute('locked', true)->save();
        }
    }

    /**
     * Unlock the cart.
     */
    public function unlock(): void
    {
        if ($this->locked) {
            $this->setAttribute('locked', false)->save();
        }
    }

    /**
     * Scope a query to only include the locked carts.
     */
    public function scopeLocked(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('locked'), true);
    }

    /**
     * Scope a query to only include the unlocked carts.
     */
    public function scopeUnlocked(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('locked'), false);
    }

    /**
     * Scope a query to only include the expired carts.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNull($query->qualifyColumn('user_id'))
            ->where($query->qualifyColumn('updated_at'), '<', Date::now()->subDays(3));
    }

    /**
     * Convert the cart to a new order.
     */
    public function toOrder(): Order
    {
        $this->order->user()->associate($this->user)->save();

        if ($this->order_id !== $this->order->getKey()) {
            $this->order()->associate($this->order)->save();
        }

        $this->order->items()->delete();
        $this->order->items()->createMany($this->items->toArray());

        $this->order->address->fill($this->address->toArray())->save();

        if ($this->order->needsShipping()) {
            $this->order->shipping->fill($this->shipping->toArray())->save();
            $this->order->shipping->address->fill($this->shipping->address->toArray())->save();
        }

        return $this->order;
    }
}
