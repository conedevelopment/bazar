<?php

namespace Bazar\Repositories;

use Bazar\Contracts\Repositories\MenuRepository as Contract;
use Illuminate\Support\Str;

class MenuRepository extends Repository implements Contract
{
    /**
     * Register a new menu item.
     *
     * @param  string  $route
     * @param  string  $label
     * @param  array  $options
     * @return void
     */
    public function register(string $route, string $label, array $options = []): void
    {
        $options = array_replace_recursive([
            'items' => [],
            'label' => $label,
            'group' => __('Shop'),
            'icon' => 'dashboard',
        ], $options);

        $this->items->put($route, $options);
    }

    /**
     * Register a new resource menu item.
     *
     * @param  string  $route
     * @param  string  $label
     * @param  array  $options
     * @return void
     */
    public function resource(string $route, string $label, array $options = []): void
    {
        $route = trim($route, '/');

        $options = array_replace_recursive([
            'items' => [
                $route => __('All :resource', ['resource' => $label]),
                "{$route}/create" => __('Create :resource', ['resource' => Str::singular($label)]),
            ],
        ], $options);

        $this->register($route, $label, $options);
    }

    /**
     * Get all the grouped items.
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items->groupBy('group', true)->toArray();
    }
}
