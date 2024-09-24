<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Interfaces\Models\Tax as Contract;
use Cone\Bazar\Support\Currency;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tax extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'value' => null,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'float',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'value',
    ];

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
     * Get the taxable model for the model.
     */
    public function taxable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the tax rate for the model.
     */
    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::getProxiedClass());
    }

    /**
     * Get the formatted value attribute.
     */
    protected function formattedValue(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->format()
        );
    }

    /**
     * Get the formatted tax.
     */
    public function format(): string
    {
        return (new Currency($this->value, $this->taxable?->checkoutable?->getCurrency()))->format();
    }
}
