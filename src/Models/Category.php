<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Traits\Sluggable;
use Cone\Bazar\Interfaces\Models\Category as Contract;
use Cone\Bazar\Database\Factories\CategoryFactory;
use Cone\Bazar\Resources\CategoryResource;
use Cone\Root\Interfaces\Resourceable;
use Cone\Root\Resources\Resource;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\InteractsWithProxy;
use Cone\Root\Traits\InteractsWithResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model implements Contract, Resourceable
{
    use HasFactory;
    use HasMedia;
    use InteractsWithProxy;
    use InteractsWithResource;
    use Sluggable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'name',
        'slug',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_categories';

    /**
     * Get the proxied interface.
     *
     * @return string
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return CategoryFactory::new();
    }

    /**
     * Get the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::getProxiedClass(), 'bazar_category_product');
    }

    /**
     * Scope the query only to the given search term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where($query->qualifyColumn('name'), 'like', "{$value}%");
    }

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource
    {
        return new CategoryResource(static::class);
    }
}
