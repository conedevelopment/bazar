<?php

namespace Bazar\Models;

use Bazar\Concerns\BazarRoutable;
use Bazar\Concerns\Filterable;
use Bazar\Concerns\InteractsWithProxy;
use Bazar\Contracts\Models\User as Contract;
use Bazar\Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

class User extends Authenticatable implements Contract, MustVerifyEmail
{
    use BazarRoutable;
    use Filterable;
    use HasFactory;
    use InteractsWithProxy;
    use Notifiable;
    use SoftDeletes;

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
     * Get the proxied contract.
     *
     * @return string
     */
    public static function getProxiedContract(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Bazar\Database\Factories\UserFactory|null
     */
    protected static function newFactory(): ?UserFactory
    {
        return get_called_class() === User::class ? UserFactory::new() : null;
    }

    /**
     * Get the active cart for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::getProxiedClass())->latestOfMany();
    }

    /**
     * Get the carts for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\v
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::getProxiedClass());
    }

    /**
     * Get the orders for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::getProxiedClass());
    }

    /**
     * Get the addresses for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::getProxiedClass(), 'addressable');
    }

    /**
     * Get the address attribute.
     *
     * @return \Bazar\Models\Address|null
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
     * Get the breadcrumb representation of the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function toBreadcrumb(Request $request): string
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
            return $query->where($query->qualifyColumn('name'), 'like', "{$value}%")
                        ->orWhere($query->qualifyColumn('email'), 'like', "{$value}%");
        });
    }
}
