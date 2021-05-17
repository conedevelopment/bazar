<?php

namespace Bazar\Cart;

use Bazar\Bazar;
use Bazar\Models\Address;
use Bazar\Models\Cart;
use Bazar\Models\Item;
use Bazar\Models\Product;
use Bazar\Models\Shipping;
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
                    if (! $cart->wasRecentlyCreated && ! $cart->locked && $cart->currency !== Bazar::currency()) {
                        $cart->setAttribute('currency', Bazar::currency())->save();
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
     * @param  \Bazar\Models\Product  $product
     * @param  float  $quantity
     * @param  array  $properties
     * @return \Bazar\Models\Item
     */
    public function addItem(Product $product, float $quantity = 1, array $properties = []): Item
    {
        if ($item = $this->getModel()->item($product, $properties)) {
            $item->update([
                'properties' => $properties,
                'quantity' => $item->quantity + $quantity,
            ]);
        } else {
            $item = Item::make(['quantity' => $quantity, 'properties' => $properties])
                        ->tap(function (Item $item) use ($product): void {
                            $item->product()
                                ->associate($product)
                                ->itemable()
                                ->associate($this->getModel())
                                ->save();
                        });
        }

        $this->refresh();

        return $item;
    }

    /**
     * Remove the given item from the cart.
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
     * Update the cart items.
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
     * Get the shipping that belongs to the cart.
     *
     * @return \Bazar\Models\Shipping
     */
    public function getShipping(): Shipping
    {
        return $this->getModel()->shipping;
    }

    /**
     * Refresh and recalculate the cart contents.
     *
     * @return void
     */
    public function refresh(): void
    {
        $this->getShipping()->cost(false);
        $this->getShipping()->tax(false);
        $this->getShipping()->save();

        $this->getModel()->discount(false);
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
        $this->getModel()->products()->detach();
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
