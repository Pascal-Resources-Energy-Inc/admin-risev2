<?php

namespace App\Providers;

use App\DealerStockRequest;
use App\Client;
use Carbon\Carbon;
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
            $customersLessForAlert = collect();

            if ($user && strcasecmp(trim((string) $user->role), 'Admin') === 0) {
                $pendingStockRequestsCount = DealerStockRequest::where('status', 'Pending')->count();
                $inactiveSince = Carbon::now()->subDays(7)->toDateString();
                $customersLessForAlert = Client::with('latestTransaction')
                    ->where('status', 'Active')
                    ->whereDoesntHave('latestTransaction', function ($query) use ($inactiveSince) {
                        $query->where('date', '>=', $inactiveSince);
                    })
                    ->whereHas('latestTransaction')
                    ->orderBy(
                        \DB::raw('(SELECT date FROM transaction_details WHERE transaction_details.client_id = clients.id ORDER BY date DESC LIMIT 1)'),
                        'desc'
                    )
                    ->get();
            }

            $view->with([
                'pendingStockRequestsCount' => $pendingStockRequestsCount,
                'customersLessForAlert' => $customersLessForAlert,
            ]);
        });
    }
}
