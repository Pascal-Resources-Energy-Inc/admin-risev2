@extends('layouts.header')
<link rel="icon" type="image/png" href="{{asset('images/logo_nya.png')}}">
@section('css')
<style>
    .dealer-page {
        padding-top: 18px;
        padding-bottom: 28px;
    }

    .dealer-head,
    .dealer-stat,
    .dealer-table-card {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(31, 41, 55, .06);
    }

    .dealer-head {
        padding: 20px 22px;
        margin-bottom: 16px;
        border-left: 5px solid #2563eb;
    }

    .dealer-eyebrow {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .dealer-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
    }

    .dealer-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 38px;
        border-radius: 8px;
        font-weight: 700;
    }

    .dealer-stat {
        min-height: 102px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dealer-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 24px;
    }

    .dealer-stat-icon.is-active {
        color: #166534;
        background: #dcfce7;
    }

    .dealer-stat-icon.is-inactive {
        color: #991b1b;
        background: #fee2e2;
    }

    .dealer-stat-value {
        color: #111827;
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
    }

    .dealer-stat-label {
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .dealer-table-card .card-header {
        padding: 16px 18px;
        border-bottom: 1px solid #edf0f5;
    }

    .dealer-table-card .card-body {
        padding: 0;
    }

    .dealer-table {
        margin-bottom: 0 !important;
    }

    .dealer-table thead th {
        vertical-align: middle !important;
        background: #f3f6fa;
        border-color: #e5e7eb;
        color: #4b5563;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .dealer-table td {
        border-color: #edf0f5;
        vertical-align: middle;
        color: #374151;
        font-size: 13px;
    }

    .dealer-table tbody tr:hover {
        background: #f8fafc;
    }

    .dealer-link {
        color: #111827;
        font-weight: 800;
        text-decoration: none;
    }

    .dealer-link:hover {
        color: #2563eb;
        text-decoration: none;
    }

    .dealer-ref {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        border-radius: 999px;
        padding: 4px 10px;
        color: #1d4ed8;
        background: #dbeafe;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .dealer-metric {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 58px;
        min-height: 28px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .dealer-metric.is-stock {
        color: #166534;
        background: #dcfce7;
    }

    .dealer-metric.is-sold {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .dealer-metric.is-remaining {
        color: #0f766e;
        background: #ccfbf1;
    }

    .dealer-metric.is-negative {
        color: #991b1b;
        background: #fee2e2;
    }

    .dealer-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 76px;
        min-height: 28px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 800;
    }

    .dealer-status.is-active {
        color: #166534;
        background: #dcfce7;
    }

    .dealer-status.is-inactive {
        color: #991b1b;
        background: #fee2e2;
    }

    .dealer-muted {
        color: #64748b;
        max-width: 260px;
        white-space: normal;
    }

    .dataTables_wrapper {
        padding: 14px;
    }

    .dataTables_wrapper .row:first-child {
        align-items: center;
        margin-bottom: 8px;
    }

    .dataTables_length select {
        width: 64px !important;
    }

    .dataTables_filter input,
    .dataTables_length select {
        border: 1px solid #dbe2ea;
        border-radius: 7px;
        min-height: 36px;
        padding: 4px 8px;
    }

    table.dataTable {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    @media (max-width: 575px) {
        .dealer-head {
            padding: 16px;
        }

        .dealer-actions,
        .dealer-actions .btn {
            width: 100%;
        }

        .dealer-stat-value {
            font-size: 26px;
        }
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection
@section('content')
@php
    $dealerPageTitle = $dealerPageTitle ?? 'Dealers';
    $dealerSingularTitle = $dealerSingularTitle ?? 'Dealer';
@endphp
<section class="dealer-page">
    <div class="dealer-head">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="dealer-eyebrow">Partner Network</div>
                <h4 class="mb-1">{{ $dealerPageTitle }}</h4>
                <div class="text-muted">Monitor dealer status, territory, stock, and sales performance.</div>
            </div>
            <div class="dealer-actions">
                @if(auth()->user()->role == 'Admin' && Route::currentRouteName() !== 'mds')
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#new_dealer">
                        <i class="ti ti-plus"></i>
                        Add {{ $dealerSingularTitle }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="dealer-stat">
                <div class="dealer-stat-icon is-active">
                    <i class="ti ti-user-check"></i>
                </div>
                <div>
                    <div class="dealer-stat-value">{{ number_format($activeDealers) }}</div>
                    <div class="dealer-stat-label">Active {{ $dealerPageTitle }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="dealer-stat">
                <div class="dealer-stat-icon is-inactive">
                    <i class="ti ti-user-x"></i>
                </div>
                <div>
                    <div class="dealer-stat-value">{{ number_format($inactiveDealers) }}</div>
                    <div class="dealer-stat-label">Inactive {{ $dealerPageTitle }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xl-12 d-flex align-items-stretch">
            <div class="card dealer-table-card w-100">
                <div class="card-header bg-white d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                    <div>
                        <h5 class="mb-0">{{ $dealerPageTitle }}</h5>
                        <div class="small text-muted">{{ $dealers->count() }} record{{ $dealers->count() == 1 ? '' : 's' }} listed</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if(auth()->user()->role == 'Admin')
                            <table class="table dealer-table transaction-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{ $dealerSingularTitle }} Reference</th>
                                        <th>{{ $dealerSingularTitle }} Name</th>
                                        <th>Store Name</th>
                                        <th>Store Type</th>
                                        <th>Number</th>
                                        <th>Qty Stock</th>
                                        <th>Qty Sold</th>
                                        <th>Address</th>
                                        <th>Sales Territory</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="adBody">
                                    @foreach($dealers as $dealer)
                                    <tr>
                                        <td scope="col"><span class="dealer-ref">{{  strtoupper($dealer->dealer_reference) }}</span></td>
                                        <td scope="col"><a href='view-dealer/{{$dealer->id}}' class="dealer-link">{{ strtoupper($dealer->name)}} </a></td>
                                        <td scope="col">{{ strtoupper($dealer->store_name ?? '-')}}</td>
                                        <td scope="col">{{ strtoupper($dealer->store_type ?? '-')}}</td>
                                        <td scope="col">{{ strtoupper($dealer->number)}}</td>
                                        <td scope="col"><span class="dealer-metric is-stock">{{ number_format(($dealer->orders)->sum('qty')) }}</span></td>
                                        <td scope="col"><span class="dealer-metric is-sold">{{ number_format(($dealer->sales)->sum('qty')) }}</span></td>
                                        <td scope="col"><div class="dealer-muted">{{ strtoupper($dealer->address ?? '-') }}</div></td>
                                        <td scope="col"><div class="dealer-muted">{{ strtoupper($dealer->area ?? '-') }}</div></td>
                                        <td>
                                            @if($dealer->status == 'Active')
                                                <span class="dealer-status is-active">Active</span>
                                            @else 
                                                <span class="dealer-status is-inactive">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table> 
                        @elseif(auth()->user()->role == 'Area Distributor')
                            <table class="table dealer-table transaction-table text-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th rowspan="2">{{ $dealerSingularTitle }} Ref</th>
                                        <th rowspan="2">{{ $dealerSingularTitle }} Name</th>
                                        <th rowspan="2">Store</th>
                                        <th rowspan="2">Type</th>
                                        <th rowspan="2">Number</th>

                                        @foreach($items as $item)
                                            <th colspan="3">{{ $item->product_name }}</th>
                                        @endforeach

                                        <th rowspan="2">Address</th>
                                        <th rowspan="2">Sales Territory</th>
                                        <th rowspan="2">Status</th>
                                    </tr>
                                    <tr>
                                        @foreach($items as $item)
                                            <th class="text-success">Stock</th>
                                            <th class="text-primary">Sold</th>
                                            <th class="text-info">Remaining</th> 
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody id="adBody">
                                    @foreach($dealers as $dealer)
                                    <tr>
                                        <td><span class="dealer-ref">{{ $dealer->dealer_reference }}</span></td>

                                        <td>
                                            <a href="view-dealer/{{$dealer->id}}" class="dealer-link">
                                                {{ $dealer->name }}
                                            </a>
                                        </td>

                                        <td>{{ strtoupper($dealer->store_name ?? '-') }}</td>
                                        <td>{{ strtoupper($dealer->store_type ?? '-') }}</td>
                                        <td>{{ $dealer->number }}</td>

                                        @foreach($items as $item)
                                            @php
                                                $stock = optional($dealer->orders->firstWhere('item', $item->product_name))->total_qty ?? 0;
                                                $sold  = optional($dealer->sales->firstWhere('item', $item->product_name))->total_qty ?? 0;
                                                $remaining = $stock - $sold;
                                            @endphp

                                            <!-- STOCK -->
                                            <td>
                                                <span class="dealer-metric is-stock">{{ number_format($stock) }}</span>
                                            </td>

                                            <!-- SOLD -->
                                            <td>
                                                <span class="dealer-metric is-sold">{{ number_format($sold) }}</span>
                                            </td>

                                            <!-- REMAINING -->
                                            <td>
                                                <span class="dealer-metric {{ $remaining < 0 ? 'is-negative' : 'is-remaining' }}">{{ number_format($remaining) }}</span>
                                            </td>
                                        @endforeach

                                        <td><div class="dealer-muted">{{ strtoupper($dealer->address ?? '-') }}</div></td>
                                        <td><div class="dealer-muted">{{ strtoupper($dealer->area ?? '-') }}</div></td>

                                        <td>
                                            @if($dealer->status == 'Active')
                                                <span class="dealer-status is-active">Active</span>
                                            @else 
                                                <span class="dealer-status is-inactive">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@include('new_dealer')
@section('javascript')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('.transaction-table').DataTable({
            pageLength: 10,
            autoWidth: false,
            language: {
                search: 'Search dealers:',
                lengthMenu: 'Show _MENU_ records',
                emptyTable: 'No dealers found.'
            }
        });
        initSelect2(); // initial load
    });

    
    $('#new_dealer').on('shown.bs.modal', function () {
        if (!map) {
            initMap();
        } else {
            setTimeout(() => {
                map.invalidateSize();
            }, 200);
        }
    });
</script>
{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("adFilter").addEventListener("change", function () {
            const selectedAdDealer = this.value;
            filterAdsByName(selectedAdDealer);
        });

        function filterAdsByName(adName) {
            const rows = document.querySelectorAll('#adBody tr');
            rows.forEach(row => {
                const dealerColumn = row.cells[0].textContent;
                if (adName === 'All' || dealerColumn === adName) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    });
</script> --}}
@endsection
