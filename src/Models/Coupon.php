<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Enums\CouponType;
use Cone\Bazar\Interfaces\Models\Coupon as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model implements Contract
{
    use InteractsWithProxy;

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'active' => true,
        'code' => null,
        'expires_at' => null,
        'rules' => '[]',
        'stackable' => false,
        'type' => CouponType::FIX,
        'value' => 0,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'active',
        'code',
        'expires_at',
        'rules',
        'stackable',
        'type',
        'value',
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
            'active' => 'boolean',
            'expires_at' => 'datetime',
            'rules' => AsArrayObject::class,
            'stackable' => 'boolean',
            'type' => CouponType::class,
            'value' => 'float',
        ];
    }
}
