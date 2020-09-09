<?php

namespace Bazar\Models;

use Bazar\Concerns\BazarRoutable;
use Bazar\Contracts\Breadcrumbable;
use Bazar\Contracts\Models\User as Contract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable implements Breadcrumbable, Contract, MustVerifyEmail
{
    use BazarRoutable, Notifiable, SoftDeletes;

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
        static::creating(function (User $user) {
            $user->password = $user->password ?: Hash::make(Str::random(10));
        });
    }

    /**
     * Get the cart for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get the orders for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the addresses for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get the address attribute.
     *
     * @return \Bazar\Models\Address|null
     */
    public function getAddressAttribute(): ?Address
    {
        return $this->addresses->firstWhere(function (Address $address) {
            return $address->default;
        }) ?: $this->addresses->first();
    }

    /**
     * Get the avatar attribute.
     *
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        return asset('vendor/bazar/img/avatar-placeholder.svg');
    }

    /**
     * Determine if the user is admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->email, config('bazar.admins', []));
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
}
