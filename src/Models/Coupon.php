<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Enums\CouponType;
use Cone\Bazar\Interfaces\Checkoutable;
use Cone\Bazar\Interfaces\Models\Coupon as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;

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
            'rules' => 'json',
            'stackable' => 'boolean',
            'type' => CouponType::class,
            'value' => 'float',
        ];
    }

    /**
     * Get the applications of the coupon.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(AppliedCoupon::getProxiedClass());
    }

    /**
     * Get the limit of the coupon.
     */
    public function limit(): int
    {
        return (int) ($this->rules['limit'] ?? 0);
    }

    /**
     * Validate the coupon for the checkoutable model.
     */
    public function validate(Checkoutable $model): bool
    {
        if (! $this->active) {
            return false;
        }

        if (! is_null($this->expires_at) && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->limit() > 0 && $this->applications()->count() >= $this->limit()) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for the checkoutable model.
     */
    public function calculate(Checkoutable $model): float
    {
        return match ($this->type) {
            CouponType::PERCENT => round($model->getSubtotal() * ($this->value / 100), 2),
            CouponType::FIX => min($this->value, $model->getSubtotal()),
        };
    }

    /**
     * Apply the coupon to the checkoutable model.
     */
    public function apply(Checkoutable $model): void
    {
        if (! $this->validate($model)) {
            throw new Exception('The coupon is not valid for this checkoutable model.');
        }

        $value = $this->calculate($model);

        $model->coupons()->syncWithoutDetaching([
            $this->getKey() => ['value' => $value],
        ]);
    }

    /**
     * Scope a query to only include active coupons.
     */
    #[Scope]
    protected function active(Builder $query, bool $active = true): Builder
    {
        return $query->where($query->qualifyColumn('active'), $active);
    }

    /**
     * Scope a query to only include available coupons.
     */
    #[Scope]
    protected function available(Builder $query, ?DateTimeInterface $date = null): Builder
    {
        $date ??= Date::now();

        return $query->active()
            ->whereNull($query->qualifyColumn('expires_at'))
            ->orWhere($query->qualifyColumn('expires_at'), '>', $date);
    }
}
