<?php

namespace Cone\Bazar\Repositories;

use Illuminate\Support\Collection;

abstract class Repository
{
    /**
     * The repository items.
     */
    protected Collection $items;

    /**
     * Create a new repository instance.
     */
    public function __construct(array $items = [])
    {
        $this->items = Collection::make($items);
    }

    /**
     * Remove the item by the given name.
     */
    public function remove(string $name): void
    {
        $this->items->forget($name);
    }

    /**
     * Dynamically call methods.
     */
    public function __call(string $method, array $arguments): mixed
    {
        return call_user_func_array([$this->items, $method], $arguments);
    }
}
