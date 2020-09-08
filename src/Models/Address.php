<?php

namespace Bazar\Models;

use Bazar\Concerns\BazarRoutable;
use Bazar\Contracts\Breadcrumbable;
use Bazar\Support\Countries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class Address extends Model implements Breadcrumbable
{
    use BazarRoutable;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'country_name',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'city' => null,
        'state' => null,
        'phone' => null,
        'email' => null,
        'alias' => null,
        'custom' => '[]',
        'country' => null,
        'default' => false,
        'company' => null,
        'address' => null,
        'postcode' => null,
        'last_name' => null,
        'first_name' => null,
        'address_secondary' => null,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'default' => 'bool',
        'custom' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city',
        'state',
        'phone',
        'email',
        'alias',
        'custom',
        'country',
        'default',
        'company',
        'address',
        'postcode',
        'last_name',
        'first_name',
        'address_secondary',
    ];

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
    public function getAliasAttribute(string $value = null): ?string
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
     * Get the breadcrumb label.
     *
     * @param  \Illuminate\Http\Request
     * @return string
     */
    public function getBreadcrumbLabel(Request $request): string
    {
        return $this->alias;
    }
}
