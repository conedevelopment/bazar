<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Interfaces\Models\Discountable as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Discountable extends MorphPivot implements Contract
{
    use InteractsWithProxy;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_discountables';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }
}
