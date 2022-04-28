<?php

namespace Cone\Bazar\Tests;

use Cone\Bazar\Traits\AsCustomer;
use Cone\Root\Database\Factories\UserFactory;
use Cone\Root\Models\User as BaseUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class User extends BaseUser
{
    use AsCustomer;

    protected static function newFactory(): ?Factory
    {
        return new class extends UserFactory {
            protected $model = User::class;
        };
    }
}
