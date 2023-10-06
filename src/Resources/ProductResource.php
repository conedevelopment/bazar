<?php

namespace Cone\Bazar\Resources;

use Cone\Bazar\Models\Product;
use Cone\Root\Resources\Resource;

class ProductResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = Product::class;
}
