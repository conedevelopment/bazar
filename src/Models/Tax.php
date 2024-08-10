<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\TaxFactory;
use Cone\Bazar\Interfaces\Models\Tax as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tax extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_taxes';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return TaxFactory::new();
    }

    /**
     * Get the tax rate for the model.
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::getProxiedClass(), 'tax_rate_id');
    }

    /**
     * Get the taxable model.
     */
    public function taxable(): MorphTo
    {
        return $this->morphTo();
    }
}
