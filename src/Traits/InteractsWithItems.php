<?php

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Cone\Bazar\Interfaces\LineItem;
use Cone\Bazar\Interfaces\Stockable;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Facades\Shipping as ShippingManager;
use Cone\Root\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
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
    public function getCurrencyAttribute(?string $value = null): string
    {
        return $value ?: Bazar::getCurrency();
    }

    /**
     * Get the shipping attribute.
     */
    public function getShippingAttribute(): Shipping
    {
        return $this->getRelationValue('shipping')
                    ->setRelation('shippable', $this->withoutRelations()->makeHidden('shipping'))
                    ->makeHidden('shippable');
    }

    /**
     * Get the items attribute.
     */
    public function getItemsAttribute(): Collection
    {
        return $this->getRelationValue('items')->each(function (Item $item): void {
            $item->setRelation('itemable', $this->withoutRelations());
        });
    }

    /**
     * Get the line items attribute.
     */
    public function getLineItemsAttribute(): Collection
    {
        return $this->items->merge([$this->shipping]);
    }

    /**
     * Get the total attibute.
     */
    public function getTotalAttribute(): float
    {
        return $this->getTotal();
    }

    /**
     * Get the formatted total attribute.
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->getFormattedTotal();
    }

    /**
     * Get the net total attribute.
     */
    public function getNetTotalAttribute(): float
    {
        return $this->getNetTotal();
    }

    /**
     * Get the formatted net total attribute.
     */
    public function getFormattedNetTotalAttribute(): string
    {
        return $this->getFormattedNetTotal();
    }

    /**
     * Get the tax attribute.
     */
    public function getTaxAttribute(): float
    {
        return $this->getTax();
    }

    /**
     * Get the formatted tax attribute.
     */
    public function getFormattedTaxAttribute(): string
    {
        return $this->getFormattedTax();
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
     * Get the downloadable files with their signed URL.
     */
    public function getDownloads(): Collection
    {
        $this->loadMissing(['items', 'items.buyable']);

        return $this->items->filter(static function (Item $item): bool {
            return $item->buyable instanceof Stockable
                && $item->buyable->isDownloadable();
        })->flatMap(static function (Item $item): array {
            return $item->buyable->get('files', []);
        })->filter()->map(function (array $file): array {
            $expiration = ($file['expiration'] ?? null) ? $this->created_at->addDays($file['expiration']) : null;

            return array_replace($file, [
                'expiration' => $expiration,
                'url' => URL::signedRoute(
                    'bazar.download', ['url' => Crypt::encryptString($file['url'])], $expiration
                ),
            ]);
        });
    }

    /**
     * Find an item by its attributes or make a new instance.
     */
    public function findItem(array $attributes): ?Item
    {
        $attributes = array_merge($attributes, [
            'itemable_id' => $this->id,
            'itemable_type' => static::class,
        ]);

        return $this->items->first(static function (Item $item) use ($attributes): bool {
            return empty(array_diff(
                Arr::dot($attributes),
                Arr::dot($item->withoutRelations()->toArray())
            ));
        });
    }

    /**
     * Merge the given item into the collection.
     *
     * @param  \Cone\Bazar\Models\Item  $items
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
