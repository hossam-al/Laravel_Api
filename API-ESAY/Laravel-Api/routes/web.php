<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});



// Dashboard routes
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    // Login routes
    Route::get('login', [DashboardController::class, 'showOwnerLoginForm'])->name('login');
    Route::post('login', [DashboardController::class, 'ownerLogin'])->name('owner.login');
    Route::post('logout', [DashboardController::class, 'ownerLogout'])->name('owner.logout');

    // Dashboard protected routes
    Route::middleware('auth')->group(function () {
        Route::get('owner', [DashboardController::class, 'ownerDashboard'])->name('owner');
    });
});
