<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\CartController;
use App\Http\Controllers\API\v1\BrandController;
use App\Http\Controllers\API\v1\OrderController;
use App\Http\Controllers\API\v1\ReviewController;
use App\Http\Controllers\API\v1\CartItemController;
use App\Http\Controllers\API\v1\CategoryController;
use App\Http\Controllers\API\v1\ProductsController;
use App\Http\Controllers\API\v1\WishlistController;
use App\Http\Controllers\API\v1\Auth\AuthController;
use App\Http\Controllers\API\v1\OrderItemController;
use App\Http\Controllers\API\v1\ProductImageController;




Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        // 🔸 Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductsController::class, 'index']);
            Route::post('/', [ProductsController::class, 'store']);
            Route::get('/{id}', [ProductsController::class, 'show']);
            Route::post('/{id}', [ProductsController::class, 'update']);
            Route::get('/slug/{slug}', [ProductsController::class, 'showBySlug']);
            Route::delete('/{id}', [ProductsController::class, 'destroy']);
        });

        // 🔸 Category
        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::get('/{id}', [CategoryController::class, 'show']);
            Route::get('/slug/{slug}', [CategoryController::class, 'showBySlug']);
            Route::post('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });

        // 🔸 Brands
        Route::prefix('brands')->group(function () {
            Route::get('/', [BrandController::class, 'index']);
            Route::post('/', [BrandController::class, 'store']);
            Route::get('{id}', [BrandController::class, 'show']);
            Route::post('/{id}', [BrandController::class, 'update']);
            Route::delete('/{id}', [BrandController::class, 'destroy']);
        });

        // 🔸 Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/{order}', [OrderController::class, 'show']);
            Route::post('/{order}', [OrderController::class, 'update']);
            Route::delete('/{order}', [OrderController::class, 'destroy']);

            Route::get('/{order}/items', [OrderItemController::class, 'index']);
            Route::post('/{order}/items', [OrderItemController::class, 'store']);
            Route::get('/{order}/items/{orderItem}', [OrderItemController::class, 'show']);
            Route::post('/{order}/items/{orderItem}', [OrderItemController::class, 'update']);
            Route::delete('/{order}/items/{orderItem}', [OrderItemController::class, 'destroy']);
        });

        // 🔸 Product Images
        Route::prefix('product-images')->group(function () {
            Route::get('/', [ProductImageController::class, 'index']);
            Route::post('/', [ProductImageController::class, 'store']);
            Route::get('{id}', [ProductImageController::class, 'show']);
            Route::post('/{id}', [ProductImageController::class, 'update']);
            Route::delete('/{id}', [ProductImageController::class, 'destroy']);
        });

        // 🔸 Reviews
        Route::prefix('reviews')->group(function () {
            Route::get('/', [ReviewController::class, 'index']);
            Route::post('/', [ReviewController::class, 'store']);
            Route::get('/{review}', [ReviewController::class, 'show']);
            Route::post('/{review}', [ReviewController::class, 'update']);
            Route::delete('/{review}', [ReviewController::class, 'destroy']);
        });

        // 🔸 Cart
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/', [CartController::class, 'store']);
            Route::get('/{cart}', [CartController::class, 'show']);
            Route::post('/{cart}', [CartController::class, 'update']);
            Route::delete('/{cart}', [CartController::class, 'destroy']);

            Route::get('/{cart}/items', [CartItemController::class, 'index']);
            Route::post('/{cart}/items', [CartItemController::class, 'store']);
            Route::get('/{cart}/items/{cartItem}', [CartItemController::class, 'show']);
            Route::post('/{cart}/items/{cartItem}', [CartItemController::class, 'update']);
            Route::delete('/{cart}/items/{cartItem}', [CartItemController::class, 'destroy']);
        });

        // 🔸 Wishlist
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/', [WishlistController::class, 'store']);
            Route::get('/{wishlist}', [WishlistController::class, 'show']);
            Route::post('/{wishlist}', [WishlistController::class, 'update']);
            Route::delete('/{wishlist}', [WishlistController::class, 'destroy']);
        });

        // 🔸 Auth Actions
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::delete('/delete-user', [AuthController::class, 'deleteUser']);
    });
});
