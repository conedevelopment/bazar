<?php

use Cone\Bazar\BazarServiceProvider;
use Cone\Root\RootServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: __DIR__.'/../vendor/laravel/laravel')
    ->withRouting(
        web: __DIR__.'/../vendor/laravel/laravel/routes/web.php',
        commands: __DIR__.'/../vendor/laravel/laravel/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withProviders([
        RootServiceProvider::class,
        BazarServiceProvider::class
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
