<?php

namespace Cone\Bazar\Tests;

use Cone\Bazar\Traits\AsCustomer;
use Cone\Root\Models\User as BaseUser;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends BaseUser
{
    use AsCustomer;
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return new class() extends UserFactory
        {
            protected $model = User::class;
        };
    }
}
