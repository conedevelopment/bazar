<?php

namespace Bazar\Contracts\Repositories;

interface AssetRepository
{
    /**
     * Register a new asset.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  string  $type
     * @param  array  $options
     * @return void
     */
    public function register(string $name, string $source, string $type, array $options = []): void;

    /**
     * Register a new script.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array  $options
     * @return void
     */
    public function script(string $name, string $source, array $options = []): void;

    /**
     * Register a new style.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array  $options
     * @return void
     */
    public function style(string $name, string $source, array $options = []): void;

    /**
     * Register a new icon.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array  $options
     * @return void
     */
    public function icon(string $name, string $source, array $options = []): void;

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
     * Get all the registered icons.
     *
     * @return array
     */
    public function icons(): array;

    /**
     * Symlink the registered scripts and styles.
     *
     * @return void
     */
    public function link(): void;
}
