<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Cone\Bazar\Interfaces\Inventoryable;
use Cone\Bazar\Interfaces\LineItem;
use Cone\Bazar\Interfaces\Taxable;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Currency;
use Cone\Bazar\Support\Facades\Shipping as ShippingManager;
use Cone\Root\Interfaces\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

trait InteractsWithItems
{
    /**
     * Boot the trait.
     */
    public static function bootInteractsWithItems(): void
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
     * Get the currency attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function currency(): Attribute
    {
        return new Attribute(
            get: static function (?string $value = null): string {
                return strtoupper($value ?: Bazar::getCurrency());
            }
        );
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
            get: function (): float {
                return $this->getTotal();
            }
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
            get: function (): string {
                return $this->getFormattedTotal();
            }
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
            get: function (): float {
                return $this->getSubtotal();
            }
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
            get: function (): string {
                return $this->getFormattedSubtotal();
            }
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
            get: function (): float {
                return $this->getTax();
            }
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
            get: function (): string {
                return $this->getFormattedTax();
            }
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
    public function getCurrency(): string
    {
        return strtoupper($this->currency);
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

        $value -= $this->discount;

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string
    {
        return (new Currency($this->getTotal(), $this->getCurrency()))->format();
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
        return (new Currency($this->getSubtotal(), $this->getCurrency()))->format();
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
        return (new Currency($this->getFeeTotal(), $this->getCurrency()))->format();
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
        return (new Currency($this->getTax(), $this->getCurrency()))->format();
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
                $data = $item->buyable->toItem($item->checkoutable, $item->only('properties'))->only('price');

                $item->fill($data)->save();
                $item->calculateTaxes();
            }
        });
    }
}
