<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/midtrans/callback', [CheckoutController::class, 'callback']);
Route::get('/test', function () {
    return response()->json(['message' => 'API ShoeCycle Aktif!']);
});
