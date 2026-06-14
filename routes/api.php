<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| J-BUY API Routes — /api/v1/
|--------------------------------------------------------------------------
| Email verification removed.
| Registration immediately authenticates the user.
| Password reset uses a 6-digit OTP sent by email.
*/

Route::prefix('v1')->group(function () {
    // ── DEBUG ONLY — remove before going live ─────────────────────────────────
    // Hit GET /api/v1/debug-auth with your token from the app.
    // It shows exactly what the server receives so you can confirm
    // which header/path is delivering the token.
    Route::get('debug-auth', function (\Illuminate\Http\Request $request) {
        $serverAuth  = $_SERVER['HTTP_AUTHORIZATION']          ?? null;
        $redirectAuth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;

        return response()->json([
            'header_X_Auth_Token'    => $request->header('X-Auth-Token'),
            'header_Authorization'   => $request->header('Authorization'),
            'server_HTTP_AUTHORIZATION'          => $serverAuth,
            'server_REDIRECT_HTTP_AUTHORIZATION' => $redirectAuth,
            'query_api_token'                    => $request->query('api_token'),
            'all_headers'            => $request->headers->all(),
            'server_keys_with_auth'  => array_filter(
                array_keys($_SERVER),
                fn($k) => str_contains(strtolower($k), 'auth')
            ),
        ]);
    });



    // ── Public ────────────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('register',        [AuthController::class, 'register']);
        Route::post('login',           [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password',  [AuthController::class, 'resetPassword']);
    });

    // Products — auth optional (is_favorite computed when token present)
    Route::get('products',      [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);

    // Catalogue
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('banners',    [BannerController::class,   'index']);

    // ── Authenticated ─────────────────────────────────────────────────────────
    Route::middleware('auth.token')->group(function () {

        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me',      [AuthController::class, 'me']);
        // verify-email and resend-verification-email removed

        // Favorites
        Route::get('favorites',                [FavoriteController::class, 'index']);
        Route::post('favorites/{productId}',   [FavoriteController::class, 'toggle']);
        Route::delete('favorites/{productId}', [FavoriteController::class, 'destroy']);

        // Cart
        Route::get('cart',          [CartController::class, 'index']);
        Route::post('cart',         [CartController::class, 'store']);
        Route::put('cart/{id}',     [CartController::class, 'update']);
        Route::delete('cart/{id}',  [CartController::class, 'destroy']);
        Route::delete('cart',       [CartController::class, 'clear']);

        // Orders
        Route::get('orders',      [OrderController::class, 'index']);
        Route::get('orders/{id}', [OrderController::class, 'show']);
        Route::post('orders',     [OrderController::class, 'store']);

        // Profile
        Route::get('profile',          [ProfileController::class, 'show']);
        Route::put('profile',          [ProfileController::class, 'update']);
        Route::post('profile/avatar',  [ProfileController::class, 'uploadAvatar']);
    });
});