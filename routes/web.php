<?php

declare(strict_types=1);

use Cone\Bazar\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

// Gateway
Route::any('/gateway/{driver}/capture', [GatewayController::class, 'capture'])->name('gateway.capture');
Route::any('/gateway/{driver}/notification', [GatewayController::class, 'notification'])->name('gateway.notification');
