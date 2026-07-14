<?php

namespace App\Providers;

use App\DealerStockRequest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.header', function ($view) {
            $user = auth()->user();
            $pendingStockRequestsCount = 0;

            if ($user && strcasecmp(trim((string) $user->role), 'Admin') === 0) {
                $pendingStockRequestsCount = DealerStockRequest::where('status', 'Pending')->count();
            }

            $view->with('pendingStockRequestsCount', $pendingStockRequestsCount);
        });
    }
}
