<?php

namespace Bazar\Repositories;

use Bazar\Contracts\Repositories\AssetRepository as Contract;

class AssetRepository extends Repository implements Contract
{
    /**
     * Register a new asset.
     *
     * @param  string  $path
     * @return void
     */
    public function register(string $path): void
    {
        $this->items->push($path);
    }

    /**
     * Get all the registerd scripts.
     *
     * @return array
     */
    public function scripts(): array
    {
        return $this->items->filter(static function (string $path): bool {
            return preg_match('/\.js$/', $path);
        })->toArray();
    }

    /**
     * Get all the registered styles.
     *
     * @return array
     */
    public function styles(): array
    {
        return $this->items->filter(static function (string $path): bool {
            return preg_match('/\.css$/', $path);
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
