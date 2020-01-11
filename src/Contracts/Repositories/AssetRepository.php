<?php

namespace Bazar\Contracts\Repositories;

interface AssetRepository
{
    /**
     * Register a new asset.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  string  $type
     * @return void
     */
    public function regsiter(string $name, string $path, string $type): void;

    /**
     * Register a new style.
     *
     * @param  string  $name
     * @param  string  $path
     * @return void
     */
    public function style(string $name, string $path): void;

    /**
     * Register a new script.
     *
     * @param  string  $name
     * @param  string  $path
     * @return void
     */
    public function script(string $name, string $path): void;

    /**
     * Get all the registerd scripts.
     *
     * @return array
     */
    public function scripts(): array;

    /**
     * Get all the registerd styles.
     *
     * @return array
     */
    public function styles(): array;

    /**
     * Publish registered assets.
     *
     * @param  bool  $force
     * @return void
     */
    public function publish(bool $force = false): void;
}
