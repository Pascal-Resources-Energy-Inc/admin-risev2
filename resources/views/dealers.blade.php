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

    .dealer-tabs {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
    }

    .dealer-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 36px;
        padding: 7px 13px;
        border: 0;
        border-radius: 7px;
        color: #64748b;
        background: transparent;
        font-size: 12px;
        font-weight: 800;
        transition: color .18s ease, background-color .18s ease, box-shadow .18s ease;
    }

    .dealer-tab:hover {
        color: #1d4ed8;
        background: #fff;
    }

    .dealer-tab.active {
        color: #1d4ed8;
        background: #fff;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .08);
    }

    .dealer-tab[data-dealer-tab="Regular"].active {
        color: #047857;
    }

    .dealer-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 23px;
        height: 23px;
        padding: 0 7px;
        border-radius: 999px;
        color: inherit;
        background: #e2e8f0;
        font-size: 11px;
    }

    .dealer-tab.active .dealer-tab-count {
        background: #dbeafe;
    }

    .dealer-tab[data-dealer-tab="Regular"].active .dealer-tab-count {
        background: #d1fae5;
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

    .dealer-type {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 76px;
        min-height: 28px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .dealer-type.is-project {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .dealer-type.is-regular {
        color: #047857;
        background: #d1fae5;
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
    $projectDealerCount = $dealers->filter(function ($dealer) {
        return strcasecmp((string) ($dealer->dealer_type ?: 'Project'), 'Regular') !== 0;
    })->count();
    $regularDealerCount = $dealers->filter(function ($dealer) {
        return strcasecmp((string) $dealer->dealer_type, 'Regular') === 0;
    })->count();
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
                        <h5 class="mb-0" id="dealerTableTitle">Project Dealers</h5>
                        <div class="small text-muted" id="dealerTableCount">{{ $projectDealerCount }} record{{ $projectDealerCount == 1 ? '' : 's' }} listed</div>
                    </div>
                    <div class="dealer-tabs mt-3 mt-lg-0" role="tablist" aria-label="Dealer type">
                        <button type="button"
                            class="dealer-tab active"
                            data-dealer-tab="Project"
                            data-count="{{ $projectDealerCount }}"
                            role="tab"
                            aria-selected="true">
                            <i class="ti ti-building-community"></i>
                            Project
                            <span class="dealer-tab-count">{{ number_format($projectDealerCount) }}</span>
                        </button>
                        <button type="button"
                            class="dealer-tab"
                            data-dealer-tab="Regular"
                            data-count="{{ $regularDealerCount }}"
                            role="tab"
                            aria-selected="false">
                            <i class="ti ti-building-store"></i>
                            Regular
                            <span class="dealer-tab-count">{{ number_format($regularDealerCount) }}</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if(auth()->user()->role == 'Admin')
                            <table class="table dealer-table transaction-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{ $dealerSingularTitle }} Reference</th>
                                        {{-- <th>Dealer Type</th> --}}
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
                                    @php $dealerType = strcasecmp((string) $dealer->dealer_type, 'Regular') === 0 ? 'Regular' : 'Project'; @endphp
                                    <tr data-dealer-type="{{ $dealerType }}">
                                        <td scope="col"><span class="dealer-ref">{{  strtoupper($dealer->dealer_reference) }}</span></td>
                                        {{-- <td>
                                            <span class="dealer-type {{ $dealerType === 'Regular' ? 'is-regular' : 'is-project' }}">
                                                {{ strtoupper($dealerType) }}
                                            </span>
                                        </td> --}}
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
                                        {{-- <th rowspan="2">Dealer Type</th> --}}
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
                                    @php $dealerType = strcasecmp((string) $dealer->dealer_type, 'Regular') === 0 ? 'Regular' : 'Project'; @endphp
                                    <tr data-dealer-type="{{ $dealerType }}">
                                        <td><span class="dealer-ref">{{ $dealer->dealer_reference }}</span></td>
                                        {{-- <td>
                                            <span class="dealer-type {{ $dealerType === 'Regular' ? 'is-regular' : 'is-project' }}">
                                                {{ strtoupper($dealerType) }}
                                            </span>
                                        </td> --}}

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
        let activeDealerType = 'Project';
        const $dealerTable = $('.transaction-table');
        const dealerTableNode = $dealerTable.get(0);

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable !== dealerTableNode) {
                return true;
            }

            const row = settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;
            const rowDealerType = row ? row.getAttribute('data-dealer-type') : null;

            return rowDealerType === activeDealerType;
        });

        const dealerTable = $dealerTable.DataTable({
            pageLength: 10,
            autoWidth: false,
            language: {
                search: 'Search dealers:',
                lengthMenu: 'Show _MENU_ records',
                emptyTable: 'No dealers found.'
            }
        });

        dealerTable.draw();

        $('.dealer-tab').on('click', function () {
            const $tab = $(this);
            activeDealerType = $tab.data('dealer-tab');
            const count = Number($tab.data('count') || 0);

            $('.dealer-tab')
                .removeClass('active')
                .attr('aria-selected', 'false');
            $tab
                .addClass('active')
                .attr('aria-selected', 'true');

            $('#dealerTableTitle').text(activeDealerType + ' Dealers');
            $('#dealerTableCount').text(
                count.toLocaleString() + ' record' + (count === 1 ? '' : 's') + ' listed'
            );

            dealerTable.search('').page('first').draw();
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
