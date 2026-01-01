<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Discount;
use Cone\Bazar\Models\DiscountRule;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
}
