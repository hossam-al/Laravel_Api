<?php

use App\Http\Controllers\ProductsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Web\AuthController as WebAuthController;
use App\Http\Controllers\Web\ProductWebController;
use App\Http\Controllers\Web\CategoryWebController;
use App\Http\Controllers\Web\BrandWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::prefix('web/products')->name('web.products.')->group(function () {
        Route::get('/', [ProductWebController::class, 'index'])->name('index');
        Route::get('/create', [ProductWebController::class, 'create'])->name('create');
        Route::post('/', [ProductWebController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductWebController::class, 'edit'])->name('edit');
        Route::post('/{product}', [ProductWebController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductWebController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('web/categories')->name('web.categories.')->group(function () {
        Route::get('/', [CategoryWebController::class, 'index'])->name('index');
        Route::get('/create', [CategoryWebController::class, 'create'])->name('create');
        Route::post('/', [CategoryWebController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryWebController::class, 'edit'])->name('edit');
        Route::post('/{category}', [CategoryWebController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryWebController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('web/brands')->name('web.brands.')->group(function () {
        Route::get('/', [BrandWebController::class, 'index'])->name('index');
        Route::get('/create', [BrandWebController::class, 'create'])->name('create');
        Route::post('/', [BrandWebController::class, 'store'])->name('store');
        Route::get('/{brand}/edit', [BrandWebController::class, 'edit'])->name('edit');
        Route::post('/{brand}', [BrandWebController::class, 'update'])->name('update');
        Route::delete('/{brand}', [BrandWebController::class, 'destroy'])->name('destroy');
    });
});
Route::get('/products', [ProductsController::class, 'index']);

// Web Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [WebAuthController::class, 'profile'])->name('profile');
});
