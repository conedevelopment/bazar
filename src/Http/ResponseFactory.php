<?php

namespace Bazar\Http;

use Bazar\Contracts\Http\ResponseFactory as Contract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;

class ResponseFactory implements Contract
{
    use Macroable;

    /**
     * The shared data.
     *
     * @var array
     */
    protected $shared = [];

    /**
     * Share data across components.
     *
     * @param  array|string  $key
     * @param  mixed $value
     * @return void
     */
    public function share($key, $value = null): void
    {
        if (is_array($key)) {
            $this->shared = array_merge($this->shared, $key);
        } else {
            Arr::set($this->shared, $key, $value);
        }
    }

    /**
     * Get a shared data.
     *
     * @param  string|null  $key
     * @return  mixed
     */
    public function getShared(string $key = null)
    {
        if ($key) {
            return Arr::get($this->shared, $key);
        }

        return $this->shared;
    }

    /**
     * Render the response.
     *
     * @param  string  $component
     * @param  array|\Arrayable  $props
     * @return \Bazar\Http\Response
     */
    public function render(string $component, $props = []): Response
    {
        if ($props instanceof Arrayable) {
            $props = $props->toArray();
        }

        return new Response(
            $component, array_merge($this->shared, $props)
        );
    }
}
