<?php

namespace Bazar\Cart;

use Bazar\Events\CartTouched;
use Bazar\Models\Cart;
use Bazar\Models\Item;
use Bazar\Models\Product;
use Bazar\Models\Shipping;
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
     * @var \Bazar\Models\Cart
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
     * @return \Bazar\Models\Cart
     */
    public function model(): Cart
    {
        return $this->cart;
    }

    /**
     * Get the item by the product and its properties.
     *
     * @param  \Bazar\Models\Product  $product
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
     * @param  \Bazar\Models\Product  $product
     * @param  float  $quantity
     * @param  array  $properties
     * @return void
     */
    public function add(Product $product, float $quantity = 1, array $properties = []): void
    {
        if ($item = $this->item($product, $properties)) {
            $item->setRelation('product', $product)->update(compact('properties') + [
                'quantity' => $item->quantity + $quantity,
            ]);
        } else {
            Item::make(compact('quantity', 'properties'))->forceFill([
                'product_id' => $product->id,
                'itemable_type' => Cart::class,
                'itemable_id' => $this->cart->id,
            ])->setRelation('product', $product)->save();
        }

        CartTouched::dispatch($this->cart);
    }

    /**
     * Remove the given items from the cart.
     *
     * @param  \Bazar\Models\Item|int|array  $item
     * @return void
     */
    public function remove($item): void
    {
        $ids = array_map(function ($item) {
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
        $this->cart->products->whereIn(
            'item.id', array_keys($items)
        )->each(function (Product $product) use ($items) {
            $product->item
                ->setRelation('product', $product)
                ->update($items[$product->item->id]);
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
     * @return \Bazar\Models\Shipping
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
     * @return \Bazar\Models\Cart
     */
    protected function retrieve(): Cart
    {
        $user = Auth::user();

        $cart = $this->resolve()->setRelation('user', $user)->loadMissing([
            'shipping', 'products', 'products.media', 'products.variations',
        ]);

        if ($user && $cart->user_id !== $user->id) {
            Cart::where('user_id', $user->id)->update(['user_id' => null]);

            $cart->user()->associate($user)->save();
        }

        return $cart;
    }

    /**
     * Resolve the cart instance.
     *
     * @return \Bazar\Models\Cart
     */
    abstract protected function resolve(): Cart;

    /**
     * Dynamically call methods.
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
