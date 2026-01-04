<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\DiscountRuleFactory;
use Cone\Bazar\Enums\DiscountRuleType;
use Cone\Bazar\Enums\DiscountRuleValueType;
use Cone\Bazar\Enums\DiscountType;
use Cone\Bazar\Exceptions\DiscountException;
use Cone\Bazar\Interfaces\Discountable;
use Cone\Bazar\Interfaces\Models\DiscountRule as Contract;
use Cone\Bazar\Models\Discountable as DiscountablePivot;
use Cone\Root\Models\User;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

class DiscountRule extends Model implements Contract
{
    use HasFactory;
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
        'discountable_type' => null,
        'rules' => '[]',
        'stackable' => false,
        'value_type' => DiscountRuleValueType::TOTAL,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'active',
        'discountable_type',
        'name',
        'rules',
        'stackable',
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
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): DiscountRuleFactory
    {
        return DiscountRuleFactory::new();
    }
   
    /**
     * Get the discountable types.
     */
    public static function getDiscountableTypes(): array
    {
        return [
            Cart::getProxiedClass(),
            Shipping::getProxiedClass(),
            Product::getProxiedClass(),
            Variant::getProxiedClass(),
        ];
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
            'value_type' => DiscountRuleValueType::class,
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
     * Get the discountables associated with the discount rule.
     */
    public function discountables(): MorphToMany
    {
        return $this->morphedByMany(
            $this->discountable_type ?: static::getDiscountableTypes()[0],
            'discountable',
            'bazar_discountables',
            'discount_rule_id',
            'discountable_id'
        )->using(DiscountablePivot::class);
    }

    /**
     * Validate the discount rule for the given discountable.
     */
    public function validate(Discountable $model): bool
    {
        $type = match (true) {
            $model instanceof Item => $model->buyable_type,
            default => $model::class,
        };

        return $this->active
            && in_array($type, static::getDiscountableTypes())
            && $this->discountable_type === $type;
    }

    /**
     * Calculate the discount for the given discountable.
     */
    public function calculate(Discountable $model): float
    {
        $value = match ($this->value_type) {
            DiscountRuleValueType::TOTAL => $model->getDiscountBase(),
            DiscountRuleValueType::QUANTITY => $model->getDiscountableQuantity(),
            default => 0,
        };

        if ($value <= 0) {
            return 0.0;
        }

        $rule = Collection::make($this->rules)
            ->filter(static function (array $rule) use ($model): bool {
                return is_null($rule['currency'] ?? null)
                    || $rule['currency'] === $model->getDiscountableCurrency()->value;
            })
            ->sortByDesc('value')
            ->first(static function (array $rule) use ($value): bool {
                return $value >= ($rule['value'] ?? 0);
            });

        if (is_null($rule)) {
            return 0.0;
        }

        return (float) match ($rule['type'] ?? null) {
            DiscountType::FIX->value => ($rule['discount'] ?? 0),
            DiscountType::PERCENT->value => ($model->getDiscountBase() * ((float) ($rule['discount'] ?? 0) / 100)),
            default => 0.0,
        };
    }

    /**
     * Apply the discount rule to the given discountable.
     */
    public function apply(Discountable $model): void
    {
        if (! $this->validate($model)) {
            throw new DiscountException('The discount rule is not valid for this discountable model.');
        }

        $value = $this->calculate($model);

        if ($value <= 0) {
            throw new DiscountException('The discount rule is not valid for this discountable model.');
        }

        $model->discounts()->syncWithoutDetaching([
            $this->getKey() => ['value' => $value],
        ]);
    }

    /**
     * Scope a query to only include active discount rules.
     */
    #[Scope]
    protected function active(Builder $query, bool $value = true): Builder
    {
        return $query->where($query->qualifyColumn('active'), $value);
    }
}
