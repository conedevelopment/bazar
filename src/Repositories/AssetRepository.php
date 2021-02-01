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
    public function register(string $name, string $path, string $type, array $options = []): void
    {
        $options = array_replace($options, compact('path', 'type'));

        $this->items->put(
            $name, array_merge($this->items->get($name, []), [$options])
        );
    }

    /**
     * Register a new script.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  array  $options
     * @return void
     */
    public function script(string $name, string $path, array $options = []): void
    {
        $this->register($name, $path, 'script', $options);
    }

    /**
     * Register a new style.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  array  $options
     * @return void
     */
    public function style(string $name, string $path, array $options = []): void
    {
        $this->register($name, $path, 'style', $options);
    }

    /**
     * Register a new icon.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  array  $options
     * @return void
     */
    public function icon(string $name, string $path, array $options = []): void
    {
        $this->register($name, $path, 'icon', $options);
    }

    /**
     * Get all the registerd scripts.
     *
     * @return array
     */
    public function scripts(): array
    {
        return $this->items->flatMap(static function (array $assets): array {
            return array_filter($assets, static function (array $asset): bool {
                return $asset['type'] === 'script';
            });
        })->toArray();
    }

    /**
     * Get all the registered styles.
     *
     * @return array
     */
    public function styles(): array
    {
        return $this->items->flatMap(static function (array $assets): array {
            return array_filter($assets, static function (array $asset): bool {
                return $asset['type'] === 'style';
            });
        })->toArray();
    }

    /**
     * Get all the registered icons.
     *
     * @return array
     */
    public function icons(): array
    {
        return $this->items->flatMap(static function (array $assets): array {
            return array_filter($assets, static function (array $asset): bool {
                return $asset['type'] === 'icon';
            });
        })->toArray();
    }

    /**
     * Symlink the registered scripts and styles.
     *
     * @return void
     */
    public function link(): void
    {
        //
    }
}
