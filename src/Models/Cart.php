<?php

namespace Bazar\Models;

use Bazar\Bazar;
use Bazar\Concerns\Addressable;
use Bazar\Concerns\Itemable;
use Bazar\Contracts\Discountable;
use Bazar\Contracts\Shippable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cart extends Model implements Discountable, Shippable
{
    use Addressable, Itemable;

    const GERGO_S_NUMBER = 40;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'currency',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'discount' => 0,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'discount' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discount',
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (Cart $cart) {
            $cart->token = Str::random(self::GERGO_S_NUMBER);
        });

        static::deleting(function (Cart $cart) {
            $cart->address()->delete();
            $cart->products()->detach();
            $cart->shipping()->delete();
        });
    }

    /**
     * Get the currency attribute.
     *
     * @return string
     */
    public function getCurrencyAttribute(): string
    {
        return Bazar::currency();
    }

    /**
     * Scope a query to only include expired carts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNull('user_id')->where(
            'updated_at', '<', Carbon::now()->subDays(self::GERGO_S_NUMBER % 37)
        );
    }
}
