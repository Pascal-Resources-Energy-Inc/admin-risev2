<?php

namespace App\Providers;

use App\DealerStockRequest;
use App\Client;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

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
            $customerAlertCount = 0;

            if ($user && strcasecmp(trim((string) $user->role), 'Admin') === 0) {
                $headerData = Cache::remember('admin-header-alerts-v1', now()->addSeconds(45), function () {
                    $inactiveSince = Carbon::now()->subDays(7)->toDateString();
                    $latestPurchases = DB::table('transaction_details')
                        ->select('client_id', DB::raw('MAX(date) as last_transaction_date'))
                        ->whereNotNull('client_id')
                        ->groupBy('client_id');
                    $inactiveCustomers = Client::query()
                        ->joinSub($latestPurchases, 'latest_purchases', function ($join) {
                            $join->on('latest_purchases.client_id', '=', 'clients.id');
                        })
                        ->where('status', 'Active')
                        ->where('latest_purchases.last_transaction_date', '<', $inactiveSince);

                    return [
                        'pendingStockRequestsCount' => DealerStockRequest::where('status', 'Pending')->count(),
                        'customerAlertCount' => (clone $inactiveCustomers)->count(),
                        'customersLessForAlert' => $inactiveCustomers->select([
                            'clients.id', 'clients.name', 'clients.location_barangay', 'clients.location_city',
                            'clients.spo', 'clients.center', 'latest_purchases.last_transaction_date',
                        ])->orderByDesc('latest_purchases.last_transaction_date')->limit(50)->get(),
                    ];
                });
                $pendingStockRequestsCount = $headerData['pendingStockRequestsCount'];
                $customerAlertCount = $headerData['customerAlertCount'];
                $customersLessForAlert = $headerData['customersLessForAlert'];
            }

            $view->with([
                'pendingStockRequestsCount' => $pendingStockRequestsCount,
                'customersLessForAlert' => $customersLessForAlert,
                'customerAlertCount' => $customerAlertCount,
            ]);
        });
    }
}
