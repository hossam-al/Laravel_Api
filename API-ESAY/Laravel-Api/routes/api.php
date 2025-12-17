<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\AdminProductsController;







Route::prefix("v1")->group(function () {
    Route::middleware('throttle:10,5')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/update', [AuthController::class, 'update']);
        Route::delete('/deleteUser', [AuthController::class, 'deleteUser']);


        Route::prefix("products")->group(function () {

            // get All data
            Route::get('/', [ProductsController::class, 'index']);

            // Add data
            Route::post('/', [ProductsController::class, 'store']);

            // show data by id
            Route::get('/{id}', [ProductsController::class, 'show']);

            //  update data
            Route::post('/{id}', [ProductsController::class, 'update']);

            // delete data
            Route::delete('/{id}', [ProductsController::class, 'destroy']);

            Route::prefix('DeleteAll')->group(function () {
                Route::delete('/delete', [ProductsController::class, 'DeleteAll']);
            });
        });
        Route::prefix("category")->group(function () {

            // get All data
            Route::get('/', [CategoryController::class, 'index']);

            // Add data
            Route::post('/', [CategoryController::class, 'store']);

            // show data by id
            Route::get('/{id}', [CategoryController::class, 'show']);

            //  update data
            Route::post('/{id}', [CategoryController::class, 'update']);

            // delete data
            Route::delete('/{id}', [CategoryController::class, 'destroy']);

            Route::prefix('DeleteAll')->group(function () {
                Route::delete('/delete', [CategoryController::class, 'DeleteAll']);
            });
        });

        // Order routes
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']); // Get all orders
            Route::post('/', [OrderController::class, 'store']); // Create a new order
            Route::get('/{id}', [OrderController::class, 'show']); // Get a specific order
            Route::put('/{id}', [OrderController::class, 'update']); // Update order status
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']); // Cancel order (User can cancel pending orders)
            Route::delete('/{id}', [OrderController::class, 'destroy']); // Delete order (Super Admin only)
        });

        // Order Items routes


        // Role management routes (Super Admin only) - DISABLED
        // Route::prefix('roles')->group(function () {
        //     Route::get('/', [RoleController::class, 'index']); // Get all roles
        //     Route::post('/', [RoleController::class, 'store']); // Create a new role
        //     Route::get('/{id}', [RoleController::class, 'show']); // Get a specific role
        //     Route::put('/{id}', [RoleController::class, 'update']); // Update a role
        //     Route::delete('/{id}', [RoleController::class, 'destroy']); // Delete a role
        //     Route::post('/assign', [RoleController::class, 'assignRole']); // Assign role to a user
        // });

        // Admin Products routes (only for admin users)
        Route::prefix('admin-products')->group(function () {
            Route::get('/', [AdminProductsController::class, 'index']); // Get admin's products
            Route::post('/', [AdminProductsController::class, 'store']); // Create a new admin product
            Route::get('/{id}', [AdminProductsController::class, 'show']); // Get a specific admin product
            Route::post('/{id}', [AdminProductsController::class, 'update']); // Update an admin product
            Route::delete('/{id}', [AdminProductsController::class, 'destroy']); // Delete an admin product

            Route::prefix('DeleteAll')->group(function () {
                Route::delete('/delete', [AdminProductsController::class, 'DeleteAll']); // Delete all admin's products
            });
        });
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
