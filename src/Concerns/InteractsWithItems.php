<?php

namespace Bazar\Concerns;

use Bazar\Bazar;
use Bazar\Contracts\Stockable;
use Bazar\Contracts\Taxable;
use Bazar\Models\Item;
use Bazar\Models\Shipping;
use Bazar\Models\User;
use Bazar\Support\Facades\Shipping as ShippingManager;
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
     *
     * @return void
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::getProxiedClass());
    }

    /**
     * Get the items for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function items(): MorphMany
    {
        return $this->morphMany(Item::getProxiedClass(), 'itemable');
    }

    /**
     * Get the shipping for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
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
     * @param  string|null  $value
     * @return string
     */
    public function getCurrencyAttribute(?string $value = null): string
    {
        return $value ?: Bazar::getCurrency();
    }

    /**
     * Get the shipping attribute.
     *
     * @return \Bazar\Models\Shipping
     */
    public function getShippingAttribute(): Shipping
    {
        return $this->getRelationValue('shipping')
                    ->setRelation('shippable', $this->withoutRelations()->makeHidden('shipping'))
                    ->makeHidden('shippable');
    }

    /**
     * Get the taxable items of the model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTaxableItemsAttribute(): Collection
    {
        return $this->items->merge([$this->shipping]);
    }

    /**
     * Get the total attibute.
     *
     * @return float
     */
    public function getTotalAttribute(): float
    {
        return $this->getTotal();
    }

    /**
     * Get the formatted total attribute.
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->getFormattedTotal();
    }

    /**
     * Get the net total attribute.
     *
     * @return float
     */
    public function getNetTotalAttribute(): float
    {
        return $this->getNetTotal();
    }

    /**
     * Get the formatted net total attribute.
     *
     * @return string
     */
    public function getFormattedNetTotalAttribute(): string
    {
        return $this->getFormattedNetTotal();
    }

    /**
     * Get the tax attribute.
     *
     * @return float
     */
    public function getTaxAttribute(): float
    {
        return $this->getTax();
    }

    /**
     * Get the formatted tax attribute.
     *
     * @return string
     */
    public function getFormattedTaxAttribute(): string
    {
        return $this->getFormattedTax();
    }

    /**
     * Get the itemable model's total.
     *
     * @return float
     */
    public function getTotal(): float
    {
        $value = $this->taxableItems->reduce(static function (float $value, Taxable $item): float {
            return $value + $item->getTotal();
        }, -$this->discount);

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted total.
     *
     * @return string
     */
    public function getFormattedTotal(): string
    {
        return Str::currency($this->getNetTotal(), $this->currency);
    }

    /**
     * Get the itemable model's total.
     *
     * @return float
     */
    public function getNetTotal(): float
    {
        $value = $this->taxableItems->reduce(static function (float $value, Taxable $item): float {
            return $value + $item->getNetTotal();
        }, -$this->discount);

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted net total.
     *
     * @return string
     */
    public function getFormattedNetTotal(): string
    {
        return Str::currency($this->getNetTotal(), $this->currency);
    }

    /**
     * Get the tax.
     *
     * @return float
     */
    public function getTax(): float
    {
        $value = $this->taxableItems->sum(static function (Taxable $taxable): float {
            return $taxable->getTax() * $taxable->quantity;
        });

        return round($value, 2);
    }

    /**
     * Get the formatted tax.
     *
     * @return string
     */
    public function getFormattedTax(): string
    {
        return Str::currency($this->getTax(), $this->currency);
    }

    /**
     * Calculate the tax.
     *
     * @param  bool  $update
     * @return float
     */
    public function calculateTax(bool $update = true): float
    {
        return $this->taxableItems->sum(static function (Taxable $taxable) use ($update): float {
            return $taxable->calculateTax($update) * $taxable->quantity;
        });
    }

    /**
     * Get the downloadable files with their signed URL.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDownloads(): Collection
    {
        $this->loadMissing(['items', 'items.buyable']);

        return $this->items->filter(static function (Item $item): bool {
            return $item->buyable instanceof Stockable
                && $item->buyable->inventory->downloadable();
        })->flatMap(static function (Item $item): array {
            return $item->buyable->inventory->get('files', []);
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
     *
     * @param  array  $attributes
     * @return \Bazar\Models\Item
     */
    public function findItemOrNew(array $attributes): Item
    {
        return $this->items->first(static function (Item $item) use ($attributes): bool {
            return empty(array_diff(Arr::dot($attributes), Arr::dot($item->toArray())));
        }, (new Item)->forceFill($attributes));
    }
}
