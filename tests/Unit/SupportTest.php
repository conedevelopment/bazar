<?php

namespace Bazar\Tests\Unit;

use Bazar\Bazar;
use Bazar\Support\Breadcrumbs;
use Bazar\Support\Countries;
use Bazar\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class SupportTest extends TestCase
{
    /** @test */
    public function str_has_currency_macro()
    {
        Bazar::currency('eur');
        $this->assertEquals('1,300.00 EUR', Str::currency(1300));

        Bazar::currency('usd');
        $this->assertEquals('150,300,400.00 USD', Str::currency(150300400));

        $this->assertEquals('150,300,400.00 HUF', Str::currency(150300400, 'huf'));
    }

    /** @test */
    public function support_has_country_lookup()
    {
        $this->assertCount(249, Countries::all());

        $this->assertSame('Hungary', Countries::name('HU'));
    }

    /** @test */
    public function breadcrumb_can_process_request()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => "/bazar/users/{$this->user->id}"]);

        $request->setRouteResolver(function () use ($request) {
            $router = (new Route('GET', '/bazar/users/{user}', []))->bind($request);

            $router->setParameter('user', $this->user);

            return $router;
        });

        $breadcrumbs = new Breadcrumbs($request);

        $this->assertSame([
            '/bazar' => 'Dashboard',
            '/bazar/users' => 'Users',
            "/bazar/users/{$this->user->id}" => $this->user->name,
        ], $breadcrumbs->toArray());
    }
}
