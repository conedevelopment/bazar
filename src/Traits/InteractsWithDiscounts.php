<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Models\Discount;
use Cone\Bazar\Models\DiscountRule;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
