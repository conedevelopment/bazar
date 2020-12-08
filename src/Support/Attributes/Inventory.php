<?php

namespace Bazar\Support\Attributes;

use Illuminate\Support\Facades\Config;

class Inventory extends Bag
{
    /**
     * The inventory items.
     *
     * @var array
     */
    protected $items = [
        'files' => [],
        'sku' => null,
        'width' => null,
        'height' => null,
        'length' => null,
        'weight' => null,
        'quantity' => null,
        'virtual' => false,
        'downloadable' => false,
    ];

    /**
     * Create a new inventory instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        parent::__construct(array_replace($this->items, $items));
    }

    /**
     * Get the formatted dimensions.
     *
     * @param  string  $glue
     * @return string|null
     */
    public function formattedDimensions(string $glue = 'x'): ?string
    {
        $dimensions = array_filter([$this->get('length'), $this->get('width'), $this->get('height')]);

        if (empty($dimensions)) {
            return null;
        }

        return sprintf('%s %s', implode($glue, $dimensions), Config::get('bazar.dimension_unit'));
    }

    /**
     * Get the formatted weight.
     *
     * @return string|null
     */
    public function formattedWeight(): ?string
    {
        if (! $weight = $this->get('weight')) {
            return null;
        }

        return sprintf('%s %s', $weight, Config::get('bazar.weight_unit'));
    }

    /**
     * Determine if the stockable model is virtual.
     *
     * @return bool
     */
    public function virtual(): bool
    {
        return (bool) $this->get('virtual', false);
    }

    /**
     * Determine if the stockable model is downloadable.
     *
     * @return bool
     */
    public function downloadable(): bool
    {
        return (bool) $this->get('downloadable', false);
    }

    /**
     * Determine if the stockable model tracks quantity.
     *
     * @return bool
     */
    public function tracksQuantity(): bool
    {
        return ! is_null($this->get('quantity'));
    }

    /**
     * Determine if the stockable model is available.
     *
     * @param  float  $quantity
     * @return bool
     */
    public function available(float $quantity = 1): bool
    {
        $stock = $this->get('quantity', 0);

        return ! $this->tracksQuantity() || (min($stock, $quantity) > 0 && $stock >= $quantity);
    }

    /**
     * Increment the quantity by the given value.
     *
     * @param  float  $quantity
     * @return void
     */
    public function incrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $this->set('quantity', $this->get('quantity', 0) + $quantity);
        }
    }

    /**
     * Decrement the quantity by the given value.
     *
     * @param  float  $quantity
     * @return void
     */
    public function decrementQuantity(float $quantity = 1): void
    {
        if ($this->tracksQuantity()) {
            $this->set('quantity', max($this->get('quantity', 0) - $quantity, 0));
        }
    }
}
