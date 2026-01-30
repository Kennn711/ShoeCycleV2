<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShoesController;
use App\Http\Controllers\ShoesVariantController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Without login routes
Route::get('/shoecycle', [HomeController::class, 'index'])->name('landing-page');
Route::get('/shoecycle/shoes-collection', [HomeController::class, 'shoesCollection'])->name('shoes-collection.index');
Route::get('/shoecycle/all-category', [HomeController::class, 'allCategory'])->name('all-category.index');
Route::get('/shoecycle/{slug}', [HomeController::class, 'detailShoes'])->name('detail-shoes');
Route::get('/shoecycle/api/search-global', [HomeController::class, 'searchGlobal'])->name('api.search-global');

// Authentication routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'customer'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/store', [CartController::class, 'addToCart'])->name('cart.store');
    Route::put('/cart/{id}', [CartController::class, 'updateQty'])->name('cart.update-qty');
    Route::delete('/cart/destroy/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::put('/checkout/cancel/{id}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    Route::get('my-orders', [TransactionController::class, 'indexCustomer'])->name('my-order.index');

    Route::post('/address/store', [AddressController::class, 'store'])->name('address.store');
    Route::put('/address/update/{id}', [AddressController::class, 'update'])->name('address.update');
    Route::put('/address/set-primary/{id}', [AddressController::class, 'setPrimary'])->name('address.set-primary');

    Route::post('/reviews/store', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/verify-password', [SettingsController::class, 'verifyCurrentPassword'])->name('settings.verify-password');
    Route::delete('/settings/account', [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/check-email', [SettingsController::class, 'checkEmail'])->name('settings.check-email');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard-admin', [DashboardController::class, 'admin'])->name('dashboard-admin');

    Route::resource('/shoes', ShoesController::class);
    Route::resource('/category', CategoryController::class);
    Route::resource('/shoes-variant', ShoesVariantController::class);
    Route::get('/driver', [DriverController::class, 'index'])->name("driver.index");
    Route::post('/driver/store', [DriverController::class, 'store'])->name('driver.store');
    Route::delete('/driver/destroy/{id}', [DriverController::class, 'destroy'])->name('driver.destroy');
    Route::get('/driver/{id}', [DriverController::class, 'show'])->name('driver.show');
    Route::put('/driver/{id}', [DriverController::class, 'update'])->name('driver.update');

    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('/transaction/show/{id}', [TransactionController::class, 'show'])->name('transaction.show');
    Route::get('/transaction/get-courier', [TransactionController::class, 'getCouriers'])->name('transaction.get-courier');
    Route::post('/transaction/update-status/{id}', [TransactionController::class, 'updateStatus'])->name('transaction.update');

    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/export-pdf', [ReportController::class, 'exportPdf'])->name('report.export-pdf');
});

Route::middleware(['auth', 'driver'])->group(function () {
    Route::get('/dashboard-driver', [DashboardController::class, 'driver'])->name('dashboard-driver');

    Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');
    Route::post('/delivery/update-status/{id}', [DeliveryController::class, 'updateStatus'])->name('delivery.update-status');
});
