<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Cone\Bazar\Models\Price;
use Cone\Bazar\Relations\Prices;
use Cone\Bazar\Support\Currency;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasPrices
{
    /**
     * Get the prices for the model.
     */
    public function prices(): Prices
    {
        $query = $this->newRelatedInstance(Price::class)->newQuery();

        [$type, $id] = $this->getMorphs('metable', null, null);

        return new Prices($query, $this, $query->qualifyColumn($type), $query->qualifyColumn($id), $this->getKeyName());
    }

    /**
     * Get the price attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<float|null, never>
     */
    protected function price(): Attribute
    {
        return new Attribute(
            get: fn (): ?float => $this->getPrice(),
        );
    }

    /**
     * Get the formatted price attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function formattedPrice(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this->getFormattedPrice(),
        );
    }

    /**
     * Get the price by the given type and currency.
     */
    public function getPrice(?string $currency = null): ?float
    {
        $currency ??= Bazar::getCurrency();

        $key = sprintf('price_%s', strtolower($currency));

        $meta = $this->prices->firstWhere('key', $key);

        return is_null($meta) ? null : $meta->value;
    }

    /**
     * Get the formatted price by the given type and currency.
     */
    public function getFormattedPrice(?string $currency = null): ?string
    {
        $currency ??= Bazar::getCurrency();

        $price = $this->getPrice($currency);

        return is_null($price) ? null : (new Currency($price, $currency))->format();
    }

    /**
     * Determine if the stockable model is free.
     */
    public function isFree(): bool
    {
        $price = $this->getPrice();

        return is_null($price) || $price === 0.0;
    }
}
