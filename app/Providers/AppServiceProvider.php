<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.frontend.index', function ($view) {
            if (auth()->check()) {
                $cartItems = Cart::with(['variant.shoe', 'variant.images'])
                    ->where('user_id', auth()->id())
                    ->get();
                $cartTotal = $cartItems->sum(function ($item) {
                    return $item->variant->shoe->price * $item->qty;
                });
                $view->with(['cartItems' => $cartItems, 'cartTotal' => $cartTotal]);
            }
        });
    }
}
