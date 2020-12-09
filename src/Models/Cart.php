<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Concerns\Addressable;
use Bazar\Concerns\InteractsWithDiscounts;
use Bazar\Concerns\InteractsWithItems;
use Bazar\Contracts\Discountable;
use Bazar\Contracts\Itemable;
use Bazar\Contracts\Models\Cart as Contract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

class Cart extends Model implements Contract, Discountable, Itemable
{
    use Addressable, InteractsWithDiscounts, InteractsWithItems;

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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token',
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
        static::creating(static function (Cart $cart): void {
            $cart->token = $cart->token ?: Uuid::uuid4();
            $cart->currency = $cart->currency ?: Bazar::currency();
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
        return $query->where('locked', true);
    }

    /**
     * Scope a query to only include the unlocked carts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnlocked(Builder $query): Builder
    {
        return $query->where('locked', false);
    }

    /**
     * Scope a query to only include the expired carts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNull('user_id')->where(
            'updated_at', '<', Carbon::now()->subDays(3)
        );
    }
}
