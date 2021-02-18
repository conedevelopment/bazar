<?php

namespace Bazar\Repositories;

use Bazar\BazarServiceProvider;
use Bazar\Contracts\Repositories\AssetRepository as Contract;
use Illuminate\Support\Facades\URL;

class AssetRepository extends Repository implements Contract
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
    public function register(string $name, string $source, string $type, array $options = []): void
    {
        $options = array_replace($options, compact('source', 'type'));

        $this->items->put(
            $name, array_merge($this->items->get($name, []), [$options])
        );
    }

    /**
     * Register a new script.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array  $options
     * @return void
     */
    public function script(string $name, string $source, array $options = []): void
    {
        $path = sprintf('vendor/%s/%s', $name, basename($source));

        $this->register($name, $source, 'script', array_replace([
            'url' => URL::asset($path),
            'target' => $path = public_path($path),
        ], $options));

        BazarServiceProvider::$publishes[BazarServiceProvider::class][$source] = $path;
        BazarServiceProvider::$publishGroups['bazar-assets'][$source] = $path;
    }

    /**
     * Register a new style.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array  $options
     * @return void
     */
    public function style(string $name, string $source, array $options = []): void
    {
        $path = sprintf('vendor/%s/%s', $name, basename($source));

        $this->register($name, $source, 'style', array_replace([
            'url' => URL::asset($path),
            'target' => $path = public_path($path),
        ], $options));

        BazarServiceProvider::$publishes[BazarServiceProvider::class][$source] = $path;
        BazarServiceProvider::$publishGroups['bazar-assets'][$source] = $path;
    }

    /**
     * Register a new icon.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  array  $options
     * @return void
     */
    public function icon(string $name, string $source, array $options = []): void
    {
        $this->register($name, $source, 'icon', $options);
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
}
