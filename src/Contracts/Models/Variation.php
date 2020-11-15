<?php

namespace Bazar\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Variation
{
    /**
     * Get the product for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo;

    /**
     * Get the alias attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getAliasAttribute(string $value = null): ?string;

    /**
     * Get the option attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getOptionAttribute(string $value): array;
}
