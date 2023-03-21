<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\CategoryFactory;
use Cone\Bazar\Interfaces\Models\Category as Contract;
use Cone\Bazar\Resources\CategoryResource;
use Cone\Root\Interfaces\Resourceable;
use Cone\Root\Support\Slug;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\InteractsWithProxy;
use Cone\Root\Traits\Sluggable;
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
    use Sluggable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'description',
        'name',
        'slug',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_categories';

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
        return CategoryFactory::new();
    }

    /**
     * Get the products for the category.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::getProxiedClass(), 'bazar_category_product');
    }

    /**
     * Get the slug representation of the model.
     */
    public function toSlug(): Slug
    {
        return (new Slug($this))->from('name')->unique();
    }

    /**
     * Get the resource representation of the model.
     */
    public static function toResource(): CategoryResource
    {
        return new CategoryResource(static::class);
    }
}
