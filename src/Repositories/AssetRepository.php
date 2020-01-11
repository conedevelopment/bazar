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
    public function regsiter(string $name, string $path, string $type): void
    {
        $this->items->put("{$type}:{$name}", $path);
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
        $this->regsiter($name, $path, 'style');
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
        $this->regsiter($name, $path, 'script');
    }

    /**
     * Get all the registerd scripts.
     *
     * @return array
     */
    public function scripts(): array
    {
        return $this->items->filter(function (string $asset, string $key) {
            return preg_match('/^script:/', $key) > 0;
        })->mapWithKeys(function (string $script, string $key) {
            return [str_replace('script:', '', $key) => $script];
        })->all();
    }

    /**
     * Get all the registerd styles.
     *
     * @return array
     */
    public function styles(): array
    {
        return $this->items->filter(function (string $asset, string $key) {
            return preg_match('/^style:/', $key) > 0;
        })->mapWithKeys(function (string $style, string $key) {
            return [str_replace('style:', '', $key) => $style];
        })->all();
    }

    /**
     * Publish registered assets.
     *
     * @param  bool  $force
     * @return void
     */
    public function publish(bool $force = false): void
    {
        //
    }
}
