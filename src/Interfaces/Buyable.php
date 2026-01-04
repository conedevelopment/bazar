<?php

declare(strict_types=1);

namespace Cone\Bazar\Interfaces;

use Cone\Bazar\Models\Item;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Buyable
{
    /**
     * Get the tax rates for the product.
     */
    public function taxRates(): MorphToMany;

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
