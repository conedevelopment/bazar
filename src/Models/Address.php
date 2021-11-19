<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Contracts\Models\Address as Contract;
use Cone\Bazar\Database\Factories\AddressFactory;
use Cone\Root\Support\Countries;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'country_name',
        'name',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'address_secondary' => null,
        'address' => null,
        'alias' => null,
        'city' => null,
        'company' => null,
        'country' => null,
        'custom' => '[]',
        'default' => false,
        'email' => null,
        'first_name' => null,
        'last_name' => null,
        'phone' => null,
        'postcode' => null,
        'state' => null,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'custom' => 'json',
        'default' => 'bool',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address_secondary',
        'address',
        'alias',
        'city',
        'company',
        'country',
        'custom',
        'default',
        'email',
        'first_name',
        'last_name',
        'phone',
        'postcode',
        'state',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_addresses';

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
        return AddressFactory::new();
    }

    /**
     * Get the addressable model for the address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the alias attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getAliasAttribute(?string $value = null): ?string
    {
        return $this->exists ? ($value ?: "#{$this->id}") : $value;
    }

    /**
     * Get the name attribute.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return trim(sprintf('%s %s', $this->first_name, $this->last_name));
    }

    /**
     * Get the country name attribute.
     *
     * @return string|null
     */
    public function getCountryNameAttribute(): ?string
    {
        return $this->country ? Countries::name($this->country) : null;
    }

    /**
     * Get a custom property.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function custom(string $key, $default = null)
    {
        return $this->custom[$key] ?? $default;
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
        return $query->where($query->qualifyColumn('alias'), 'like', "{$value}%");
    }
}
