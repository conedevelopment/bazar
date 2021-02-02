<?php

namespace Bazar\Repositories;

use Bazar\Contracts\Repositories\AssetRepository as Contract;
use Illuminate\Support\Facades\File;
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
            'target' => public_path($path),
        ], $options));
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
            'target' => public_path($path),
        ], $options));
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

    /**
     * Symlink the registered scripts and styles.
     *
     * @return void
     */
    public function link(): void
    {
        $this->items->each(static function (array $assets, string $name): void {
            $assets = array_filter($assets, static function (array $asset): bool {
                return $asset['type'] !== 'icon'
                    && file_exists($asset['source'])
                    && ! file_exists($asset['target']);
            });

            File::ensureDirectoryExists(public_path("vendor/{$name}"));

            foreach ($assets as $asset) {
                symlink($asset['source'], $asset['target']);
            }
        });
    }
}
