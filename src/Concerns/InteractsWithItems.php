<?php

namespace Bazar\Concerns;

use Bazar\Bazar;
use Bazar\Contracts\Models\Product;
use Bazar\Contracts\Models\Shipping;
use Bazar\Contracts\Taxable;
use Bazar\Models\Item;
use Bazar\Proxies\Product as ProductProxy;
use Bazar\Proxies\Shipping as ShippingProxy;
use Bazar\Proxies\User as UserProxy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
            if (! in_array(SoftDeletes::class, class_uses($model)) || $model->forceDeleting) {
                $model->products()->detach();
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
        return $this->belongsTo(UserProxy::getProxiedClass());
    }

    /**
     * Get the products for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphToMany(ProductProxy::getProxiedClass(), 'itemable', 'bazar_items')
                    ->withPivot(['id', 'price', 'tax', 'quantity', 'properties'])
                    ->withTimestamps()
                    ->as('item')
                    ->using(Item::class);
    }

    /**
     * Get the shipping for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function shipping(): MorphOne
    {
        return $this->morphOne(ShippingProxy::getProxiedClass(), 'shippable')->withDefault();
    }

    /**
     * Get the currency attribute.
     *
     * @param  string|null  $value
     * @return string
     */
    public function getCurrencyAttribute(string $value = null): string
    {
        return $value ?: Bazar::currency();
    }

    /**
     * Get the shipping attribute.
     *
     * @return \Bazar\Contracts\Models\Shipping
     */
    public function getShippingAttribute(): Shipping
    {
        return $this->getRelationValue('shipping')->setRelation(
            'shippable', $this->withoutRelations()->makeHidden('shipping')
        )->makeHidden('shippable');
    }

    /**
     * Get all the items of the model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getItemsAttribute(): Collection
    {
        return $this->products->map(function (Product $product): Item {
            return (
                $product->item instanceof Item
                    ? $product->item
                    : new Item((array) $product->item)
            )->setRelation('product', $product)
             ->setRelation('itemable', $this);
        });
    }

    /**
     * Get all the taxable items of the model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTaxablesAttribute(): Collection
    {
        return $this->items->merge([$this->shipping])->filter(static function ($item): bool {
            return $item instanceof Taxable;
        })->values();
    }

    /**
     * Get the total attibute.
     *
     * @return float
     */
    public function getTotalAttribute(): float
    {
        return $this->total();
    }

    /**
     * Get the formatted total attribute.
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->formattedTotal();
    }

    /**
     * Get the net total attribute.
     *
     * @return float
     */
    public function getNetTotalAttribute(): float
    {
        return $this->netTotal();
    }

    /**
     * Get the formatted net total attribute.
     *
     * @return string
     */
    public function getFormattedNetTotalAttribute(): string
    {
        return $this->formattedNetTotal();
    }

    /**
     * Get the tax attribute.
     *
     * @return float
     */
    public function getTaxAttribute(): float
    {
        $value = $this->taxables->sum(static function (Taxable $taxable): float {
            return $taxable->tax * $taxable->quantity;
        });

        return round($value, 2);
    }

    /**
     * Get the formatted tax attribute.
     *
     * @return string
     */
    public function getFormattedTaxAttribute(): string
    {
        return $this->formattedTax();
    }

    /**
     * Get the itemable model's total.
     *
     * @return float
     */
    public function total(): float
    {
        $value = $this->taxables->reduce(static function (float $value, Taxable $item): float {
            return $value + $item->total;
        }, -$this->discount);

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted total.
     *
     * @return string
     */
    public function formattedTotal(): string
    {
        return Str::currency($this->netTotal(), $this->currency);
    }

    /**
     * Get the itemable model's total.
     *
     * @return float
     */
    public function netTotal(): float
    {
        $value = $this->taxables->reduce(static function (float $value, Taxable $item): float {
            return $value + $item->netTotal;
        }, -$this->discount);

        return round($value < 0 ? 0 : $value, 2);
    }

    /**
     * Get the formatted net total.
     *
     * @return string
     */
    public function formattedNetTotal(): string
    {
        return Str::currency($this->netTotal(), $this->currency);
    }

    /**
     * Get the total tax.
     *
     * @param  bool  $update
     * @return float
     */
    public function tax(bool $update = true): float
    {
        return $this->taxables->sum(static function (Taxable $taxable) use ($update): float {
            return $taxable->tax($update) * $taxable->quantity;
        });
    }

    /**
     * Get the formatted tax.
     *
     * @return string
     */
    public function formattedTax(): string
    {
        return Str::currency($this->tax, $this->currency);
    }

    /**
     * Get the downloadable files with their signed URL.
     *
     * @return \Illuminate\Support\Collection
     */
    public function downloads(): Collection
    {
        return $this->products->filter(static function (Product $product): bool {
            return $product->inventory->downloadable();
        })->flatMap(static function (Product $product): array {
            return $product->inventory->get('files', []);
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
     * Get an item by its parent product and properties.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  array  $properties
     * @return \Bazar\Models\Item|null
     */
    public function item(Product $product, array $properties = []): ?Item
    {
        return $this->items->first(static function (Item $item) use ($product, $properties): bool {
            return (int) $item->product_id === (int) $product->id && empty(array_diff(
                Arr::dot($properties), Arr::dot($item->properties)
            ));
        });
    }
}
