<?php

namespace Bazar\Cart;

use Bazar\Contracts\Models\Cart;
use Bazar\Contracts\Models\Product;
use Bazar\Contracts\Models\Shipping;
use Bazar\Events\CartTouched;
use Bazar\Models\Item;
use Bazar\Proxies\Cart as CartProxy;
use Bazar\Services\Checkout;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The cart instance.
     *
     * @var \Bazar\Contracts\Models\Cart
     */
    protected $cart;

    /**
     * Create a new driver instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;

        $this->cart = $this->retrieve();
    }

    /**
     * Get the cart model.
     *
     * @return \Bazar\Contracts\Models\Cart
     */
    public function model(): Cart
    {
        return $this->cart;
    }

    /**
     * Get the item by the product and its properties.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  array  $properties
     * @return \Bazar\Models\Item|null
     */
    public function item(Product $product, array $properties = []): ?Item
    {
        return $this->cart->item($product, $properties);
    }

    /**
     * Add the product with the given properties to the cart.
     *
     * @param  \Bazar\Contracts\Models\Product  $product
     * @param  float  $quantity
     * @param  array  $properties
     * @return \Bazar\Models\Item
     */
    public function add(Product $product, float $quantity = 1, array $properties = []): Item
    {
        if ($item = $this->item($product, $properties)) {
            $item->update(compact('properties') + [
                'quantity' => $item->quantity + $quantity,
            ]);
        } else {
            $item = tap(Item::make(compact('quantity', 'properties')), function (Item $item) use ($product): void {
                $item->product()
                    ->associate($product)
                    ->itemable()
                    ->associate($this->cart)
                    ->save();
            });
        }

        CartTouched::dispatch($this->cart);

        return $item;
    }

    /**
     * Remove the given items from the cart.
     *
     * @param  \Bazar\Models\Item|int|array  $item
     * @return void
     */
    public function remove($item): void
    {
        $ids = array_map(static function ($item): string {
            return $item instanceof Item ? $item->id : $item;
        }, Arr::wrap($item));

        $this->cart->products()->wherePivotIn('id', $ids)->detach();

        CartTouched::dispatch($this->cart);
    }

    /**
     * Update the cart items.
     *
     * @param  array  $items
     * @return void
     */
    public function update(array $items = []): void
    {
        $this->cart->items->whereIn(
            'id', array_keys($items)
        )->each(static function (Item $item) use ($items): void {
            $item->update($items[$item->id]);
        });

        CartTouched::dispatch($this->cart);
    }

    /**
     * Empty the cart.
     *
     * @return void
     */
    public function empty(): void
    {
        $this->cart->products()->detach();
        $this->cart->shipping->update(['tax' => 0, 'cost' => 0]);

        CartTouched::dispatch($this->cart);
    }

    /**
     * Get the products that belong to the cart.
     *
     * @return \Illuminate\Support\Collection
     */
    public function products(): Collection
    {
        return $this->cart->products;
    }

    /**
     * Get the cart items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function items(): Collection
    {
        return $this->cart->items;
    }

    /**
     * Get the shipping that belongs to the cart.
     *
     * @return \Bazar\Contracts\Models\Shipping
     */
    public function shipping(): Shipping
    {
        return $this->cart->shipping;
    }

    /**
     * Get the number of the cart items.
     *
     * @return float
     */
    public function count(): float
    {
        return $this->cart->items->sum('quantity');
    }

    /**
     * Determine if the cart is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->cart->items->isEmpty();
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
     * Initialize a checkout instance.
     *
     * @return \Bazar\Services\Checkout
     */
    public function checkout(): Checkout
    {
        return new Checkout($this->cart);
    }

    /**
     * Retrieve the cart instance.
     *
     * @return \Bazar\Contracts\Models\Cart
     */
    protected function retrieve(): Cart
    {
        $user = Auth::user();

        $cart = $this->resolve()->setRelation('user', $user)->loadMissing([
            'shipping', 'products', 'products.media', 'products.variations',
        ]);

        if ($user && $cart->user_id !== $user->id) {
            CartProxy::query()->where('user_id', $user->id)->update(['user_id' => null]);

            $cart->user()->associate($user)->save();
        }

        return $cart;
    }

    /**
     * Resolve the cart instance.
     *
     * @return \Bazar\Contracts\Models\Cart
     */
    abstract protected function resolve(): Cart;

    /**
     * Handle dynamic method calls into the driver.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->cart->{$method}(...$arguments);
    }
}
