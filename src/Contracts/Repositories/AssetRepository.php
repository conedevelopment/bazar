<?php

namespace Bazar\Contracts\Repositories;

interface AssetRepository
{
    /**
     * Register a new asset.
     *
     * @param  string  $path
     * @return void
     */
    public function register(string $path): void;

    /**
     * Get all the registerd scripts.
     *
     * @return array
     */
    public function scripts(): array;

    /**
     * Get all the registered styles.
     *
     * @return array
     */
    public function styles(): array;

    /**
     * Symlink the registered scripts and styles.
     *
     * @return void
     */
    public function link(): void;
}
