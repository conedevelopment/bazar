<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Stockable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Variant extends Breadcrumbable, Stockable
{
    /**
     * Get the product for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo;
}
