<?php

namespace Bazar\Tests\Unit;

use Bazar\Filters\Filters;
use Bazar\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class FilterTest extends TestCase
{
    /** @test */
    public function a_builder_can_be_filtered()
    {
        $filters = Filters::make(FilterableModel::class)->searchIn('name');

        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'state' => 'all',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
        ]);

        $query = FilterableModel::where(function ($query) {
            $query->where('name', 'like', '%test%');
        })->withTrashed()
          ->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc');

        $this->assertSame(
            FilterableModel::filter($request, $filters)->toSql(), $query->toSql()
        );
    }
}

class FilterableModel extends Model
{
    use SoftDeletes;
}
