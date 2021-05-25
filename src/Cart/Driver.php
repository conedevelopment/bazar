<?php

namespace Bazar\Cart;

use Bazar\Bazar;
use Bazar\Contracts\Buyable;
use Bazar\Models\Address;
use Bazar\Models\Cart;
use Bazar\Models\Item;
use Bazar\Models\Order;
use Bazar\Models\Shipping;
use Bazar\Support\Facades\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * The cart instance.
     *
     * @var \Bazar\Models\Cart|null
     */
    protected ?Cart $cart = null;

    /**
     * Create a new driver instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Resolve the cart instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Bazar\Models\Cart
     */
    abstract protected function resolve(Request $request): Cart;

    /**
     * Get the cart model.
     *
     * @return \Bazar\Models\Cart
     */
    public function getModel(): Cart
    {
        if (is_null($this->cart)) {
            $this->cart = App::call(function (Request $request): Cart {
                return tap($this->resolve($request), static function (Cart $cart): void {
                    if (! $cart->wasRecentlyCreated && ! $cart->locked && $cart->currency !== Bazar::getCurrency()) {
                        $cart->setAttribute('currency', Bazar::getCurrency())->save();
                    }
                });
            });
        }

        return $this->cart;
    }

    /**
     * Get the item with the given id.
     *
     * @param  string  $id
     * @return \Bazar\Models\Item|null
     */
    public function getItem(string $id): ?Item
    {
        return $this->getItems()->firstWhere('id', $id);
    }

    /**
     * Add the product with the given properties to the cart.
     *
     * @param  \Bazar\Contracts\Buyable  $buyable
     * @param  float  $quantity
     * @param  array  $properties
     * @return \Bazar\Models\Item
     */
    public function addItem(Buyable $buyable, float $quantity = 1, array $properties = []): Item
    {
        $item = $this->getModel()->findItemOrNew([
            'properties' => $properties,
            'buyable_id' => $buyable->id,
            'buyable_type' => get_class($buyable),
            'itemable_id' => $this->getModel()->id,
            'itemable_type' => get_class($this->getModel()),
        ]);

        $item->quantity += $quantity;

        $item->save();

        $this->refresh();

        return $item;
    }

    /**
     * Remove the given cart item.
     *
     * @param  string  $id
     * @return void
     */
    public function removeItem(string $id): void
    {
        if ($item = $this->getItem($id)) {
            $item->delete();

            $this->refresh();
        }
    }

    /**
     * Remove the given cart items.
     *
     * @param  array  $ids
     * @return void
     */
    public function removeItems(array $ids): void
    {
        $count = $this->getModel()->items()->whereIn('id', $ids)->delete();

        if ($count > 0) {
            $this->refresh();
        }
    }

    /**
     * Update the given cart item.
     *
     * @param  string  $id
     * @param  array  $properties
     * @return void
     */
    public function updateItem(string $id, array $properties = []): void
    {
        if ($item = $this->getItem($id)) {
            $item->fill($properties)->save();

            $this->refresh();
        }
    }

    /**
     * Update the given cart items.
     *
     * @param  array  $data
     * @return void
     */
    public function updateItems(array $data): void
    {
        $items = $this->getItems()->whereIn('id', array_keys($data));

        $items->each(static function (Item $item) use ($data): void {
            $item->fill($data[$item->id])->save();
        });

        if ($items->isNotEmpty()) {
            $this->refresh();
        }
    }

    /**
     * Get the cart items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getItems(): Collection
    {
        return $this->getModel()->items;
    }

    /**
     * Get the billing address that belongs to the cart.
     *
     * @return \Bazar\Models\Address
     */
    public function getBilling(): Address
    {
        return $this->getModel()->address;
    }

    /**
     * Update the billing address.
     *
     * @param  array  $attributes
     * @return void
     */
    public function updateBilling(array $attributes): void
    {
        $this->getBilling()->fill($attributes)->save();

        $this->refresh();
    }

    /**
     * Get the shipping that belongs to the cart.
     *
     * @return \Bazar\Models\Shipping
     */
    public function getShipping(): Shipping
    {
        return $this->getModel()->shipping;
    }

    /**
     * Update the shipping address and driver.
     *
     * @param  array  $attributes
     * @param  string|null  $driver
     * @return void
     */
    public function updateShipping(array $attributes, ?string $driver = null): void
    {
        $this->getShipping()->address->fill($attributes)->save();

        if (! is_null($driver)) {
            $this->getShipping()->setAttribute('driver', $driver);
        }

        $this->refresh();
    }

    /**
     * Refresh and recalculate the cart contents.
     *
     * @return void
     */
    public function refresh(): void
    {
        $this->getShipping()->calculateCost(false);
        $this->getShipping()->calculateTax(false);
        $this->getShipping()->save();

        $this->getModel()->calculateDiscount(false);
        $this->getModel()->save();

        $this->getModel()->refresh();
    }

    /**
     * Empty the cart.
     *
     * @return void
     */
    public function empty(): void
    {
        $this->getModel()->items()->delete();
        $this->getModel()->shipping->update(['tax' => 0, 'cost' => 0]);

        $this->refresh();
    }

    /**
     * Get the number of the cart items.
     *
     * @return float
     */
    public function count(): float
    {
        return $this->getItems()->sum('quantity');
    }

    /**
     * Determine if the cart is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->getItems()->isEmpty();
    }

    /**
     * Perform the checkout using the given driver.
     *
     * @param  string  $driver
     * @return \Bazar\Models\Order
     */
    public function checkout(string $driver): Order
    {
        return App::call(function (Request $request) use ($driver): Order {
            return Gateway::driver($driver)->checkout($request, $this->getModel());
        });
    }

    /**
     * Determine if the cart is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Handle dynamic method calls into the driver.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([$this->getModel(), $method], $parameters);
    }
}
