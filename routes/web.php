<?php

use App\Http\Controllers\Admin\Dashboard\DashboardAuthController;
use App\Http\Controllers\Admin\Dashboard\DashboardBannerController;
use App\Http\Controllers\Admin\Dashboard\DashboardCategoryController;
use App\Http\Controllers\Admin\Dashboard\DashboardHomeController;
use App\Http\Controllers\Admin\Dashboard\DashboardOrderController;
use App\Http\Controllers\Admin\Dashboard\DashboardProductController;
use App\Http\Controllers\Admin\Dashboard\DashboardUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Dashboard (Blade) — /admin
|--------------------------------------------------------------------------
| Session-based auth (the web guard, not Sanctum).
| The Flutter app still uses /admin/* JSON routes from routes/admin.php for
| its mobile admin features; those are separate.
*/

Route::prefix('admin')->middleware('dashboard.locale')->group(function () {

    // Locale switcher — public so the login page can be translated
    Route::get('language/{locale}', [DashboardAuthController::class, 'switchLocale'])
         ->name('dashboard.locale');

    // Login (public)
    Route::get('login',  [DashboardAuthController::class, 'showLogin'])->name('dashboard.login');
    Route::post('login', [DashboardAuthController::class, 'login'])->name('dashboard.login.post');

    // Protected
    Route::middleware(['auth'])->group(function () {

        Route::post('logout', [DashboardAuthController::class, 'logout'])->name('dashboard.logout');

        Route::get('/', [DashboardHomeController::class, 'index'])->name('dashboard.home');

        // Products
        Route::get('products',                 [DashboardProductController::class, 'index'])->name('dashboard.products.index');
        Route::get('products/create',          [DashboardProductController::class, 'create'])->name('dashboard.products.create');
        Route::post('products',                [DashboardProductController::class, 'store'])->name('dashboard.products.store');
        Route::get('products/{id}/edit',       [DashboardProductController::class, 'edit'])->name('dashboard.products.edit');
        Route::put('products/{id}',            [DashboardProductController::class, 'update'])->name('dashboard.products.update');
        Route::delete('products/{id}',         [DashboardProductController::class, 'destroy'])->name('dashboard.products.destroy');

        // Categories
        Route::get('categories',               [DashboardCategoryController::class, 'index'])->name('dashboard.categories.index');
        Route::get('categories/create',        [DashboardCategoryController::class, 'create'])->name('dashboard.categories.create');
        Route::post('categories',              [DashboardCategoryController::class, 'store'])->name('dashboard.categories.store');
        Route::get('categories/{id}/edit',     [DashboardCategoryController::class, 'edit'])->name('dashboard.categories.edit');
        Route::put('categories/{id}',          [DashboardCategoryController::class, 'update'])->name('dashboard.categories.update');
        Route::delete('categories/{id}',       [DashboardCategoryController::class, 'destroy'])->name('dashboard.categories.destroy');

        // Banners
        Route::get('banners',                  [DashboardBannerController::class, 'index'])->name('dashboard.banners.index');
        Route::get('banners/create',           [DashboardBannerController::class, 'create'])->name('dashboard.banners.create');
        Route::post('banners',                 [DashboardBannerController::class, 'store'])->name('dashboard.banners.store');
        Route::get('banners/{id}/edit',        [DashboardBannerController::class, 'edit'])->name('dashboard.banners.edit');
        Route::put('banners/{id}',             [DashboardBannerController::class, 'update'])->name('dashboard.banners.update');
        Route::delete('banners/{id}',          [DashboardBannerController::class, 'destroy'])->name('dashboard.banners.destroy');

        // Orders
        Route::get('orders',                   [DashboardOrderController::class, 'index'])->name('dashboard.orders.index');
        Route::get('orders/{id}',              [DashboardOrderController::class, 'show'])->name('dashboard.orders.show');
        Route::put('orders/{id}/status',       [DashboardOrderController::class, 'updateStatus'])->name('dashboard.orders.status');

        // Users
        Route::get('users',                    [DashboardUserController::class, 'index'])->name('dashboard.users.index');
        Route::get('users/{id}/edit',          [DashboardUserController::class, 'edit'])->name('dashboard.users.edit');
        Route::put('users/{id}',               [DashboardUserController::class, 'update'])->name('dashboard.users.update');
        Route::post('users/{id}/toggle',       [DashboardUserController::class, 'toggleStatus'])->name('dashboard.users.toggle');
        Route::delete('users/{id}',            [DashboardUserController::class, 'destroy'])->name('dashboard.users.destroy');
    });
});