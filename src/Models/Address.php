<?php

namespace Cone\Bazar\Models;

use Cone\Bazar\Database\Factories\AddressFactory;
use Cone\Bazar\Interfaces\Models\Address as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'name',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
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
     * @var array<string, string>
     */
    protected $casts = [
        'custom' => 'json',
        'default' => 'bool',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
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
        return AddressFactory::new();
    }

    /**
     * Get the addressable model for the address.
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the alias attribute.
     */
    protected function alias(): Attribute
    {
        return new Attribute(
            get: function (?string $value): ?string {
                return $this->exists ? ($value ?: "#{$this->getKey()}") : $value;
            }
        );
    }

    /**
     * Get the name attribute.
     */
    protected function name(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): string {
                return trim(sprintf('%s %s', $attributes['first_name'], $attributes['last_name']));
            }
        );
    }
}
