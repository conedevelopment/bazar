<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Address;
use Bazar\Models\Category;
use Bazar\Models\Medium;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\User;
use Bazar\Models\Variation;
use Bazar\Tests\TestCase;
use Illuminate\Http\Request;

class FilterTest extends TestCase
{
    /** @test */
    public function a_product_query_can_be_filtered()
    {
        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'state' => 'all',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
            'category' => 1,
        ]);

        $query = Product::query()->where(function ($query) {
            $query->where('name', 'like', 'test%')
                ->orWhere('inventory->sku', 'like', 'test%');
        })->withTrashed()
          ->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc')
          ->whereHas('categories', function ($query) {
              return $query->where('bazar_categories.id', 1);
          });

        $this->assertSame(
            Product::filter($request)->toSql(), $query->toSql()
        );
    }

    /** @test */
    public function an_order_query_can_be_filtered()
    {
        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'state' => 'all',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
            'status' => 'in_progress',
            'user' => 1,
        ]);

        $query = Order::query()->whereHas('address', function ($query) {
            $query->where('bazar_addresses.first_name', 'like', 'test%')
                ->orWhere('bazar_addresses.last_name', 'like', 'test%');
        })->withTrashed()
          ->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc')
          ->whereIn('status', ['in_progress'])
          ->whereHas('user', function ($query) {
              return $query->where('users.id', 1);
          });

        $this->assertSame(
            Order::filter($request)->toSql(), $query->toSql()
        );
    }

    /** @test */
    public function a_medium_query_can_be_filtered()
    {
        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'state' => 'fake',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
            'type' => 'image',
        ]);

        $query = Medium::query()->where('name', 'like', 'test%')
          ->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc')
          ->where('mime_type', 'like', 'image%');

        $this->assertSame(
            Medium::filter($request)->toSql(), $query->toSql()
        );
    }

    /** @test */
    public function an_address_query_can_be_filtered()
    {
        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
        ]);

        $query = Address::query()->where('alias', 'like', 'test%')
          ->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc');

        $this->assertSame(
            Address::filter($request)->toSql(), $query->toSql()
        );
    }

    /** @test */
    public function a_category_query_can_be_filtered()
    {
        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
        ]);

        $query = Category::query()->where('name', 'like', 'test%')
          ->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc');

        $this->assertSame(
            Category::filter($request)->toSql(), $query->toSql()
        );
    }

    /** @test */
    public function a_user_query_can_be_filtered()
    {
        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'state' => 'fake',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
        ]);

        $query = User::query()->where(function ($query) {
            $query->where('name', 'like', 'test%')
                ->orWhere('email', 'like', 'test%');
        })->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc');

        $this->assertSame(
            User::filter($request)->toSql(), $query->toSql()
        );
    }

    /** @test */
    public function a_variation_query_can_be_filtered()
    {
        $request = Request::create('/', 'GET', [
            'search' => 'test',
            'state' => 'trashed',
            'exclude' => [1, 2],
            'sort' => ['by' => 'created_at', 'order' => 'desc'],
        ]);

        $query = Variation::query()->where('alias', 'like', 'test%')
          ->onlyTrashed()
          ->whereNotIn('id', [1, 2])
          ->orderBy('created_at', 'desc');

        $this->assertSame(
            Variation::filter($request)->toSql(), $query->toSql()
        );
    }
}
