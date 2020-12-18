<?php

namespace Bazar\Models;

use Bazar\Proxies\Product as ProductProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Option extends Model
{
    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'values' => '[]',
        'variable' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'values' => 'json',
        'variable' => 'bool',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'values',
        'variable',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_options';

    /**
     * Get the products for the option.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ProductProxy::getProxiedClass(), 'bazar_option_product')
                    ->withPivot(['selection', 'variable']);
    }
}
