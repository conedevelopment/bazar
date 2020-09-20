<?php

namespace Bazar\Tests\Unit;

use Bazar\Filters\Category;
use Bazar\Filters\Filters;
use Bazar\Filters\Status;
use Bazar\Filters\Type;
use Bazar\Filters\User;
use Bazar\Models\Category as CategoryModel;
use Bazar\Models\User as UserModel;
use Bazar\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class FilterTest extends TestCase
{
    /** @test */
    public function a_builder_can_be_filtered()
    {
        $filters = Filters::make(FilterableModel::class, [
            Type::make(), Category::make(), Status::make(), User::make(),
        ])->searchIn('name');

        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'state' => 'all',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
            'type' => 'image',
            'category' => 1,
            'status' => 'in_progress',
            'user' => 1,
        ]);

        $query = FilterableModel::where(function ($query) {
            $query->where('name', 'like', '%test%');
        })->withTrashed()
          ->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc')
          ->where('mime_type', 'like', 'image%')
          ->whereHas('categories', function ($query) {
              return $query->where('categories.id', 1);
          })
          ->whereIn('status', ['in_progress'])
          ->whereHas('user', function ($query) {
              return $query->where('users.id', 1);
          });

        $this->assertSame(
            FilterableModel::filter($request, $filters)->toSql(), $query->toSql()
        );
    }
}

class FilterableModel extends Model
{
    use SoftDeletes;

    public function categories()
    {
        return $this->belongsToMany(CategoryModel::class);
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }
}
