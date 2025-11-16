<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShoesController;
use App\Http\Controllers\ShoesSizeController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', [PurchaseController::class, 'landing'])->name('landing-page');

// Authentication routes
Route::get('/login', [AuthController::class, 'login'])->name('login');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard-admin');

// CRUD routes for resources
Route::resource('/shoes', ShoesController::class);


Route::resource('/category', CategoryController::class);
Route::get('category/{id}/shoes', [CategoryController::class, 'getShoes'])->name('category.shoes');

Route::resource('/shoes-size', ShoesSizeController::class);

// Transaction
Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
