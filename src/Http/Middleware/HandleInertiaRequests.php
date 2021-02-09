<?php

namespace Bazar\Http\Middleware;

use Bazar\Contracts\Models\User;
use Bazar\Support\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'bazar::app';

    /**
     * Determines the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
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
        ]);
    }
}
