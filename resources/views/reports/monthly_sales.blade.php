@extends('layouts.header')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/2.40.0/tabler-icons.min.css" rel="stylesheet">
<style>
    .msr-page {
        --msr-navy: #173f66;
        --msr-blue: #2f6fa4;
        --msr-slate: #526a80;
        --msr-border: #e2e8f0;
        --msr-muted: #64748b;
        padding: 18px 12px 32px;
        color: #172033;
    }

    .msr-card,
    .msr-hero,
    .msr-kpi {
        background: #fff;
        border: 1px solid var(--msr-border);
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
    }

    .msr-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 22px 24px;
        margin-bottom: 16px;
        overflow: hidden;
        position: relative;
        background:
            radial-gradient(circle at 92% 15%, rgba(91, 194, 231, .18), transparent 28%),
            linear-gradient(135deg, #fff 0%, #f3f8ff 100%);
        border-left: 5px solid var(--msr-blue);
    }

    .msr-hero::after {
        position: absolute;
        right: -40px;
        bottom: -55px;
        width: 170px;
        height: 170px;
        content: "";
        border: 28px solid rgba(47, 111, 164, .05);
        border-radius: 50%;
        pointer-events: none;
    }

    .msr-hero-copy,
    .msr-actions {
        position: relative;
        z-index: 1;
    }

    .msr-eyebrow {
        color: var(--msr-blue);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .msr-hero h3 {
        margin: 4px 0 5px;
        color: #15324f;
        font-size: clamp(21px, 2vw, 26px);
        font-weight: 800;
    }

    .msr-hero p,
    .msr-report-head p {
        margin: 0;
        color: #718096;
        font-size: 13px;
    }

    .msr-actions,
    .msr-report-tools,
    .msr-report-badges {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .msr-actions .btn,
    .msr-filters .btn,
    .msr-report-tools .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
    }

    .msr-period {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 40px;
        padding: 8px 12px;
        color: #244f7a;
        background: #eaf2fb;
        border: 1px solid #d6e7f7;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 800;
    }

    .msr-kpi {
        display: flex;
        align-items: center;
        gap: 13px;
        min-height: 92px;
        padding: 16px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .msr-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, .09);
    }

    .msr-kpi-icon {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 48px;
        height: 48px;
        border-radius: 13px;
        font-size: 24px;
    }

    .msr-kpi-icon.is-sales { color: #15803d; background: #dcfce7; }
    .msr-kpi-icon.is-orders { color: #1d4ed8; background: #dbeafe; }
    .msr-kpi-icon.is-units { color: #7e22ce; background: #f3e8ff; }
    .msr-kpi-icon.is-partners { color: #c2410c; background: #ffedd5; }

    .msr-kpi small {
        display: block;
        margin-bottom: 3px;
        color: #718096;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    .msr-kpi strong {
        display: block;
        color: #172033;
        font-size: clamp(18px, 2vw, 22px);
        line-height: 1.15;
    }

    .msr-filters {
        padding: 18px;
        margin-bottom: 16px;
    }

    .msr-filters .form-label {
        margin-bottom: 6px;
        color: #46556a;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .35px;
        text-transform: uppercase;
    }

    .msr-filters .form-control,
    .msr-filters .form-select,
    .msr-table-search {
        min-height: 42px;
        border-color: #dbe4ee;
        border-radius: 9px;
        font-size: 13px;
    }

    .msr-filters .form-control:focus,
    .msr-filters .form-select:focus,
    .msr-table-search:focus {
        border-color: #73b9d4;
        box-shadow: 0 0 0 3px rgba(91, 194, 231, .14);
    }

    .msr-note {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 9px 12px;
        color: #5c6b7e;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 9px;
        font-size: 12px;
    }

    .msr-report {
        overflow: hidden;
    }

    .msr-report-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 17px 20px;
        border-bottom: 1px solid #e7edf4;
    }

    .msr-report-head h5 {
        margin: 0 0 3px;
        color: #172033;
        font-size: 16px;
        font-weight: 800;
    }

    .msr-report-badges span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        color: #53657a;
        background: #f1f5f9;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
    }

    .msr-report-tools {
        padding: 12px 16px;
        background: #fbfdff;
        border-bottom: 1px solid #e7edf4;
    }

    .msr-search-wrap {
        position: relative;
        width: min(320px, 100%);
    }

    .msr-search-wrap i {
        position: absolute;
        top: 50%;
        left: 12px;
        color: #94a3b8;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .msr-table-search {
        width: 100%;
        padding-left: 35px;
        background: #fff;
    }

    .msr-visible-count {
        margin-left: auto;
        color: var(--msr-muted);
        font-size: 11px;
        font-weight: 700;
    }

    .msr-scroll-hint {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
    }

    .msr-table-wrap {
        max-height: calc(100vh - 250px);
        overflow: auto;
        scrollbar-color: #9eabba #edf2f7;
        scrollbar-width: thin;
        overscroll-behavior: contain;
    }

    .msr-table {
        width: max-content;
        min-width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 11px;
    }

    .msr-table th,
    .msr-table td {
        padding: 9px 8px;
        background: #fff;
        border-right: 1px solid #dfe6ee;
        border-bottom: 1px solid #dfe6ee;
        vertical-align: middle;
    }

    .msr-table thead th {
        position: sticky;
        z-index: 6;
        color: #fff;
        background: var(--msr-slate);
        text-align: center;
        font-weight: 800;
    }

    .msr-table thead tr:first-child th {
        top: 0;
        height: 58px;
    }

    .msr-table thead tr:nth-child(2) th {
        top: 58px;
        height: 35px;
    }

    .msr-table tbody tr:nth-child(even) td {
        background: #fbfdff;
    }

    .msr-table tbody tr:hover td {
        background: #eef8fc;
    }

    .msr-fixed {
        text-align: left !important;
    }

    .msr-region-col { left: 0; width: 72px; min-width: 72px; }
    .msr-id-col { left: 72px; width: 175px; min-width: 175px; }
    .msr-business-col { left: 247px; width: 195px; min-width: 195px; }
    .msr-type-col { left: 442px; width: 130px; min-width: 130px; }
    .msr-project-col { left: 572px; width: 95px; min-width: 95px; }

    .msr-table thead .msr-fixed {
        z-index: 10;
        background: #40586e;
    }

    .msr-table tbody .msr-fixed {
        position: sticky;
        z-index: 4;
    }

    .msr-table tbody tr:nth-child(odd) .msr-fixed { background: #fff; }
    .msr-table tbody tr:nth-child(even) .msr-fixed { background: #fbfdff; }
    .msr-table tbody tr:hover .msr-fixed { background: #eef8fc; }

    .msr-project-col {
        box-shadow: 7px 0 14px rgba(15, 23, 42, .06);
    }

    .msr-id-col strong,
    .msr-business-col strong {
        display: block;
        overflow: hidden;
        color: #26384c;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .msr-id-col small {
        display: block;
        margin-top: 3px;
        color: #8290a3;
        font-size: 9px;
    }

    .msr-region {
        display: inline-grid;
        place-items: center;
        min-width: 38px;
        padding: 5px 7px;
        color: #1d4f7a;
        background: #e7f1fb;
        border-radius: 7px;
        font-weight: 900;
    }

    .msr-project {
        display: inline-flex;
        margin: 2px;
        padding: 4px 6px;
        color: #6b3fa0;
        background: #f2eafd;
        border-radius: 6px;
        font-size: 9px;
        font-weight: 900;
    }

    .msr-product-group {
        min-width: 190px;
        max-width: 220px;
        background: #647b90 !important;
    }

    .msr-product-group span,
    .msr-product-group strong {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .msr-product-group span {
        color: #d9f3ff;
        font-size: 9px;
        letter-spacing: .4px;
    }

    .msr-product-group strong {
        margin-top: 4px;
        font-size: 10px;
    }

    .msr-subhead {
        min-width: 82px;
        background: #7a8fa2 !important;
        font-size: 9px;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .msr-payment-group {
        background: #324b64 !important;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .msr-payment-head {
        min-width: 105px;
        max-width: 125px;
        background: #627a90 !important;
        font-size: 9px;
        white-space: normal;
    }

    .msr-number {
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .msr-qty {
        min-width: 74px;
        color: #315f92;
        font-weight: 800;
    }

    .msr-amount {
        min-width: 110px;
        color: #334155;
    }

    .msr-total-col {
        min-width: 130px;
        color: #fff !important;
        background: var(--msr-navy) !important;
        box-shadow: inset 1px 0 rgba(255, 255, 255, .08);
    }

    .msr-table tbody .msr-total-col {
        color: var(--msr-navy) !important;
        background: #e8f2fb !important;
        font-size: 11px;
    }

    .msr-payment-cell {
        min-width: 105px;
        color: #94a3b8;
    }

    .msr-payment-cell.has-value {
        color: #166534;
        background: #f0fdf4 !important;
        font-weight: 800;
    }

    .msr-table tfoot td {
        position: sticky;
        bottom: 0;
        z-index: 5;
        color: #fff;
        background: #334e68;
        border-color: #46627d;
        font-weight: 800;
    }

    .msr-grand-label {
        position: sticky !important;
        left: 0;
        z-index: 11 !important;
        text-align: right;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .msr-empty {
        height: 230px;
        color: #7b8a9d;
        text-align: center;
    }

    .msr-empty i,
    .msr-empty strong,
    .msr-empty span {
        display: block;
    }

    .msr-empty i { margin-bottom: 8px; font-size: 35px; }
    .msr-empty strong { color: #42526a; font-size: 15px; }
    .msr-empty span { margin-top: 4px; }

    .msr-row-hidden {
        display: none;
    }

    @media (max-width: 1199.98px) {
        .msr-visible-count {
            width: 100%;
            margin-left: 0;
        }
    }

    @media (max-width: 991.98px) {
        .msr-hero,
        .msr-report-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .msr-table tbody .msr-fixed,
        .msr-table thead .msr-fixed {
            position: static;
        }

        .msr-project-col {
            box-shadow: none;
        }

        .msr-table-wrap {
            max-height: 68vh;
        }
    }

    @media (max-width: 575.98px) {
        .msr-page { padding-inline: 4px; }
        .msr-hero { padding: 18px; }
        .msr-actions { width: 100%; }
        .msr-actions .btn { flex: 1 1 auto; }
        .msr-period { width: 100%; justify-content: center; }
        .msr-report-tools { align-items: stretch; flex-direction: column; }
        .msr-search-wrap { width: 100%; }
        .msr-scroll-hint { display: none; }
    }

    @media print {
        .sidebar,
        .topbar,
        .footer,
        .msr-actions,
        .msr-filters,
        .msr-report-tools {
            display: none !important;
        }

        .main-content,
        .content-area {
            position: static !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .msr-page { padding: 0; }
        .msr-hero { padding: 8px 10px; margin-bottom: 8px; }
        .msr-hero::after, .msr-eyebrow { display: none; }
        .msr-hero h3 { font-size: 16px; }
        .msr-kpi { min-height: 55px; padding: 8px; box-shadow: none; }
        .msr-kpi-icon { width: 30px; height: 30px; font-size: 15px; }
        .msr-kpi strong { font-size: 13px; }
        .msr-card, .msr-hero, .msr-kpi { box-shadow: none; }
        .msr-report-head { padding: 8px 10px; }
        .msr-table-wrap { max-height: none; overflow: visible; }
        .msr-table { width: 100%; font-size: 6px; }
        .msr-table th, .msr-table td { padding: 3px; }
        .msr-table thead th,
        .msr-table tbody .msr-fixed,
        .msr-table tfoot td {
            position: static !important;
        }

        .msr-row-hidden { display: table-row; }
        .msr-client-empty { display: none !important; }

        @page {
            size: landscape;
            margin: 6mm;
        }
    }
</style>
@endsection

@section('content')
@php
    $columnCount = 6 + ($products->count() * 2) + $paymentColumns->count();
    $hasFilters = collect(['month', 'region', 'project', 'sales_status', 'distributor', 'product'])
        ->contains(fn ($filter) => request()->filled($filter));
@endphp

<div class="container-fluid msr-page">
    <section class="msr-hero">
        <div class="msr-hero-copy">
            <div class="msr-eyebrow">Sales Analytics</div>
            <h3>Monthly Sales Report</h3>
            <p>{{ $period->format('F Y') }} distributor sales, product performance, and payment collection summary.</p>
        </div>
        <div class="msr-actions">
            <span class="msr-period">
                <i class="ti ti-calendar-month" aria-hidden="true"></i>
                {{ $period->format('F Y') }}
            </span>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="ti ti-printer" aria-hidden="true"></i>
                Print
            </button>
            <a href="{{ route('monthly-sales.export', request()->query()) }}" class="btn btn-success">
                <i class="ti ti-file-spreadsheet" aria-hidden="true"></i>
                Export Excel
            </a>
        </div>
    </section>

    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-md-6">
            <article class="msr-kpi">
                <span class="msr-kpi-icon is-sales"><i class="ti ti-currency-peso" aria-hidden="true"></i></span>
                <div>
                    <small>Total Sales</small>
                    <strong>&#8369;{{ number_format($summary->total_sales, 2) }}</strong>
                </div>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="msr-kpi">
                <span class="msr-kpi-icon is-orders"><i class="ti ti-receipt-2" aria-hidden="true"></i></span>
                <div>
                    <small>Transactions</small>
                    <strong>{{ number_format($summary->transactions) }}</strong>
                </div>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="msr-kpi">
                <span class="msr-kpi-icon is-units"><i class="ti ti-packages" aria-hidden="true"></i></span>
                <div>
                    <small>Units Sold</small>
                    <strong>{{ number_format($summary->total_qty) }}</strong>
                </div>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="msr-kpi">
                <span class="msr-kpi-icon is-partners"><i class="ti ti-building-store" aria-hidden="true"></i></span>
                <div>
                    <small>Selling Distributors</small>
                    <strong>{{ number_format($summary->active_distributors) }}</strong>
                </div>
            </article>
        </div>
    </div>

    <section class="msr-card msr-filters" aria-labelledby="monthlySalesFilters">
        <form method="GET" action="{{ route('monthly-sales') }}" class="row g-3 align-items-end">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <h5 id="monthlySalesFilters" class="mb-0 fw-bold fs-6">Report Filters</h5>
                    @if($hasFilters)
                        <span class="badge rounded-pill text-bg-primary">Filters applied</span>
                    @endif
                </div>
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label" for="msrMonth">Report month</label>
                <input id="msrMonth" type="month" name="month" class="form-control" value="{{ request('month', $period->format('Y-m')) }}">
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label" for="msrRegion">Region</label>
                <select id="msrRegion" name="region" class="form-select">
                    <option value="">All regions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region }}" {{ request('region') === $region ? 'selected' : '' }}>{{ $region }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label" for="msrProject">Project</label>
                <select id="msrProject" name="project" class="form-select">
                    <option value="">All projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project }}" {{ request('project') === $project ? 'selected' : '' }}>{{ $project }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label" for="msrStatus">Sales status</label>
                <select id="msrStatus" name="sales_status" class="form-select">
                    <option value="">All distributors</option>
                    <option value="with_sales" {{ request('sales_status') === 'with_sales' ? 'selected' : '' }}>With sales</option>
                    <option value="no_sales" {{ request('sales_status') === 'no_sales' ? 'selected' : '' }}>No sales</option>
                </select>
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label" for="msrDistributor">Distributor</label>
                <input id="msrDistributor" type="search" name="distributor" class="form-control" value="{{ request('distributor') }}" placeholder="ID or business name">
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label" for="msrProduct">Product / SKU</label>
                <input id="msrProduct" type="search" name="product" class="form-control" value="{{ request('product') }}" placeholder="Find product">
            </div>
            <div class="col-xl-3 col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="ti ti-filter" aria-hidden="true"></i>
                    Apply filters
                </button>
                <a href="{{ route('monthly-sales') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-refresh" aria-hidden="true"></i>
                    Reset
                </a>
            </div>
            <div class="col-xl-9">
                <div class="msr-note">
                    <i class="ti ti-info-circle" aria-hidden="true"></i>
                    Only completed sales dated {{ $from->format('M d') }}&ndash;{{ $to->format('M d, Y') }} are included. Amount = quantity &times; selling price.
                </div>
            </div>
        </form>
    </section>

    <section class="msr-card msr-report" aria-labelledby="monthlySalesMatrix">
        <header class="msr-report-head">
            <div>
                <h5 id="monthlySalesMatrix">Distributor Sales Matrix</h5>
                <p>{{ $rows->count() }} distributor{{ $rows->count() === 1 ? '' : 's' }}, {{ $products->count() }} product{{ $products->count() === 1 ? '' : 's' }}</p>
            </div>
            <div class="msr-report-badges">
                <span><i class="ti ti-box" aria-hidden="true"></i> Product Qty &amp; Amount</span>
                <span><i class="ti ti-wallet" aria-hidden="true"></i> Payment Allocation</span>
            </div>
        </header>

        <div class="msr-report-tools">
            <div class="msr-search-wrap">
                <i class="ti ti-search" aria-hidden="true"></i>
                <input id="msrTableSearch" type="search" class="form-control msr-table-search" placeholder="Search visible distributors…" autocomplete="off">
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="msrJumpPayments">
                <i class="ti ti-arrow-bar-to-right" aria-hidden="true"></i>
                Jump to payments
            </button>
            <span class="msr-scroll-hint"><i class="ti ti-arrows-horizontal"></i> Scroll horizontally for product and payment details</span>
            <span class="msr-visible-count" id="msrVisibleCount">{{ $rows->count() }} visible row{{ $rows->count() === 1 ? '' : 's' }}</span>
        </div>

        <div class="msr-table-wrap" id="msrTableWrap" tabindex="0" aria-label="Scrollable monthly sales table">
            <table class="msr-table" id="msrTable">
                <thead>
                    <tr>
                        <th rowspan="2" class="msr-fixed msr-region-col" scope="col">Region</th>
                        <th rowspan="2" class="msr-fixed msr-id-col" scope="col">Authorized Distributor ID</th>
                        <th rowspan="2" class="msr-fixed msr-business-col" scope="col">Business Name</th>
                        <th rowspan="2" class="msr-fixed msr-type-col" scope="col">Customer Type</th>
                        <th rowspan="2" class="msr-fixed msr-project-col" scope="col">Project</th>
                        @foreach($products as $product)
                            <th colspan="2" class="msr-product-group" scope="colgroup" title="{{ $product->product_name }}">
                                <span>{{ $product->sku ?: 'NO SKU' }}</span>
                                <strong>{{ $product->product_name }}</strong>
                            </th>
                        @endforeach
                        <th rowspan="2" class="msr-total-col" scope="col">Total Amount</th>
                        <th colspan="{{ $paymentColumns->count() }}" class="msr-payment-group" scope="colgroup">Payment</th>
                    </tr>
                    <tr>
                        @foreach($products as $product)
                            <th class="msr-subhead" scope="col">Qty</th>
                            <th class="msr-subhead" scope="col">Amount</th>
                        @endforeach
                        @foreach($paymentColumns as $label)
                            <th class="msr-payment-head" scope="col">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr data-msr-row data-search="{{ strtolower(implode(' ', [
                            $row->region,
                            $row->region_name,
                            $row->distributor_id,
                            $row->business_name,
                            $row->customer_type,
                            $row->projects->implode(' '),
                        ])) }}">
                            <td class="msr-fixed msr-region-col">
                                <span class="msr-region" title="{{ $row->region_name }}">{{ $row->region }}</span>
                            </td>
                            <td class="msr-fixed msr-id-col">
                                <strong title="{{ $row->distributor_id }}">{{ $row->distributor_id }}</strong>
                                <small>{{ number_format($row->transaction_count) }} transaction{{ $row->transaction_count === 1 ? '' : 's' }}</small>
                            </td>
                            <td class="msr-fixed msr-business-col">
                                <strong title="{{ $row->business_name }}">{{ $row->business_name ?: 'Unnamed business' }}</strong>
                            </td>
                            <td class="msr-fixed msr-type-col">{{ $row->customer_type }}</td>
                            <td class="msr-fixed msr-project-col">
                                @forelse($row->projects as $project)
                                    <span class="msr-project">{{ $project }}</span>
                                @empty
                                    <span class="text-muted">&mdash;</span>
                                @endforelse
                            </td>
                            @foreach($products as $product)
                                @php
                                    $productTotal = $row->product_totals->get($product->key);
                                    $productQty = (float) optional($productTotal)->qty;
                                    $productAmount = (float) optional($productTotal)->amount;
                                @endphp
                                <td class="msr-number msr-qty">{{ number_format($productQty) }}</td>
                                <td class="msr-number msr-amount">&#8369;{{ number_format($productAmount, 2) }}</td>
                            @endforeach
                            <td class="msr-number msr-total-col"><strong>&#8369;{{ number_format($row->total_amount, 2) }}</strong></td>
                            @foreach($paymentColumns as $key => $label)
                                @php
                                    $paymentAmount = (float) $row->payment_totals->get($key, 0);
                                @endphp
                                <td class="msr-number msr-payment-cell {{ $paymentAmount > 0 ? 'has-value' : '' }}">
                                    &#8369;{{ number_format($paymentAmount, 2) }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $columnCount }}" class="msr-empty">
                                <i class="ti ti-chart-bar-off" aria-hidden="true"></i>
                                <strong>No completed sales found</strong>
                                <span>Change the month or filters to view another sales period.</span>
                            </td>
                        </tr>
                    @endforelse
                    @if($rows->isNotEmpty())
                        <tr id="msrClientEmpty" class="msr-row-hidden msr-client-empty">
                            <td colspan="{{ $columnCount }}" class="msr-empty">
                                <i class="ti ti-search-off" aria-hidden="true"></i>
                                <strong>No matching distributor</strong>
                                <span>Try another table search.</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
                @if($rows->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td colspan="5" class="msr-grand-label">Grand Total</td>
                            @foreach($products as $product)
                                @php
                                    $grand = $productGrandTotals->get($product->key);
                                    $grandQty = (float) optional($grand)->qty;
                                    $grandAmount = (float) optional($grand)->amount;
                                @endphp
                                <td class="msr-number">{{ number_format($grandQty) }}</td>
                                <td class="msr-number">&#8369;{{ number_format($grandAmount, 2) }}</td>
                            @endforeach
                            <td class="msr-number msr-total-col"><strong>&#8369;{{ number_format($summary->total_sales, 2) }}</strong></td>
                            @foreach($paymentColumns as $key => $label)
                                <td class="msr-number">&#8369;{{ number_format($paymentGrandTotals->get($key, 0), 2) }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('msrTableSearch');
        const tableWrap = document.getElementById('msrTableWrap');
        const paymentHeader = document.querySelector('.msr-payment-group');
        const jumpButton = document.getElementById('msrJumpPayments');
        const visibleCount = document.getElementById('msrVisibleCount');
        const clientEmpty = document.getElementById('msrClientEmpty');
        const rows = Array.from(document.querySelectorAll('[data-msr-row]'));

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                let count = 0;

                rows.forEach(function (row) {
                    const matches = !query || row.dataset.search.includes(query);
                    row.classList.toggle('msr-row-hidden', !matches);
                    if (matches) count++;
                });

                if (visibleCount) {
                    visibleCount.textContent = count + ' visible row' + (count === 1 ? '' : 's');
                }

                if (clientEmpty) {
                    clientEmpty.classList.toggle('msr-row-hidden', count !== 0);
                }
            });
        }

        if (jumpButton && tableWrap && paymentHeader) {
            jumpButton.addEventListener('click', function () {
                const targetLeft = paymentHeader.offsetLeft - tableWrap.clientWidth * .35;
                tableWrap.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
            });
        }
    });
</script>
@endsection
