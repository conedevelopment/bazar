<?php

namespace Cone\Bazar\Models;

use Closure;
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
     * @var list<string>
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
        'tax_id' => null,
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
     * @var list<string>
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
        'tax_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bazar_addresses';

    /**
     * The address validator callback.
     */
    protected static ?Closure $validator = null;

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
     * Set the address validator callback.
     */
    public static function validateUsing(Closure $callback): void
    {
        static::$validator = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
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
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function alias(): Attribute
    {
        return new Attribute(
            get: function (?string $value): ?string {
                return $this->exists ? ($value ?: $this->name) : $value;
            }
        );
    }

    /**
     * Get the name attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return new Attribute(
            get: function (mixed $value, array $attributes): string {
                return trim(sprintf('%s %s', $attributes['first_name'], $attributes['last_name']));
            }
        );
    }

    /**
     * Validate the address.
     */
    public function validate(): bool
    {
        $callback = static::$validator ?: static function (Address $address): bool {
            $data = $address->toArray();

            return (isset($data['company'], $data['tax_id']) || isset($data['first_name'], $data['last_name']))
                && isset($data['address'], $data['city'], $data['country'], $data['postcode'], $data['email']);
        };

        return call_user_func_array($callback, [$this]);
    }
}
