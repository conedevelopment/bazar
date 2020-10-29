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
            'csrf_token' => static function () {
                return csrf_token();
            },
            'admin' => static function () use ($request) {
                return $request->user();
            },
            'breadcrumbs' => static function () use ($request) {
                return new Breadcrumbs($request);
            },
            'message' => static function () use ($request) {
                return $request->session()->get('message');
            },
            'errors' => static function () use ($request) {
                return $request->session()->has('errors') ? array_map(static function (array $errors) {
                    return $errors[0];
                }, $request->session()->get('errors')->getBag('default')->messages()) : (object) [];
            },
        ]);

        return $next($request);
    }
}
