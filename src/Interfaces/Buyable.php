<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Item;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

interface Buyable extends Priceable
{
    /**
     * Get the tax rates for the product.
     */
    public function taxRates(): MorphToMany;

    /**
     * Get the applicable tax rates for the buyable instance.
     */
    public function getApplicableTaxRates(): Collection;

    /**
     * Determine whether the buyable object is available for the checkoutable instance.
     */
    public function buyable(Checkoutable $checkoutable): bool;

    /**
     * Get the name of the buyable instance.
     */
    public function getBuyableName(): string;

    /**
     * Get the item representation of the buyable instance.
     */
    public function toItem(Checkoutable $checkoutable, array $attributes = []): Item;
}
