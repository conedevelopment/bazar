<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Cone\Bazar\Enums\Currency;
use Cone\Bazar\Interfaces\Inventoryable;
use Cone\Bazar\Interfaces\LineItem;
use Cone\Bazar\Interfaces\Taxable;
use Cone\Bazar\Models\AppliedCoupon;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Coupon;
use Cone\Bazar\Models\Discountable;
use Cone\Bazar\Models\DiscountRule;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Facades\Shipping as ShippingManager;
use Cone\Root\Interfaces\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Throwable;

trait AsOrder
{
    use InteractsWithDiscounts {
        InteractsWithDiscounts::calculateDiscount as protected __calculateDiscount;
    }

    /**
     * Boot the trait.
     */
    public static function bootAsOrder(): void
    {
        static::deleting(static function (self $model): void {
            if (! in_array(SoftDeletes::class, class_uses_recursive($model)) || $model->forceDeleting) {
                $model->items->each->delete();
                $model->shipping->delete();
            }
        });
    }

    /**
     * Get the user for the model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(get_class(App::make(User::class)));
    }

    /**
     * Get the items for the model.
     */
    public function items(): MorphMany
    {
        return $this->morphMany(Item::getProxiedClass(), 'checkoutable');
    }

    /**
     * Get the shipping for the model.
     */
    public function shipping(): MorphOne
    {
        return $this->morphOne(Shipping::getProxiedClass(), 'shippable')->withDefault([
            'driver' => ShippingManager::getDefaultDriver(),
        ]);
    }

    /**
     * Get the coupons for the model.
     */
    public function coupons(): MorphToMany
    {
        return $this->morphToMany(Coupon::getProxiedClass(), 'couponable', 'bazar_couponables')
            ->as('coupon')
            ->using(AppliedCoupon::getProxiedClass())
            ->withPivot(['value'])
            ->withTimestamps();
    }

    /**
     * Get the items.
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Get the line items.
     */
    public function getLineItems(): Collection
    {
        return $this->getItems()->filter->isLineItem();
    }

    /**
     * Get the fees.
     */
    public function getFees(): Collection
    {
        return $this->getItems()->filter->isFee();
    }

    /**
     * Get the taxables.
     */
    public function getTaxables(): Collection
    {
        return $this->getItems()->when($this->needsShipping(), function (Collection $items): Collection {
            return $items->merge([$this->shipping]);
        });
    }

    /**
     * Get the total attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<float, never>
     */
    protected function total(): Attribute
    {
        return new Attribute(
            get: fn (): float => $this->getTotal()
        );
    }

    /**
     * Get the formatted total attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedTotal(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedTotal()
        );
    }

    /**
     * Get the subtotal attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<float, never>
     */
    protected function subtotal(): Attribute
    {
        return new Attribute(
            get: fn (): float => $this->getSubtotal()
        );
    }

    /**
     * Get the formatted subtotal attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedSubtotal(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedSubtotal()
        );
    }

    /**
     * Get the tax attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<float, never>
     */
    protected function tax(): Attribute
    {
        return new Attribute(
            get: fn (): float => $this->getTax()
        );
    }

    /**
     * Get the formatted tax attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedTax(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedTax()
        );
    }

    /**
     * Get the discount attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<float, never>
     */
    protected function discount(): Attribute
    {
        return new Attribute(
            get: fn (): float => $this->getDiscount()
        );
    }

    /**
     * Get the formatted discount attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function formattedDiscount(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->getFormattedDiscount()
        );
    }

    /**
     * Determine if the model needs shipping.
     */
    public function needsShipping(): bool
    {
        return $this->items->some(static function (Item $item): bool {
            return ! $item->isFee()
                && $item->buyable instanceof Inventoryable
                && $item->buyable->isPhysical();
        });
    }

    /**
     * Get the currency.
     */
    public function getCurrency(): Currency
    {
        return $this->currency ?: Bazar::getCurrency();
    }

    /**
     * Get the checkoutable model's total.
     */
    public function getTotal(): float
    {
        $value = $this->items->sum(static function (Item $item): float {
            return $item->getTotal();
        });

        $value += $this->needsShipping() ? $this->shipping->getTotal() : 0;

        $value -= $this->getDiscount();

        return round(max($value, 0), 2);
    }

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string
    {
        return $this->getCurrency()->format($this->getTotal());
    }

    /**
     * Get the checkoutable model's subtotal.
     */
    public function getSubtotal(): float
    {
        $value = $this->getLineItems()->sum(static function (LineItem $item): float {
            return $item->getSubtotal();
        });

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted subtotal.
     */
    public function getFormattedSubtotal(): string
    {
        return $this->getCurrency()->format($this->getSubtotal());
    }

    /**
     * Get the checkoutable model's fee total.
     */
    public function getFeeTotal(): float
    {
        $value = $this->getFees()->sum(static function (LineItem $item): float {
            return $item->getSubtotal();
        });

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted fee total.
     */
    public function getFormattedFeeTotal(): string
    {
        return $this->getCurrency()->format($this->getFeeTotal());
    }

    /**
     * Get the tax.
     */
    public function getTax(): float
    {
        $value = $this->getTaxables()->sum(static function (Taxable $item): float {
            return $item->getTaxTotal();
        });

        return round($value, 2);
    }

    /**
     * Get the formatted tax.
     */
    public function getFormattedTax(): string
    {
        return $this->getCurrency()->format($this->getTax());
    }

    /**
     * Calculate the tax.
     */
    public function calculateTax(): float
    {
        $value = $this->getTaxables()->each(static function (Taxable $item): void {
            $item->calculateTaxes();
        })->sum(static function (Taxable $item): float {
            return $item->getTaxTotal();
        });

        return round($value, 2);
    }

    /**
     * Find an item by its attributes or make a new instance.
     */
    public function findItem(array $attributes): ?Item
    {
        $attributes = array_merge(['properties' => null], $attributes, [
            'checkoutable_id' => $this->getKey(),
            'checkoutable_type' => static::class,
        ]);

        return $this->items->first(static function (Item $item) use ($attributes): bool {
            return empty(array_diff(
                array_filter(Arr::dot($attributes)),
                array_filter(Arr::dot(array_merge(['properties' => null], $item->withoutRelations()->toArray())))
            ));
        });
    }

    /**
     * Merge the given item into the collection.
     */
    public function mergeItem(Item $item): Item
    {
        $stored = $this->findItem(
            $item->only(['properties', 'buyable_id', 'buyable_type'])
        );

        if (is_null($stored)) {
            $item->checkoutable()->associate($this);

            $item->setRelation('checkoutable', $this->withoutRelations());

            $this->items->push($item);

            return $item;
        }

        $stored->quantity += $item->quantity;

        return $stored;
    }

    /**
     * Sync the items.
     */
    public function syncItems(): void
    {
        $this->items->each(static function (Item $item): void {
            if ($item->isLineItem() && ! is_null($item->checkoutable)) {
                $data = $item->buyable->toItem($item->checkoutable, $item->only('properties'))->only(['price']);

                $item->fill($data)->save();
                $item->calculateTaxes();
            }
        });
    }

    /**
     * Determine whether the checkoutable models needs payment.
     */
    public function needsPayment(): bool
    {
        return $this->getTotal() > 0;
    }

    /**
     * Apply a coupon to the checkoutable model.
     */
    public function applyCoupon(string|Coupon $coupon): bool
    {
        try {
            $coupon = match (true) {
                is_string($coupon) => Coupon::query()->code($coupon)->available()->firstOrFail(),
                default => $coupon,
            };

            $coupon->apply($this);

            return true;
        } catch (ModelNotFoundException $exception) {
            //
        } catch (Throwable $exception) {
            $this->removeCoupon($coupon);
        }

        return false;
    }

    /**
     * Remove a coupon from the checkoutable model.
     */
    public function removeCoupon(string|Coupon $coupon): void
    {
        try {
            $coupon = match (true) {
                is_string($coupon) => Coupon::query()->code($coupon)->firstOrFail(),
                default => $coupon,
            };

            $this->coupons()->detach([$coupon->getKey()]);
        } catch (Throwable $exception) {
            //
        }
    }

    /**
     * Get the discount.
     */
    public function getDiscount(): float
    {
        $value = $this->coupons->sum('coupon.value');
        $value += $this->discounts->sum('discount.value');
        $value += ($this->needsShipping() ? $this->shipping->getDiscount() : 0);
        $value += $this->items->sum(static function (Item $item): float {
            return $item->getDiscount();
        });

        return $value;
    }

    /**
     * Get the discountable currency.
     */
    public function getDiscountableCurrency(): Currency
    {
        return $this->getCurrency();
    }

    /**
     * Get the discountable quantity.
     */
    public function getDiscountableQuantity(): float
    {
        return $this->items->sum(static function (Item $item): float {
            return $item->getDiscountableQuantity();
        });
    }

    /**
     * Calculate the discount.
     */
    public function calculateDiscount(): float
    {
        $this->coupons->each(function (Coupon $coupon): void {
            $this->applyCoupon($coupon);
        });

        $this->getItems()->each(static function (Item $item): void {
            $item->calculateDiscount();
        });

        $this->shipping->calculateDiscount();

        return $this->__calculateDiscount();
    }

    /**
     * Get the applicable discount rules.
     */
    public function getApplicableDiscountRules(): Collection
    {
        return once(fn (): Collection => $this->applicableDiscountRulesQuery()->get());
    }

    /**
     * Get the applicable discount rules query.
     */
    protected function applicableDiscountRulesQuery(): Builder
    {
        return DiscountRule::proxy()
            ->newQuery()
            ->active()
            ->where(function (Builder $query): Builder {
                return $query->whereIn(
                    $query->qualifyColumn('discountable_type'),
                    [Cart::getProxiedClass(), Shipping::getProxiedClass()]
                )->orWhere(function (Builder $query): Builder {
                    return $query->whereIn(
                        $query->getModel()->getQualifiedKeyName(),
                        Discountable::proxy()
                            ->newQuery()
                            ->select('bazar_discountables.discount_rule_id')
                            ->whereRaw(sprintf(
                                'concat(bazar_discountables.discountable_type, \':\', bazar_discountables.discountable_id) in (%s)',
                                $this->items()->selectRaw('concat(bazar_items.buyable_type, \':\', bazar_items.buyable_id) as `type`')->toRawSql()
                            ))
                    );
                });
            })
            ->where(function (Builder $query): Builder {
                return $query->whereDoesntHave('users')
                    ->orWhereHas('users', function (Builder $query): Builder {
                        return $query->where($query->getModel()->getQualifiedKeyName(), $this->user_id);
                    });
            });
    }
}
