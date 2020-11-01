<?php

namespace Bazar\Http\Middleware;

use Bazar\Support\Breadcrumbs;
use Bazar\Support\Facades\Component;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ShareComponentData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Component::share([
            'csrf_token' => static function () {
                return csrf_token();
            },
            'config' => static function () {
                return [
                    'weight_unit' => Config::get('bazar.weight_unit'),
                    'dimension_unit' => Config::get('bazar.dimension_unit'),
                ];
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
