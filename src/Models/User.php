<?php

namespace Bazar\Models;

use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Filterable;
use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Models\Address;
use Bazar\Contracts\Models\User as Contract;
use Bazar\Proxies\Address as AddressProxy;
use Bazar\Proxies\Cart as CartProxy;
use Bazar\Proxies\Order as OrderProxy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class User extends Authenticatable implements Breadcrumbable, Contract, MustVerifyEmail
{
    use BazarRoutable, Filterable, Notifiable, SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'name' => null,
        'email' => null,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'deleted_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(static function (self $user): void {
            $user->password = $user->password ?: Hash::make(Str::random(10));
        });
    }

    /**
     * Get the filter options for the model.
     *
     * @return array
     */
    public static function filters(): array
    {
        return [
            'state' => [
                'all' => __('All'),
                'available' => __('Available'),
                'trashed' => __('Trashed')
            ],
        ];
    }

    /**
     * Get the cart for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(CartProxy::getProxiedClass());
    }

    /**
     * Get the orders for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(OrderProxy::getProxiedClass());
    }

    /**
     * Get the addresses for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(AddressProxy::getProxiedClass(), 'addressable');
    }

    /**
     * Get the address attribute.
     *
     * @return \Bazar\Contracts\Models\Address|null
     */
    public function getAddressAttribute(): ?Address
    {
        return $this->addresses->firstWhere('default', true) ?: $this->addresses->first();
    }

    /**
     * Get the avatar attribute.
     *
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        return URL::asset('vendor/bazar/img/avatar-placeholder.svg');
    }

    /**
     * Determine if the user is admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->email, Config::get('bazar.admins', []));
    }

    /**
     * Get the breadcrumb label.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function getBreadcrumbLabel(Request $request): string
    {
        return $this->name;
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
        return $query->where(static function (Builder $query) use ($value): Builder {
            return $query->where('name', 'like', "{$value}%")
                        ->orWhere('email', 'like', "{$value}%");
        });
    }
}
