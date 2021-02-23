<?php

namespace Bazar\Cart;

use Bazar\Bazar;
use Bazar\Contracts\Models\Cart;
use Bazar\Contracts\Models\Product;
use Bazar\Contracts\Models\Shipping;
use Bazar\Events\CartTouched;
use Bazar\Models\Item;
use Bazar\Cart\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

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
     * @var \Bazar\Contracts\Models\Cart|null
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
    }

    /**
     * Get the cart model.
     *
     * @return \Bazar\Contracts\Models\Cart
     */
    public function model(): Cart
    {
        if (is_null($this->cart)) {
            $this->cart = App::call(function (Request $request): Cart {
                $cart = $this->resolve($request);

                if (! $cart->wasRecentlyCreated && ! $cart->locked && $cart->currency !== Bazar::currency()) {
                    $cart->fill(['currency' => Bazar::currency()])->save();
                }

                return $cart;
            });
        }

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
        return $this->model()->item($product, $properties);
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
                    ->associate($this->model())
                    ->save();
            });
        }

        CartTouched::dispatch($this->model());

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
        $id = array_map(static function ($item): string {
            return $item instanceof Item ? $item->id : $item;
        }, Arr::wrap($item));

        $this->model()->products()->wherePivotIn('id', $id)->detach();

        CartTouched::dispatch($this->model());
    }

    /**
     * Update the cart items.
     *
     * @param  array  $items
     * @return void
     */
    public function update(array $items = []): void
    {
        $this->model()->items->whereIn(
            'id', array_keys($items)
        )->each(static function (Item $item) use ($items): void {
            $item->update($items[$item->id]);
        });

        CartTouched::dispatch($this->model());
    }

    /**
     * Empty the cart.
     *
     * @return void
     */
    public function empty(): void
    {
        $this->model()->products()->detach();
        $this->model()->shipping->update(['tax' => 0, 'cost' => 0]);

        CartTouched::dispatch($this->model());
    }

    /**
     * Get the products that belong to the cart.
     *
     * @return \Illuminate\Support\Collection
     */
    public function products(): Collection
    {
        return $this->model()->products;
    }

    /**
     * Get the cart items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function items(): Collection
    {
        return $this->model()->items;
    }

    /**
     * Get the shipping that belongs to the cart.
     *
     * @return \Bazar\Contracts\Models\Shipping
     */
    public function shipping(): Shipping
    {
        return $this->model()->shipping;
    }

    /**
     * Get the number of the cart items.
     *
     * @return float
     */
    public function count(): float
    {
        return $this->model()->items->sum('quantity');
    }

    /**
     * Determine if the cart is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->model()->items->isEmpty();
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
     * @return \Bazar\Cart\Checkout
     */
    public function checkout(): Checkout
    {
        return new Checkout($this->model());
    }

    /**
     * Resolve the cart instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Bazar\Contracts\Models\Cart
     */
    abstract protected function resolve(Request $request): Cart;

    /**
     * Handle dynamic method calls into the driver.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->model()->{$method}(...$arguments);
    }
}
