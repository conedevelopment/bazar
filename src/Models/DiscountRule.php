<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Enums\DiscountRuleType;
use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Interfaces\Models\DiscountRule as Contract;
use Cone\Root\Models\User;
use Cone\Root\Traits\InteractsWithProxy;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiscountRule extends Model implements Contract
{
    use InteractsWithProxy;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'active' => true,
        'stackable' => false,
        'type' => DiscountRuleType::CART,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'active',
        'name',
        'rules',
        'stackable',
        'type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_discount_rules';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Get the morph class for the model.
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'rules' => 'json',
            'stackable' => 'boolean',
            'type' => DiscountRuleType::class,
        ];
    }

    /**
     * Get the users associated with the discount rule.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::getProxiedClass(),
            'bazar_discount_rule_user',
            'discount_rule_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Validate the discount rule for the given discountable.
     */
    public function validate(Discountable $model): bool
    {
        return true;
    }

    /**
     * Calculate the discount for the given discountable.
     */
    public function calculate(Discountable $model): float
    {
        return 0.0;
    }

    /**
     * Apply the discount rule to the given discountable.
     */
    public function apply(Discountable $model): void
    {
        if (! $this->validate($model)) {
            throw new Exception('The discount rule is not valid for this discountable model.');
        }

        $value = $this->calculate($model);

        $model->discounts()->syncWithoutDetaching([
            $this->getKey() => ['value' => $value],
        ]);
    }
}
