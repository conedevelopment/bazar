<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Interfaces\Models\TaxRate as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }
}
