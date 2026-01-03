<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\CouponFactory;
use Cone\Bazar\Enums\DiscountType;
use Cone\Bazar\Interfaces\Checkoutable;
use Cone\Bazar\Interfaces\Models\Coupon as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;

class Coupon extends Model implements Contract
{
    use HasFactory;
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
        'type' => DiscountType::FIX,
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
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CouponFactory
    {
        return CouponFactory::new();
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
            'type' => DiscountType::class,
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
        return match (true) {
            ! $this->active => false,
            ! is_null($this->expires_at) && $this->expires_at->isPast() => false,
            $model->coupons->where('stackable', false)->isNotEmpty() => false,
            ! $this->stackable && $model->coupons->isNotEmpty() => false,
            $this->limit() > 0 && $this->applications()->count() >= $this->limit() => false,
            default => true,
        };
    }

    /**
     * Calculate the discount amount for the checkoutable model.
     */
    public function calculate(Checkoutable $model): float
    {
        return match ($this->type) {
            DiscountType::PERCENT => round($model->getSubtotal() * ($this->value / 100), 2),
            DiscountType::FIX => min($this->value, $model->getSubtotal()),
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

    /**
     * Scope the query to only include the coupons with the given code.
     */
    #[Scope]
    protected function code(Builder $query, string $code): Builder
    {
        return $query->whereRaw('lower(`bazar_coupons`.`code`) like ?', [strtolower($code)]);
    }
}
