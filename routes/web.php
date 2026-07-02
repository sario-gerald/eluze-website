<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landingpage');
});

Route::get('/shopping-cart', function () {
    return view('shoppingcart');
})->name('shopping-cart');

Route::prefix('eluze-admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
    });

Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('eluze-admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });
