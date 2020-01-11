<?php

namespace Bazar\Support\Facades;

use Bazar\Contracts\Repositories\AssetRepository;
use Illuminate\Support\Facades\Facade;

class Asset extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return AssetRepository::class;
    }
}
