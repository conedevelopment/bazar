<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Concerns\Addressable;
use Bazar\Concerns\HasUuid;
use Bazar\Concerns\InteractsWithDiscounts;
use Bazar\Concerns\InteractsWithItems;
use Bazar\Concerns\InteractsWithProxy;
use Bazar\Contracts\Discountable;
use Bazar\Contracts\Itemable;
use Bazar\Contracts\Models\Cart as Contract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Cart extends Model implements Contract, Discountable, Itemable
{
    use Addressable, HasUuid, InteractsWithDiscounts, InteractsWithItems, InteractsWithProxy;

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
        'id',
        'locked',
        'discount',
        'currency',
    ];

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

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
    protected $table = 'bazar_carts';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(static function (self $cart): void {
            $cart->currency = $cart->currency ?: Bazar::currency();
        });

        static::updating(static function (self $cart): void {
            if (! $cart->locked && $cart->getOriginal('currency') !== $cart->currency) {
                $cart->items->each->save();
                $cart->discount(false);
            }
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
     * Lock the cart.
     *
     * @return void
     */
    public function lock(): void
    {
        if (! $this->locked) {
            $this->fill(['locked' => true])->save();
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
            $this->fill(['locked' => false])->save();
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
                     ->where($query->qualifyColumn('updated_at'), '<', Carbon::now()->subDays(3));
    }
}
