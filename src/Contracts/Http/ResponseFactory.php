<?php

namespace Bazar\Contracts\Http;

use Bazar\Http\Response;

interface ResponseFactory
{
    /**
     * Share data across components.
     *
     * @param  array|string  $key
     * @param  mixed $value
     * @return void
     */
    public function share($key, $value = null): void;

    /**
     * Get a shared data.
     *
     * @param  string|null  $key
     * @return  mixed
     */
    public function getShared(string $key = null);

    /**
     * Render the response.
     *
     * @param  string  $component
     * @param  array|\Arrayable  $props
     * @return \Bazar\Http\Response
     */
    public function render(string $component, $props = []): Response;
}
