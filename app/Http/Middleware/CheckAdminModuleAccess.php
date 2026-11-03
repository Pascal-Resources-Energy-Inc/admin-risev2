<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminModuleAccess
{
    private $pathModules = [
        'transactions' => 'transactions',
        'dealers' => 'dealers',
        'new-dealer' => 'dealers',
        'check-dealer-duplicate' => 'dealers',
        'view-dealer' => 'dealers',
        'dealer' => 'dealers',
        'change-avatar-dealer' => 'dealers',
        'valid-id-dealer' => 'dealers',
        'submit-contract-dealer' => 'dealers',
        'dealer-ads' => 'dealers',
        'mds' => 'mega_dealers',
        'mega-dealers' => 'mega_dealers',
        'customers' => 'customers',
        'clients' => 'customers',
        'customer' => 'customers',
        'new-customer' => 'customers',
        'view-client' => 'customers',
        'change-avatar' => 'customers',
        'valid-id' => 'customers',
        'submit-contract' => 'customers',
        'signature' => 'customers',
        'signature-dealer' => 'dealers',
        'users' => 'users',
        'new-user' => 'users',
        'admin-privillege' => 'users',
        'center-chiefs' => 'center_chiefs',
        'centers' => 'centers',
        'stock-requests' => 'stock_requests',
        'serial-numbers' => 'serial_numbers',
        'ad-purchase-orders' => 'purchase_orders',
        'warehouse' => 'purchase_orders',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'Admin') {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $module = null;

        if ($path === '' || $path === 'home') {
            $module = 'dashboard';
        } else {
            $firstSegment = strtok($path, '/');
            if (isset($this->pathModules[$firstSegment])) {
                $module = $this->pathModules[$firstSegment];
            } elseif (strpos($path, 'reports/isl-report') === 0) {
                $module = 'inventory_reports';
            } elseif (strpos($path, 'reports/') === 0) {
                $module = 'reports';
            }
        }

        if ($module && !$user->hasModuleAccess($module)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You do not have access to this module.'], 403);
            }

            abort(403, 'You do not have access to this module.');
        }

        return $next($request);
    }
}
