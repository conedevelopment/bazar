<?php

use Bazar\Http\Controllers\AddressesController;
use Bazar\Http\Controllers\BatchAddressesController;
use Bazar\Http\Controllers\BatchCategoriesController;
use Bazar\Http\Controllers\BatchMediaController;
use Bazar\Http\Controllers\BatchOrdersController;
use Bazar\Http\Controllers\BatchProductsController;
use Bazar\Http\Controllers\BatchUsersController;
use Bazar\Http\Controllers\BatchVariationsController;
use Bazar\Http\Controllers\CategoriesController;
use Bazar\Http\Controllers\DownloadController;
use Bazar\Http\Controllers\MediaController;
use Bazar\Http\Controllers\OrdersController;
use Bazar\Http\Controllers\PagesController;
use Bazar\Http\Controllers\ProductsController;
use Bazar\Http\Controllers\TransactionsController;
use Bazar\Http\Controllers\UsersController;
use Bazar\Http\Controllers\VariationsController;
use Bazar\Http\Controllers\WidgetsController;
use Illuminate\Support\Facades\Route;

// Pages
Route::get('/', [PagesController::class, 'dashboard'])->name('dashboard');
Route::get('support', [PagesController::class, 'support'])->name('support');

// Users
Route::patch('users/batch-update', [BatchUsersController::class, 'update'])->name('users.batch-update');
Route::delete('users/batch-destroy', [BatchUsersController::class, 'destroy'])->name('users.batch-destroy');
Route::patch('users/batch-restore', [BatchUsersController::class, 'restore'])->name('users.batch-restore');
Route::patch('users/{user}/restore', [UsersController::class, 'restore'])->name('users.restore');
Route::resource('users', UsersController::class)->except('edit');

// Addresses
Route::as('users.addresses.')->prefix('users/{user}/addresses')->group(function () {
    Route::patch('batch-update', [BatchAddressesController::class, 'update'])->name('batch-update');
    Route::delete('batch-destroy', [BatchAddressesController::class, 'destroy'])->name('batch-destroy');
});
Route::resource('users.addresses', AddressesController::class)->except('edit')->scoped();

// Categories
Route::patch('categories/batch-update', [BatchCategoriesController::class, 'update'])->name('categories.batch-update');
Route::delete('categories/batch-destroy', [BatchCategoriesController::class, 'destroy'])->name('categories.batch-destroy');
Route::patch('categories/batch-restore', [BatchCategoriesController::class, 'restore'])->name('categories.batch-restore');
Route::patch('categories/{category}/restore', [CategoriesController::class, 'restore'])->name('categories.restore');
Route::resource('categories', CategoriesController::class)->except('edit');

// Products
Route::patch('products/batch-update', [BatchProductsController::class, 'update'])->name('products.batch-update');
Route::delete('products/batch-destroy', [BatchProductsController::class, 'destroy'])->name('products.batch-destroy');
Route::patch('products/batch-restore', [BatchProductsController::class, 'restore'])->name('products.batch-restore');
Route::patch('products/{product}/restore', [ProductsController::class, 'restore'])->name('products.restore');
Route::resource('products', ProductsController::class)->except('edit');

// Variations
Route::as('products.variations.')->prefix('products/{product}/variations')->group(function () {
    Route::patch('batch-update', [BatchVariationsController::class, 'update'])->name('batch-update');
    Route::delete('batch-destroy', [BatchVariationsController::class, 'destroy'])->name('batch-destroy');
    Route::patch('batch-restore', [BatchVariationsController::class, 'restore'])->name('batch-restore');
    Route::patch('{variation:id}/restore', [VariationsController::class, 'restore'])->name('restore');
});
Route::resource('products.variations', VariationsController::class)->except('edit')->scoped();

// Orders
Route::patch('orders/batch-update', [BatchOrdersController::class, 'update'])->name('orders.batch-update');
Route::delete('orders/batch-destroy', [BatchOrdersController::class, 'destroy'])->name('orders.batch-destroy');
Route::patch('orders/batch-restore', [BatchOrdersController::class, 'restore'])->name('orders.batch-restore');
Route::patch('orders/{order}/restore', [OrdersController::class, 'restore'])->name('orders.restore');
Route::resource('orders', OrdersController::class)->except('edit');

// Transactions
Route::apiResource('orders.transactions', TransactionsController::class)->except(['index', 'show'])->scoped();

// Media
Route::delete('media/batch-destroy', [BatchMediaController::class, 'destroy'])->name('media.batch-destroy');
Route::apiResource('media', MediaController::class);

// Widgets
Route::get('widgets/sales', [WidgetsController::class, 'sales'])->name('widgets.sales');
Route::get('widgets/metrics', [WidgetsController::class, 'metrics'])->name('widgets.metrics');
Route::get('widgets/activities', [WidgetsController::class, 'activities'])->name('widgets.activities');

// Download
Route::get('download', DownloadController::class)->withoutMiddleware('auth')->middleware('signed')->name('download');
