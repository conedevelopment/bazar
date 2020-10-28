<?php

namespace Bazar\Http\Middleware;

use Bazar\Support\Breadcrumbs;
use Bazar\Support\Facades\Component;

class ShareComponentData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, callable $next)
    {
        Component::share([
            'csrf_token' => function () {
                return csrf_token();
            },
            'admin' => function () use ($request) {
                return $request->user();
            },
            'breadcrumbs' => function () use ($request) {
                return new Breadcrumbs($request);
            },
            'message' => function () use ($request) {
                return $request->session()->get('message');
            },
            'errors' => function () use ($request) {
                return $request->session()->has('errors') ? array_map(function (array $errors) {
                    return $errors[0];
                }, $request->session()->get('errors')->getBag('default')->messages()) : (object) [];
            },
        ]);

        return $next($request);
    }
}
