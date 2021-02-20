<?php

namespace Bazar\Contracts\Repositories;

interface MenuRepository
{
    /**
     * Register a new menu item.
     *
     * @param  string  $route
     * @param  string  $label
     * @param  array  $options
     * @return void
     */
    public function register(string $route, string $label, array $options = []): void;

    /**
     * Register a new resource menu item.
     *
     * @param  string  $route
     * @param  string  $label
     * @param  array  $options
     * @return void
     */
    public function resource(string $route, string $label, array $options = []): void;

    /**
     * Get all the grouped items.
     *
     * @return array
     */
    public function items(): array;
}
