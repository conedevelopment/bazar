<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Enums\CouponType;
use Cone\Bazar\Interfaces\Models\Coupon as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model implements Contract
{
    use InteractsWithProxy;

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'available_at',
        'code',
        'discount',
        'expires_at',
        'limit',
        'type',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'bazar_coupons';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * {@inheritdoc}
     */
    public function casts(): array
    {
        return [
            'available_at' => 'datetime',
            'expires_at' => 'datetime',
            'discount' => 'float',
            'limit' => 'integer',
            'type' => CouponType::class,
        ];
    }
}
