<?php

namespace Cone\Bazar\Tests;

use Cone\Bazar\Traits\AsCustomer;
use Cone\Root\Models\User as BaseUser;
use Cone\Root\Tests\Team;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends BaseUser
{
    use AsCustomer;
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return new class extends UserFactory
        {
            protected $model = User::class;
        };
    }

    public function teams()
    {
        $builder = (new Team)->newQuery();

        $builder->shouldReceive('getModels')->andReturn($builder->get()->all());

        return new BelongsToMany($builder, $this, 'team_user', 'team_id', 'user_id', 'id', 'id', 'teams');
    }
}
