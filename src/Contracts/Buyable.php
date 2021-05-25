<?php

namespace Bazar\Contracts;

interface Buyable
{
    /**
     * Get the buyable ID.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $properties
     * @return int|null
     */
    public function getBuyableId(Itemable $itemable, array $properties = []): ?int;

    /**
     * Get the buyable type.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $properties
     * @return string|null
     */
    public function getBuyableType(Itemable $itemable, array $properties = []): ?string;

    /**
     * Get the buyable price.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $properties
     * @return float
     */
    public function getBuyablePrice(Itemable $itemable, array $properties = []): float;

    /**
     * Get the buyable name.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $properties
     * @return string
     */
    public function getBuyableName(Itemable $itemable, array $properties = []): string;

    /**
     * Get the buyable quantity.
     *
     * @param  \Bazar\Contracts\Itemable  $itemable
     * @param  array  $properties
     * @return float|null
     */
    public function getBuyableQuantity(Itemable $itemable, array $properties = []): ?float;
}
