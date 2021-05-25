<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Concerns\Addressable;
use Bazar\Concerns\InteractsWithDiscounts;
use Bazar\Concerns\InteractsWithItems;
use Bazar\Concerns\InteractsWithProxy;
use Bazar\Contracts\Models\Cart as Contract;
use Bazar\Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;

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
     * @var array
     */
    protected $attributes = [
        'discount' => 0,
        'locked' => false,
        'currency' => null,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'locked' => 'bool',
        'discount' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locked',
        'discount',
        'currency',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_carts';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(static function (self $cart): void {
            $cart->currency = $cart->currency ?: Bazar::getCurrency();
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
     * @return \Bazar\Database\Factories\CartFactory
     */
    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
    }

    /**
     * Get the order for the cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::getProxiedClass())->withDefault(function (): Order {
            return Order::proxy()->newInstance($this->toArray());
        });
    }

    /**
     * Lock the cart.
     *
     * @return void
     */
    public function lock(): void
    {
        if (! $this->locked) {
            $this->setAttribute('locked', true)->save();
        }
    }

    /**
     * Unlock the cart.
     *
     * @return void
     */
    public function unlock(): void
    {
        if ($this->locked) {
            $this->setAttribute('locked', false)->save();
        }
    }

    /**
     * Scope a query to only include the locked carts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocked(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('locked'), true);
    }

    /**
     * Scope a query to only include the unlocked carts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnlocked(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('locked'), false);
    }

    /**
     * Scope a query to only include the expired carts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNull($query->qualifyColumn('user_id'))
                     ->where($query->qualifyColumn('updated_at'), '<', Date::now()->subDays(3));
    }

    /**
     * Convert the cart to a new order.
     *
     * @return \Bazar\Models\Order
     */
    public function toOrder(): Order
    {
        $this->order->user()->associate($this->user)->save();

        if ($this->order_id !== $this->order->id) {
            $this->order()->associate($this->order)->save();
        }

        $this->order->items()->createMany($this->items->toArray());
        $this->order->address->fill($this->address->toArray())->save();
        $this->order->shipping->fill($this->shipping->toArray())->save();
        $this->order->shipping->address->fill($this->shipping->address->toArray())->save();

        return $this->order;
    }
}
