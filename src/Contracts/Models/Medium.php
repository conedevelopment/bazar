<?php

namespace Bazar\Contracts\Models;

interface Medium
{
    /**
     * Determine if the file is image.
     *
     * @return bool
     */
    public function getIsImageAttribute(): bool;

    /**
     * Get the conversion URLs.
     *
     * @return array
     */
    public function getUrlsAttribute(): array;

    /**
     * Perform the conversions on the medium.
     *
     * @return $this
     */
    public function convert(): self;

    /**
     * Get the path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function path(string $conversion = null): string;

    /**
     * Get the full path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function fullPath(string $conversion = null): string;

    /**
     * Get the url to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function url(string $conversion = null): string;
}
