<?php

namespace Bazar\Contracts\Models;

interface Medium
{
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
    public function getPath(?string $conversion = null): string;

    /**
     * Get the full path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getFullPath(?string $conversion = null): string;

    /**
     * Get the url to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getUrl(?string $conversion = null): string;
}
