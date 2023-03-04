<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Cone\Bazar\Interfaces\LineItem;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Facades\Shipping as ShippingManager;
use Cone\Root\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait InteractsWithItems
{
    /**
     * Boot the trait.
     */
    public static function bootInteractsWithItems(): void
    {
        static::deleting(static function (self $model): void {
            if (! in_array(SoftDeletes::class, class_uses_recursive($model)) || $model->forceDeleting) {
                $model->items()->delete();
                $model->shipping()->delete();
            }
        });
    }

    /**
     * Get the user for the model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::getProxiedClass());
    }

    /**
     * Get the items for the model.
     */
    public function items(): MorphMany
    {
        return $this->morphMany(Item::getProxiedClass(), 'itemable');
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
     */
    protected function currency(): Attribute
    {
        return new Attribute(
            get: static function (?string $value = null): string {
                return $value ?: Bazar::getCurrency();
            }
        );
    }

    /**
     * Get the line items attribute.
     */
    protected function lineItems(): Attribute
    {
        return new Attribute(
            get: function (): Collection {
                return $this->items->merge([$this->shipping]);
            }
        );
    }

    /**
     * Get the total attibute.
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
     * Get the net total attribute.
     */
    protected function netTotal(): Attribute
    {
        return new Attribute(
            get: function (): float {
                return $this->getNetTotal();
            }
        );
    }

    /**
     * Get the formatted net total attribute.
     */
    protected function formattedNetTotal(): Attribute
    {
        return new Attribute(
            get: function (): string {
                return $this->getFormattedNetTotal();
            }
        );
    }

    /**
     * Get the tax attribute.
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
     * Get the currency.
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get the itemable model's total.
     */
    public function getTotal(): float
    {
        $value = $this->lineItems->reduce(static function (float $value, LineItem $item): float {
            return $value + $item->getTotal();
        }, -$this->discount);

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted total.
     */
    public function getFormattedTotal(): string
    {
        return Str::currency($this->getNetTotal(), $this->getCurrency());
    }

    /**
     * Get the itemable model's total.
     */
    public function getNetTotal(): float
    {
        $value = $this->lineItems->reduce(static function (float $value, LineItem $item): float {
            return $value + $item->getNetTotal();
        }, -$this->discount);

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted net total.
     */
    public function getFormattedNetTotal(): string
    {
        return Str::currency($this->getNetTotal(), $this->getCurrency());
    }

    /**
     * Get the tax.
     */
    public function getTax(): float
    {
        $value = $this->lineItems->sum(static function (LineItem $item): float {
            return $item->getTax() * $item->getQuantity();
        });

        return round($value, 2);
    }

    /**
     * Get the formatted tax.
     */
    public function getFormattedTax(): string
    {
        return Str::currency($this->getTax(), $this->getCurrency());
    }

    /**
     * Calculate the tax.
     */
    public function calculateTax(bool $update = true): float
    {
        return $this->lineItems->sum(static function (LineItem $item) use ($update): float {
            return $item->calculateTax($update) * $item->getQuantity();
        });
    }

    /**
     * Find an item by its attributes or make a new instance.
     */
    public function findItem(array $attributes): ?Item
    {
        $attributes = array_merge(['properties' => null], $attributes, [
            'itemable_id' => $this->getKey(),
            'itemable_type' => static::class,
        ]);

        return $this->items->first(static function (Item $item) use ($attributes): bool {
            return empty(array_diff(
                Arr::dot($attributes),
                Arr::dot(array_merge(['properties' => null], $item->withoutRelations()->toArray()))
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
            $item->itemable()->associate($this);

            $item->setRelation('itemable', $this->withoutRelations());

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
        $this->items->each(function (Item $item): void {
            if ($item->buyable && $item->itemable) {
                $data = $item->buyable->toItem($item->itemable, $item->only('properties'))->only('price');

                $item->fill($data)->calculateTax();
            }
        });
    }
}
