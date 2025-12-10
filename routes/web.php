<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShoesController;
use App\Http\Controllers\ShoesVariantController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Landing page (before login)
Route::get('/', [PurchaseController::class, 'landing'])->name('landing-page');

// Authentication routes
Route::get('/login', [AuthController::class, 'login'])->name('login');

// Dashboard admin
Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard-admin');

Route::resource('/shoes', ShoesController::class);
Route::resource('/category', CategoryController::class);
Route::resource('/shoes-variant', ShoesVariantController::class);
Route::get('/driver', [DriverController::class, 'index'])->name("driver.index");
Route::post('/driver/store', [DriverController::class, 'store'])->name('driver.store');
Route::delete('/driver/destroy/{id}', [DriverController::class, 'destroy'])->name('driver.destroy');
Route::get('/driver/{id}', [DriverController::class, 'show'])->name('driver.show');
Route::put('/driver/{id}', [DriverController::class, 'update'])->name('driver.update');

// Transaction
Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
