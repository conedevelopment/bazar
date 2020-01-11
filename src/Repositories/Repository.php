<?php

namespace Bazar\Repositories;

abstract class Repository
{
    /**
     * The repository items.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->items = collect();
    }

    /**
     * Dynamically call methods.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->items->{$method}(...$arguments);
    }
}
