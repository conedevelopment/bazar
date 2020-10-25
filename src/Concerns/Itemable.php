<?php

namespace Bazar\Concerns;

use Bazar\Contracts\Taxable;
use Bazar\Models\Item;
use Bazar\Models\Product;
use Bazar\Models\Shipping;
use Bazar\Models\User;
use Bazar\Support\Facades\Discount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

trait Itemable
{
    /**
     * Get the user for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphToMany(Product::class, 'itemable', 'items')
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
        return $this->morphOne(Shipping::class, 'shippable')->withDefault();
    }

    /**
     * Get the shipping attribute.
     *
     * @return \Bazar\Models\Shipping
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
        return $this->products->pluck('item')->filter()->values();
    }

    /**
     * Get all the taxable items of the model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTaxablesAttribute(): Collection
    {
        return $this->items->merge([$this->shipping])->filter(function ($item) {
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
        $value = $this->taxables->sum(function (Taxable $taxable) {
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
     * Get the formatted discount attribute.
     *
     * @return string
     */
    public function getFormattedDiscountAttribute(): string
    {
        return $this->formattedDiscount();
    }

    /**
     * Get the itemable model's total.
     *
     * @return float
     */
    public function total(): float
    {
        $value = $this->taxables->reduce(function (float $value, Taxable $item) {
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
        $value = $this->taxables->reduce(function (float $value, Taxable $item) {
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
        return $this->taxables->sum(function (Taxable $taxable) use ($update) {
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
     * Calculate the discount.
     *
     * @param  bool  $update
     * @return float
     */
    public function discount(bool $update = true): float
    {
        $discount = Discount::calculate($this);

        if ($this->exists && $update) {
            $this->update(compact('discount'));
        }

        return $this->discount = $discount;
    }

    /**
     * Get the formatted discount.
     *
     * @return string
     */
    public function formattedDiscount(): string
    {
        return Str::currency($this->discount, $this->currency);
    }

    /**
     * Get the downloadable files with their signed URL.
     *
     * @return \Illuminate\Support\Collection
     */
    public function downloads(): Collection
    {
        return $this->products->filter(function (Product $product) {
            return $product->inventory['downloadable'];
        })->flatMap(function (Product $product) {
            return $product->inventory['files'];
        })->map(function (array $file) {
            $expiration = ($file['expiration'] ?? null) ? $this->created_at->addDays($file['expiration']) : null;

            return array_replace($file, compact('expiration') + ['url' => URL::signedRoute(
                'bazar.download', ['url' => Crypt::encryptString($file['url'])], $expiration
            )]);
        });
    }

    /**
     * Get an item by its parent product and properties.
     *
     * @param  \Bazar\Models\Product  $product
     * @param  array  $properties
     * @return \Bazar\Models\Item|null
     */
    public function item(Product $product, array $properties = []): ?Item
    {
        return $this->items->first(function (Item $item) use ($product, $properties) {
            return (int) $item->product_id === (int) $product->id && empty(array_diff(
                Arr::dot($properties), Arr::dot($item->properties)
            ));
        });
    }
}
