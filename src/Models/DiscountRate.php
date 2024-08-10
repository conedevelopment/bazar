<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\DiscountRateFactory;
use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Interfaces\Models\DiscountRate as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountRate extends Model implements Contract
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
    protected $table = 'bazar_discount_rates';

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
        return DiscountRateFactory::new();
    }

    /**
     * Determine wheter the discount rate is applicable on the model.
     */
    public function applicable(Discountable $model): bool
    {
        return true;
    }

    /**
     * Calculate the discount for the given model.
     */
    public function calculate(Discountable $model): float
    {
        return 0;
    }

    /**
     * Apply the discount rate on the model.
     */
    public function apply(Discountable $model): ?Discount
    {
        if (! $this->applicable($model)) {
            return null;
        }

        return $model->discounts()->updateOrCreate(
            ['discount_rate_id' => $this->getKey()],
            ['value' => $this->calculate($model)]
        );
    }
}
