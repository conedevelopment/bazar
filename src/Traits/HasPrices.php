<?php

declare(strict_types=1);

namespace Cone\Bazar\Traits;

use Cone\Bazar\Bazar;
use Cone\Bazar\Enums\Currency;
use Cone\Bazar\Models\Price;
use Cone\Bazar\Relations\Prices;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\HtmlString;

trait HasPrices
{
    /**
     * Get the prices for the model.
     */
    public function prices(): Prices
    {
        $query = $this->newRelatedInstance(Price::class)->newQuery();

        [$type, $id] = $this->getMorphs('metable', null, null);

        return new Prices(
            $query,
            $this,
            $query->qualifyColumn($type),
            $query->qualifyColumn($id),
            $this->getKeyName()
        );
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
    public function getPrice(?Currency $currency = null): ?float
    {
        $currency ??= Bazar::getCurrency();

        $key = sprintf('price_%s', $currency->key());

        return $this->prices->firstWhere('key', $key)?->value;
    }

    /**
     * Get the formatted price by the given type and currency.
     */
    public function getFormattedPrice(?Currency $currency = null): ?string
    {
        $currency ??= Bazar::getCurrency();

        $price = $this->getPrice($currency);

        return is_null($price) ? null : $currency->format($price);
    }

    /**
     * Get the price HTML representation.
     */
    public function getPriceHtml(?Currency $currency = null): HtmlString
    {
        return match (true) {
            $this->isFree($currency) => new HtmlString(__('Free')),
            default => new HtmlString($this->getFormattedPrice($currency)),
        };
    }

    /**
     * Determine if the stockable model is free.
     */
    public function isFree(?Currency $currency = null): bool
    {
        $price = $this->getPrice($currency);

        return is_null($price) || $price === 0.0;
    }
}
