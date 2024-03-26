<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\CategoryFactory;
use Cone\Bazar\Interfaces\Models\Category as Contract;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model implements Contract
{
    use HasFactory;
    use HasMedia;
    use InteractsWithProxy;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the products for the category.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::getProxiedClass(), 'bazar_category_product');
    }
}
