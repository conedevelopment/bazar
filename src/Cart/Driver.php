<?php

namespace Cone\Bazar\Cart;

use Cone\Bazar\Bazar;
use Cone\Bazar\Interfaces\Buyable;
use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Support\Facades\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

abstract class Driver
{
    /**
     * The driver config.
     */
    protected array $config = [];

    /**
     * The cart instance.
     */
    protected ?Cart $cart = null;

    /**
     * Create a new driver instance.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Resolve the cart instance.
     */
    abstract protected function resolve(Request $request): Cart;

    /**
     * The callback after the cart instance is resolved.
     */
    protected function resolved(Request $request, Cart $cart): void
    {
        if (! $cart->exists || ($request->user() && $cart->user_id !== $request->user()->getKey())) {
            $cart->user()->associate($request->user())->save();
        }

        $cart->loadMissing(['items', 'items.buyable']);
    }

    /**
     * Get the cart model.
     */
    public function getModel(): Cart
    {
        if (is_null($this->cart)) {
            $this->cart = App::call(function (Request $request): Cart {
                return tap($this->resolve($request), function (Cart $cart) use ($request): void {
                    $this->resolved($request, $cart);
                });
            });
        }

        return tap($this->cart, static function (Cart $cart): void {
            if (! $cart->locked && $cart->currency !== Bazar::getCurrency()) {
                $cart->setAttribute('currency', Bazar::getCurrency());
                $cart->syncItems();
                $cart->shipping->calculateCost(false);
                $cart->shipping->calculateTax();
                $cart->calculateDiscount();
            }
        });
    }

    /**
     * Get the item with the given id.
     */
    public function getItem(string $id): ?Item
    {
        return $this->getItems()->firstWhere('id', $id);
    }

    /**
     * Add the product with the given properties to the cart.
     */
    public function addItem(Buyable $buyable, float $quantity = 1, array $properties = []): Item
    {
        $item = $buyable->toItem(
            $this->getModel(),
            ['quantity' => $quantity, 'properties' => $properties]
        );

        $this->getModel()->mergeItem($item)->save();

        $this->sync();

        return $item;
    }

    /**
     * Remove the given cart item.
     */
    public function removeItem(string $id): void
    {
        if ($item = $this->getItem($id)) {
            $item->delete();

            $key = $this->getItems()->search(static function (Item $item) use ($id) {
                return $item->getKey() === $id;
            });

            $this->getItems()->forget($key);

            $this->sync();
        }
    }

    /**
     * Remove the given cart items.
     */
    public function removeItems(array $ids): void
    {
        $count = $this->getModel()->items()->whereIn('id', $ids)->delete();

        if ($count > 0) {
            $keys = $this->getItems()->reduce(static function (array $keys, Item $item, int $key) use ($ids): array {
                return in_array($item->getKey(), $ids) ? array_merge($keys, [$key]) : $keys;
            }, []);

            $this->getItems()->forget($keys);

            $this->sync();
        }
    }

    /**
     * Update the given cart item.
     */
    public function updateItem(string $id, array $properties = []): void
    {
        if ($item = $this->getItem($id)) {
            $item->fill($properties)->calculateTax();

            $this->sync();
        }
    }

    /**
     * Update the given cart items.
     */
    public function updateItems(array $data): void
    {
        $items = $this->getItems()->whereIn('id', array_keys($data));

        $items->each(static function (Item $item) use ($data): void {
            $item->fill($data[$item->getKey()])->calculateTax();
        });

        if ($items->isNotEmpty()) {
            $this->sync();
        }
    }

    /**
     * Get the cart items.
     */
    public function getItems(): Collection
    {
        return $this->getModel()->items;
    }

    /**
     * Get the billing address that belongs to the cart.
     */
    public function getBilling(): Address
    {
        return $this->getModel()->address;
    }

    /**
     * Update the billing address.
     */
    public function updateBilling(array $attributes): void
    {
        $this->getBilling()->fill($attributes)->save();

        $this->sync();
    }

    /**
     * Get the shipping that belongs to the cart.
     */
    public function getShipping(): Shipping
    {
        return $this->getModel()->shipping;
    }

    /**
     * Update the shipping address and driver.
     */
    public function updateShipping(array $attributes = [], string $driver = null): void
    {
        if (! is_null($driver)) {
            $this->getShipping()->setAttribute('driver', $driver);
        }

        $this->getShipping()->address->fill($attributes);

        if (! empty($attributes) || ! is_null($driver)) {
            $this->sync();
        }

        $this->getShipping()->address->save();
    }

    /**
     * Empty the cart.
     */
    public function empty(): void
    {
        $this->getModel()->items()->delete();
        $this->getModel()->setRelation('items', $this->getModel()->items()->getRelated()->newCollection());

        $this->getShipping()->update(['tax' => 0, 'cost' => 0]);

        $this->getModel()->calculateDiscount();
    }

    /**
     * Get the number of the cart items.
     */
    public function count(): float
    {
        return $this->getItems()->sum('quantity');
    }

    /**
     * Determine if the cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->getItems()->isEmpty();
    }

    /**
     * Perform the checkout using the given driver.
     */
    public function checkout(string $driver): Order
    {
        return App::call(function (Request $request) use ($driver): Order {
            return Gateway::driver($driver)->checkout($request, $this->getModel());
        });
    }

    /**
     * Determine if the cart is not empty.
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Sync the cart.
     */
    public function sync(): void
    {
        $this->getShipping()->calculateCost(false);
        $this->getShipping()->calculateTax();

        $this->getModel()->calculateDiscount();
    }

    /**
     * Handle dynamic method calls into the driver.
     */
    public function __call(string $method, array $parameters): mixed
    {
        return call_user_func_array([$this->getModel(), $method], $parameters);
    }
}
