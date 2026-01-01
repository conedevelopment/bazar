<?php

declare(strict_types=1);

namespace Cone\Bazar\Models;

use Cone\Bazar\Interfaces\Models\DiscountRule as Contract;
use Cone\Root\Models\User;
use Cone\Root\Traits\InteractsWithProxy;
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
    protected $attributes = [];

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
    protected $table = 'bazar_discount_rules';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
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
}
