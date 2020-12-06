<?php

namespace Bazar\Repositories;

use Bazar\Contracts\Models\Medium;
use Bazar\Contracts\Repositories\ConversionRepository as Contract;
use Bazar\Services\Image;
use Closure;

class ConversionRepository extends Repository implements Contract
{
    /**
     * Create a new repository instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $items = array_replace([
            'thumb' => static function (Image $image): void {
                $image->crop(500, 500);
            },
            'medium' => static function (Image $image): void {
                $image->resize(1400, 1000);
            },
        ], $items);

        parent::__construct($items);
    }

    /**
     * Register a new conversion.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public function register(string $name, Closure $callback): void
    {
        $this->items->put($name, $callback);
    }

    /**
     * Perform the registered conversion on the given medium.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return \Bazar\Contracts\Models\Medium
     */
    public function perform(Medium $medium): Medium
    {
        $this->items->each(static function (Closure $callback, string $name) use ($medium): void {
            $image = Image::make($medium);

            call_user_func_array($callback, [$image]);

            $image->save($name);
        });

        return $medium;
    }
}
