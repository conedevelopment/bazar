<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Exceptions\DiscountException;
use Cone\Bazar\Models\Discount;
use Cone\Bazar\Models\DiscountRule;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Number;
use Throwable;

trait InteractsWithDiscounts
{
    /**
     * Boot the trait.
     */
    public static function bootInteractsWithDiscounts(): void
    {
        static::deleting(static function (self $model): void {
            if (! in_array(SoftDeletes::class, class_uses_recursive($model)) || $model->forceDeleting) {
                $model->discounts()->detach();
            }
        });
    }

    /**
     * Get the discounts for the model.
     */
    public function discounts(): MorphToMany
    {
        return $this->morphToMany(DiscountRule::getProxiedClass(), 'discountable', 'bazar_discounts')
            ->as('discount')
            ->using(Discount::getProxiedClass())
            ->withPivot(['value'])
            ->withTimestamps();
    }

    /**
     * Apply a discount rule to the checkoutable model.
     */
    public function applyDiscount(DiscountRule $discountRule): bool
    {
        try {
            $discountRule->apply($this);

            return true;
        } catch (DiscountException $exception) {
            //
        } catch (Throwable $exception) {
            $this->removeDiscount($discountRule);
        }

        return false;
    }

    /**
     * Remove a discount rule from the discountable model.
     */
    public function removeDiscount(DiscountRule $discountRule): void
    {
        $this->discounts()->detach([$discountRule->getKey()]);
    }

    /**
     * Get the discount base.
     */
    public function getDiscountBase(): float
    {
        return $this->getSubtotal();
    }

    /**
     * Get the discount.
     */
    public function getDiscount(): float
    {
        return $this->discounts->sum('discount.value');
    }

    /**
     * Get the discount rate.
     */
    public function getDiscountRate(): float
    {
        return (float) match (true) {
            $this->getDiscountBase() > 0 => round(($this->getDiscount() / $this->getDiscountBase()) * 100, 2),
            default => 0,
        };
    }

    /**
     * Get the formatted discount rate.
     */
    public function getFormattedDiscountRate(): string
    {
        return Number::percentage($this->getDiscountRate());
    }

    /**
     * Calculate the discount.
     */
    public function calculateDiscount(): float
    {
        $this->getApplicableDiscountRules()->each(function (DiscountRule $rule): void {
            $this->applyDiscount($rule);
        });

        return $this->getDiscount();
    }
}
