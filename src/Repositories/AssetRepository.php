<?php

namespace Bazar\Repositories;

use Bazar\Contracts\Repositories\AssetRepository as Contract;

class AssetRepository extends Repository implements Contract
{
    /**
     * Register a new asset.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  string  $type
     * @return void
     */
    public function register(string $name, string $path, string $type): void
    {
        $this->items->put("{$name}-{$type}", $path);
    }

    /**
     * Register a new script.
     *
     * @param  string  $name
     * @param  string  $path
     * @return void
     */
    public function script(string $name, string $path): void
    {
        $this->register($name, $path, 'script');
    }

    /**
     * Register a new style.
     *
     * @param  string  $name
     * @param  string  $path
     * @return void
     */
    public function style(string $name, string $path): void
    {
        $this->register($name, $path, 'style');
    }

    /**
     * Get all the registerd scripts.
     *
     * @return array
     */
    public function scripts(): array
    {
        return $this->items->filter(static function (string $path, string $name): bool {
            return preg_match('/script$/', $name);
        })->toArray();
    }

    /**
     * Get all the registered styles.
     *
     * @return array
     */
    public function styles(): array
    {
        return $this->items->filter(static function (string $path, string $name): bool {
            return preg_match('/style$/', $name);
        })->toArray();
    }
}
