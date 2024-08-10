<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\DiscountFactory;
use Cone\Bazar\Interfaces\Models\Discount as Contract;
use Cone\Bazar\Support\Currency;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discount extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

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
    protected $table = 'bazar_discounts';

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
        return DiscountFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'float',
        ];
    }

    /**
     * Get the discount rate for the model.
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(DiscountRate::getProxiedClass(), 'discount_rate_id');
    }

    /**
     * Get the discountable model.
     */
    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the formatted value attribute.
     */
    protected function formattedValue(): Attribute
    {
        return new Attribute(
            get: function (): string {
                return (new Currency($this->value, $this->discountable->currency))->format();
            }
        );
    }
}
