<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Interfaces\Models\Discount as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Discount extends MorphPivot implements Contract
{
    use InteractsWithProxy;

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'value' => 0,
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
    protected $table = 'bazar_discounts';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * {@inheritdoc}
     */
    public function casts(): array
    {
        return [
            'value' => 'float',
        ];
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
        return $this->pivotParent?->discountable?->getCurrency()?->format($this->value) ?: '';
    }
}
