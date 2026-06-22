@extends('layouts.header')

@section('css')
<style>
    .adpo-page { max-width: 1360px; margin: 0 auto; color: #1f2937; }
    .adpo-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
    .adpo-title { margin: 0; color: #101828; font-size: 25px; font-weight: 800; line-height: 1.2; }
    .adpo-subtitle { margin: 5px 0 0; color: #667085; font-size: 13px; }
    .adpo-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .adpo-summary { display: grid; grid-template-columns: repeat(4, minmax(170px, 1fr)); gap: 12px; margin-bottom: 18px; }
    .adpo-tile { display: flex; align-items: center; gap: 13px; min-height: 94px; background: #fff; border: 1px solid #e6e9ef; border-radius: 8px; padding: 16px; box-shadow: 0 10px 24px rgba(15, 23, 42, .04); }
    .adpo-tile-icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; flex: 0 0 42px; border-radius: 8px; background: #f8fafc; color: #475467; font-size: 20px; }
    .adpo-tile-icon.pending { background: #fff7ed; color: #c2410c; }
    .adpo-tile-icon.completed { background: #ecfdf3; color: #027a48; }
    .adpo-tile-icon.amount { background: #eff6ff; color: #1d4ed8; }
    .adpo-tile span { display: block; color: #667085; font-size: 11px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .adpo-tile strong { display: block; margin-top: 4px; color: #101828; font-size: 22px; font-weight: 800; line-height: 1.15; }
    .adpo-panel { background: #fff; border: 1px solid #e6e9ef; border-radius: 8px; overflow: hidden; box-shadow: 0 12px 30px rgba(15, 23, 42, .05); }
    .adpo-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 16px; border-bottom: 1px solid #edf0f5; background: #fcfcfd; }
    .adpo-panel-title { margin: 0; color: #101828; font-size: 15px; font-weight: 800; }
    .adpo-panel-subtitle { margin: 3px 0 0; color: #667085; font-size: 12px; }
    .adpo-filters { display: grid; grid-template-columns: minmax(260px, 1fr) 160px auto auto; gap: 8px; align-items: center; max-width: 690px; width: 100%; }
    .adpo-search { position: relative; }
    .adpo-search i { position: absolute; left: 11px; top: 50%; color: #98a2b3; transform: translateY(-50%); pointer-events: none; }
    .adpo-search .form-control { padding-left: 34px; }
    .adpo-table { margin: 0; }
    .adpo-table thead th { background: #f8fafc; border-bottom: 1px solid #e6e9ef; color: #667085; font-size: 11px; font-weight: 800; letter-spacing: .04em; padding: 12px 16px; text-transform: uppercase; white-space: nowrap; }
    .adpo-table tbody td { border-bottom: 1px solid #f1f3f6; padding: 14px 16px; vertical-align: middle; color: #344054; }
    .adpo-table tbody tr:last-child td { border-bottom: 0; }
    .adpo-table tbody tr:hover { background: #fafafa; }
    .po-number { color: #101828; font-weight: 800; white-space: nowrap; }
    .po-date { color: #667085; font-size: 12px; white-space: nowrap; }
    .business-name { color: #101828; font-weight: 800; }
    .territory-text { display: block; max-width: 330px; overflow: hidden; color: #667085; font-size: 12px; text-overflow: ellipsis; white-space: nowrap; }
    .item-count { color: #344054; font-weight: 800; }
    .item-count small { display: block; margin-top: 2px; color: #667085; font-weight: 500; }
    .amount-text { color: #101828; font-weight: 800; white-space: nowrap; }
    .status-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 6px 10px; font-size: 11px; font-weight: 800; white-space: nowrap; }
    .status-pill::before { content: ""; width: 7px; height: 7px; border-radius: 999px; background: currentColor; }
    .status-pending { background: #fff7ed; color: #c2410c; }
    .status-approved { background: #eff6ff; color: #1d4ed8; }
    .status-processing { background: #f5f3ff; color: #6d28d9; }
    .status-completed { background: #ecfdf3; color: #027a48; }
    .status-cancelled { background: #fef2f2; color: #b42318; }
    .empty-state { padding: 54px 24px; text-align: center; }
    .empty-state i { display: inline-flex; align-items: center; justify-content: center; width: 54px; height: 54px; margin-bottom: 12px; border-radius: 8px; background: #f8fafc; color: #667085; font-size: 26px; }
    .empty-state h6 { margin: 0 0 5px; color: #101828; font-weight: 800; }
    .empty-state p { margin: 0; color: #667085; font-size: 13px; }
    @media (max-width: 992px) {
        .adpo-head, .adpo-panel-head { align-items: stretch; flex-direction: column; }
        .adpo-summary { grid-template-columns: repeat(2, minmax(160px, 1fr)); }
        .adpo-filters { grid-template-columns: 1fr 150px auto auto; max-width: none; }
        .adpo-table-wrap { overflow-x: auto; }
        .adpo-table { min-width: 980px; }
    }
    @media (max-width: 640px) {
        .adpo-filters { grid-template-columns: 1fr; }
        .adpo-actions .btn, .adpo-filters .btn { width: 100%; }
    }
    @media (max-width: 576px) { .adpo-summary { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
@php
    $hasFilters = request()->filled('search') || request()->filled('status');
    $pageTitle = $pageTitle ?? 'Area Distributor Purchase Orders';
    $pageSubtitle = $pageSubtitle ?? 'Create, review, and track ADPO submissions in a separate module.';
    $panelTitle = $panelTitle ?? 'Purchase Order History';
    $showCreateButton = $showCreateButton ?? true;
    $clearRoute = $clearRoute ?? route('ad-purchase-orders.index');
    $viewRouteName = $viewRouteName ?? 'ad-purchase-orders.show';
@endphp
    <div class="adpo-head">
        <div>
            <h4 class="adpo-title">{{ $pageTitle }}</h4>
            <p class="adpo-subtitle">{{ $pageSubtitle }}</p>
        </div>
        @if($showCreateButton)
            <div class="adpo-actions">
                <a href="{{ route('ad-purchase-orders.create') }}" class="btn btn-danger">
                    <i class="bi bi-plus-circle"></i> New ADPO
                </a>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="adpo-summary">
        <div class="adpo-tile">
            <div class="adpo-tile-icon"><i class="bi bi-receipt"></i></div>
            <div><span>Total Orders</span><strong>{{ number_format($summary['total']) }}</strong></div>
        </div>
        <div class="adpo-tile">
            <div class="adpo-tile-icon pending"><i class="bi bi-hourglass-split"></i></div>
            <div><span>Pending</span><strong>{{ number_format($summary['pending']) }}</strong></div>
        </div>
        <div class="adpo-tile">
            <div class="adpo-tile-icon completed"><i class="bi bi-check2-circle"></i></div>
            <div><span>Completed</span><strong>{{ number_format($summary['completed']) }}</strong></div>
        </div>
        <div class="adpo-tile">
            <div class="adpo-tile-icon amount"><i class="bi bi-cash-stack"></i></div>
            <div><span>Total Amount</span><strong>PHP {{ number_format($summary['amount'], 2) }}</strong></div>
        </div>
    </div>

    <div class="adpo-panel">
        <div class="adpo-panel-head">
            <div>
                <h6 class="adpo-panel-title">{{ $panelTitle }}</h6>
                <p class="adpo-panel-subtitle">{{ number_format($orders->count()) }} order(s) found</p>
            </div>
            <form method="GET" class="adpo-filters">
                <div class="adpo-search">
                    <i class="bi bi-search"></i>
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search PO, business, territory">
                </div>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach(['Pending','Approved','Processing','Completed','Cancelled'] as $status)
                        <option value="{{ $status }}" @if(request('status') === $status) selected @endif>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-secondary" type="submit">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                @if($hasFilters)
                    <a href="{{ $clearRoute }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="adpo-table-wrap">
            <table class="table table-hover adpo-table">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Business</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $statusClass = 'status-' . strtolower(str_replace(' ', '-', $order->status));
                            $submittedAt = $order->submitted_at ?: $order->created_at;
                        @endphp
                        <tr>
                            <td>
                                <div class="po-number">{{ $order->po_number }}</div>
                                <small class="text-muted">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</small>
                            </td>
                            <td>
                                <div class="business-name">{{ $order->business_name ?: 'Area Distributor' }}</div>
                                <span class="territory-text" title="{{ $order->authorized_territory ?: 'No territory set' }}">
                                    {{ $order->authorized_territory ?: 'No territory set' }}
                                </span>
                                @if(isset($showCreateButton) && !$showCreateButton)
                                    <span class="territory-text" title="{{ $order->delivery_address ?: 'No delivery address' }}">
                                        {{ $order->delivery_address ?: 'No delivery address' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>{{ optional($submittedAt)->format('M d, Y') }}</div>
                                <div class="po-date">{{ optional($submittedAt)->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="item-count">{{ number_format($order->total_qty) }} qty</div>
                                <small>{{ number_format($order->items->count()) }} line(s)</small>
                            </td>
                            <td class="amount-text">PHP {{ number_format($order->total_amount, 2) }}</td>
                            <td><span class="status-pill {{ $statusClass }}">{{ $order->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route($viewRouteName, $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h6>{{ $hasFilters ? 'No matching purchase orders' : 'No AD purchase orders yet' }}</h6>
                                    <p>{{ $hasFilters ? 'Try clearing the filters or searching another keyword.' : 'Create a new ADPO to start tracking submissions.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
