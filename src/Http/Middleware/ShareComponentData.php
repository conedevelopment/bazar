<?php

namespace Bazar\Http\Middleware;

use Bazar\Contracts\Models\User;
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
            'csrf_token' => static function (): string {
                return csrf_token();
            },
            'config' => static function (): array {
                return [
                    'weight_unit' => Config::get('bazar.weight_unit'),
                    'dimension_unit' => Config::get('bazar.dimension_unit'),
                ];
            },
            'admin' => static function () use ($request): ?User {
                return $request->user();
            },
            'breadcrumbs' => static function () use ($request): Breadcrumbs {
                return new Breadcrumbs($request);
            },
            'message' => static function () use ($request): ?string {
                return $request->session()->get('message');
            },
            'errors' => static function () use ($request): array {
                return $request->session()->has('errors')
                    ? array_map(static function (array $errors): string {
                        return $errors[0];
                    }, $request->session()->get('errors')->getBag('default')->messages())
                    : [];
            },
        ]);

        return $next($request);
    }
}
