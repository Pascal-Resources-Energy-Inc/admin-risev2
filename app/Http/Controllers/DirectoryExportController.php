<?php

namespace App\Http\Controllers;

use App\CenterChief;
use App\Client;
use App\Dealer;
use App\MegaDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DirectoryExportController extends Controller
{
    public function dealers(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'Admin', 403);

        $type = $request->input('dealer_type') === 'Regular' ? 'Regular' : 'Project';
        $search = $this->searchTerm($request);
        $orders = DB::table('order_details')->select('dealer_id', DB::raw('COALESCE(SUM(qty), 0) as stock_qty'))->groupBy('dealer_id');
        $sales = DB::table('transaction_details')->select('dealer_id', DB::raw('COALESCE(SUM(qty), 0) as sold_qty'))->groupBy('dealer_id');
        $query = Dealer::query()
            ->leftJoinSub($orders, 'dealer_order_totals', function ($join) { $join->on('dealer_order_totals.dealer_id', '=', 'dealers.user_id'); })
            ->leftJoinSub($sales, 'dealer_sales_totals', function ($join) { $join->on('dealer_sales_totals.dealer_id', '=', 'dealers.user_id'); })
            ->select('dealers.*', 'dealer_order_totals.stock_qty', 'dealer_sales_totals.sold_qty');

        if (Schema::hasColumn('dealers', 'dealer_type')) {
            if ($type === 'Regular') {
                $query->where('dealers.dealer_type', 'Regular');
            } else {
                $query->where(function ($builder) {
                    $builder->whereNull('dealers.dealer_type')->orWhere('dealers.dealer_type', '!=', 'Regular');
                });
            }
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                foreach (['dealers.dealer_reference', 'dealers.name', 'dealers.store_name', 'dealers.store_type', 'dealers.number', 'dealers.address', 'dealers.area', 'dealers.status'] as $index => $column) {
                    $index === 0 ? $builder->where($column, 'like', '%' . $search . '%') : $builder->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        return $this->streamCsv($query, 'dealers_' . strtolower($type) . '_' . date('Ymd_His') . '.csv', [
            'Dealer Reference', 'Name', 'Store Name', 'Store Type', 'Contact', 'Stock Quantity', 'Sold Quantity', 'Address', 'Sales Territory', 'MFI', 'Center', 'Status',
        ], function ($dealer) {
            return [
                $dealer->dealer_reference, $dealer->name, $dealer->store_name, $dealer->store_type,
                $dealer->number, $dealer->stock_qty ?: 0, $dealer->sold_qty ?: 0, $dealer->address,
                $dealer->area, $dealer->mfi, $dealer->center, $dealer->status,
            ];
        });
    }

    public function megaDealers(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'Admin', 403);

        $search = $this->searchTerm($request);
        $orders = DB::table('order_details')->select('dealer_id', DB::raw('COALESCE(SUM(qty), 0) as stock_qty'))->groupBy('dealer_id');
        $sales = DB::table('transaction_details')->select('dealer_id', DB::raw('COALESCE(SUM(qty), 0) as sold_qty'))->groupBy('dealer_id');
        $query = MegaDealer::query()
            ->leftJoinSub($orders, 'mega_order_totals', function ($join) { $join->on('mega_order_totals.dealer_id', '=', 'mega_dealers.user_id'); })
            ->leftJoinSub($sales, 'mega_sales_totals', function ($join) { $join->on('mega_sales_totals.dealer_id', '=', 'mega_dealers.user_id'); })
            ->select('mega_dealers.*', 'mega_order_totals.stock_qty', 'mega_sales_totals.sold_qty');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                foreach (['mega_dealers.partner_code', 'mega_dealers.name', 'mega_dealers.store_name', 'mega_dealers.number', 'mega_dealers.area', 'mega_dealers.status'] as $index => $column) {
                    $index === 0 ? $builder->where($column, 'like', '%' . $search . '%') : $builder->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        return $this->streamCsv($query, 'mega_dealers_' . date('Ymd_His') . '.csv', [
            'Partner Code', 'Name', 'Store Name', 'Store Type', 'Contact', 'Email', 'Stock Quantity', 'Sold Quantity', 'MFI', 'Center', 'Sales Territory', 'Address', 'Status',
        ], function ($dealer) {
            return [
                $dealer->partner_code, $dealer->name, $dealer->store_name, $dealer->store_type,
                $dealer->number, $dealer->email_address, $dealer->stock_qty ?: 0, $dealer->sold_qty ?: 0,
                $dealer->mfi, $dealer->center, $dealer->area, $dealer->address, $dealer->status,
            ];
        });
    }

    public function customers(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'Admin', 403);

        $search = $this->searchTerm($request);
        $status = in_array($request->input('status'), ['Active', 'Inactive'], true) ? $request->input('status') : '';
        $pointTotals = DB::table('transaction_details')
            ->select('client_id', DB::raw('COALESCE(SUM(points_client), 0) as total_points'))
            ->whereNotNull('client_id')->groupBy('client_id');
        $query = Client::query()->with('serial')
            ->leftJoinSub($pointTotals, 'customer_point_totals', function ($join) { $join->on('customer_point_totals.client_id', '=', 'clients.id'); })
            ->select('clients.*', 'customer_point_totals.total_points');

        if ($status !== '') {
            $query->where('clients.status', $status);
        }
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                foreach (['clients.client_reference', 'clients.name', 'clients.number', 'clients.email_address', 'clients.center', 'clients.spo', 'clients.mfi', 'clients.status'] as $index => $column) {
                    $index === 0 ? $builder->where($column, 'like', '%' . $search . '%') : $builder->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        return $this->streamCsv($query, 'customers_' . date('Ymd_His') . '.csv', [
            'Customer Reference', 'Name', 'Contact', 'Email', 'Serial Number', 'Address', 'Total Points', 'MFI', 'Center', 'SPO', 'Sales Territory', 'Status',
        ], function ($customer) {
            $address = implode(', ', array_filter([
                $customer->street_address, $customer->location_barangay,
                $customer->location_city, $customer->location_province, $customer->postal_code,
            ]));

            return [
                $customer->client_reference, $customer->name, $customer->number, $customer->email_address,
                optional($customer->serial)->serial_number, $address, $customer->total_points ?: 0,
                $customer->mfi, $customer->center, $customer->spo, $customer->area, $customer->status,
            ];
        });
    }

    public function centerChiefs(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->role === 'Admin', 403);

        $search = $this->searchTerm($request);
        $query = CenterChief::with('centers')->orderBy('name');
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('mfi', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('number', 'like', '%' . $search . '%')
                    ->orWhereHas('centers', function ($centers) use ($search) {
                        $centers->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        return $this->streamCsv($query, 'center_chiefs_' . date('Ymd_His') . '.csv', [
            'Name', 'MFI', 'Centers', 'Email', 'Contact', 'Street Address', 'Barangay', 'City', 'Province', 'Region', 'Sales Territory', 'Status',
        ], function ($chief) {
            return [
                $chief->name, $chief->mfi, $chief->centers->pluck('name')->implode('; '), $chief->email,
                $chief->number, $chief->street_address, $chief->location_barangay, $chief->location_city,
                $chief->location_province, $chief->location_region, $chief->area, $chief->status,
            ];
        });
    }

    private function searchTerm(Request $request)
    {
        $search = $request->input('search', '');
        if (is_array($search)) {
            $search = isset($search['value']) ? $search['value'] : '';
        }

        return trim((string) $search);
    }

    private function streamCsv($query, $filename, array $headers, callable $map)
    {
        return response()->stream(function () use ($query, $headers, $map) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);

            $query->chunk(500, function ($records) use ($output, $map) {
                foreach ($records as $record) {
                    fputcsv($output, array_map([$this, 'safeCsvCell'], $map($record)));
                }
            });

            fclose($output);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private function safeCsvCell($value)
    {
        if (is_string($value) && preg_match('/^[=+@\-\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}
