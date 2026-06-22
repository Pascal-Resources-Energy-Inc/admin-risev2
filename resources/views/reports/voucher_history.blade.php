@extends('layouts.header')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/2.40.0/tabler-icons.min.css" rel="stylesheet">
<style>
    .vhr-page { padding: 18px 12px 32px; color: #172033; }
    .vhr-hero, .vhr-card, .vhr-kpi { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 10px 30px rgba(15,23,42,.06); }
    .vhr-hero { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:22px 24px; margin-bottom:16px; background:linear-gradient(135deg,#fff 0%,#fff8ed 100%); border-left:5px solid #d97706; }
    .vhr-eyebrow { color:#b45309; font-size:10px; font-weight:900; letter-spacing:1.5px; text-transform:uppercase; }
    .vhr-hero h3 { margin:4px 0 5px; color:#3b2a16; font-size:clamp(21px,2vw,26px); font-weight:800; }
    .vhr-hero p, .vhr-head p { margin:0; color:#718096; font-size:13px; }
    .vhr-actions, .vhr-legend, .vhr-tools { display:flex; align-items:center; flex-wrap:wrap; gap:8px; }
    .vhr-actions .btn, .vhr-filters .btn, .vhr-tools .btn { min-height:40px; display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:9px; font-size:12px; font-weight:700; }
    .vhr-period { min-height:40px; display:inline-flex; align-items:center; gap:7px; padding:8px 12px; color:#92400e; background:#ffedd5; border-radius:9px; font-size:12px; font-weight:800; }
    .vhr-kpi { min-height:92px; display:flex; align-items:center; gap:13px; padding:16px; }
    .vhr-kpi-icon { width:48px; height:48px; display:grid; place-items:center; flex:0 0 auto; border-radius:13px; font-size:23px; }
    .vhr-kpi-icon.is-voucher { color:#7c3aed; background:#ede9fe; }
    .vhr-kpi-icon.is-used { color:#15803d; background:#dcfce7; }
    .vhr-kpi-icon.is-rebate { color:#b45309; background:#fef3c7; }
    .vhr-kpi-icon.is-audit { color:#1d4ed8; background:#dbeafe; }
    .vhr-kpi small { display:block; margin-bottom:3px; color:#718096; font-size:10px; font-weight:800; text-transform:uppercase; }
    .vhr-kpi strong { display:block; font-size:clamp(18px,2vw,22px); line-height:1.15; }
    .vhr-filters { padding:18px; margin-bottom:16px; }
    .vhr-filters .form-label { margin-bottom:6px; color:#46556a; font-size:10px; font-weight:800; text-transform:uppercase; }
    .vhr-filters .form-control, .vhr-filters .form-select, .vhr-search { min-height:42px; border-color:#dce5ef; border-radius:9px; font-size:13px; }
    .vhr-head { display:flex; align-items:center; justify-content:space-between; gap:15px; padding:17px 20px; border-bottom:1px solid #e7edf4; }
    .vhr-head h5 { margin:0 0 3px; font-size:16px; font-weight:800; }
    .vhr-legend span { padding:6px 9px; color:#53657a; background:#f1f5f9; border-radius:8px; font-size:10px; font-weight:800; }
    .vhr-tools { padding:12px 16px; background:#fbfdff; border-bottom:1px solid #e7edf4; }
    .vhr-search-wrap { position:relative; width:min(320px,100%); }
    .vhr-search-wrap i { position:absolute; top:50%; left:12px; color:#94a3b8; transform:translateY(-50%); }
    .vhr-search { width:100%; padding-left:35px; }
    .vhr-visible { margin-left:auto; color:#64748b; font-size:11px; font-weight:700; }
    .vhr-table-wrap { overflow:auto; max-height:calc(100vh - 270px); }
    .vhr-table { width:100%; min-width:1280px; border-collapse:separate; border-spacing:0; font-size:11px; }
    .vhr-table th, .vhr-table td { padding:10px 9px; background:#fff; border-right:1px solid #e7edf4; border-bottom:1px solid #e7edf4; vertical-align:middle; }
    .vhr-table thead th { position:sticky; top:0; z-index:4; color:#fff; background:#536a7f; font-size:10px; font-weight:800; text-transform:uppercase; }
    .vhr-table tbody tr:nth-child(even) td { background:#fbfdff; }
    .vhr-table tbody tr:hover td { background:#fff8ed; }
    .vhr-code { display:block; color:#5b21b6; font-size:12px; font-weight:900; }
    .vhr-sub { display:block; margin-top:3px; color:#8492a6; font-size:9px; }
    .vhr-areas { max-width:210px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .vhr-status { display:inline-flex; align-items:center; gap:5px; padding:5px 8px; border-radius:999px; font-size:9px; font-weight:900; text-transform:uppercase; }
    .vhr-status.is-active { color:#166534; background:#dcfce7; }
    .vhr-status.is-expired, .vhr-status.is-deleted, .vhr-status.is-inactive { color:#991b1b; background:#fee2e2; }
    .vhr-status.is-scheduled { color:#1d4ed8; background:#dbeafe; }
    .vhr-status.is-used-up { color:#9a5b06; background:#fef3c7; }
    .vhr-usage { min-width:120px; }
    .vhr-progress { height:5px; margin-top:5px; overflow:hidden; background:#e2e8f0; border-radius:999px; }
    .vhr-progress span { display:block; height:100%; background:linear-gradient(90deg,#16a34a,#22c55e); border-radius:inherit; }
    .vhr-money { text-align:right; font-variant-numeric:tabular-nums; white-space:nowrap; }
    .vhr-history-btn { display:inline-flex; align-items:center; gap:5px; border-radius:8px; font-size:10px; font-weight:800; }
    .vhr-empty { height:220px; color:#7b8a9d; text-align:center; }
    .vhr-empty i, .vhr-empty strong, .vhr-empty span { display:block; }
    .vhr-empty i { margin-bottom:8px; font-size:34px; }
    .vhr-row-hidden { display:none; }
    .vhr-modal .modal-content { border:0; border-radius:16px; overflow:hidden; box-shadow:0 24px 70px rgba(15,23,42,.22); }
    .vhr-modal-head { padding:18px 20px; background:linear-gradient(135deg,#fff7ed,#fff); border-bottom:1px solid #fed7aa; }
    .vhr-modal-meta { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
    .vhr-modal-meta span { padding:5px 8px; color:#6b4f2f; background:#fff; border:1px solid #fed7aa; border-radius:7px; font-size:10px; font-weight:800; }
    .vhr-timeline { position:relative; padding-left:22px; }
    .vhr-timeline::before { position:absolute; top:4px; bottom:4px; left:7px; width:2px; content:""; background:#e2e8f0; }
    .vhr-event { position:relative; padding:0 0 18px 12px; }
    .vhr-event::before { position:absolute; top:4px; left:-20px; width:11px; height:11px; content:""; background:#64748b; border:3px solid #fff; border-radius:50%; box-shadow:0 0 0 2px #cbd5e1; }
    .vhr-event.is-usage::before { background:#16a34a; box-shadow:0 0 0 2px #bbf7d0; }
    .vhr-event h6 { margin:0; font-size:12px; font-weight:800; }
    .vhr-event-time { color:#8492a6; font-size:9px; }
    .vhr-event p { margin:5px 0 0; color:#5c6b7e; font-size:11px; }
    .vhr-change { margin-top:7px; overflow:hidden; border:1px solid #e2e8f0; border-radius:8px; }
    .vhr-change-row { display:grid; grid-template-columns:130px 1fr 1fr; gap:8px; padding:7px 9px; font-size:9px; border-bottom:1px solid #eef2f7; }
    .vhr-change-row:last-child { border-bottom:0; }
    @media(max-width:991.98px){ .vhr-hero,.vhr-head{align-items:flex-start;flex-direction:column}.vhr-visible{width:100%;margin-left:0} }
    @media(max-width:575.98px){ .vhr-page{padding-inline:4px}.vhr-hero{padding:18px}.vhr-actions{width:100%}.vhr-actions .btn{flex:1}.vhr-period{width:100%;justify-content:center}.vhr-tools{align-items:stretch;flex-direction:column}.vhr-search-wrap{width:100%} }
    @media print {
        .sidebar,.topbar,.footer,.vhr-actions,.vhr-filters,.vhr-tools,.vhr-history-btn{display:none!important}
        .main-content,.content-area{position:static!important;width:100%!important;margin:0!important;padding:0!important}
        .vhr-page{padding:0}.vhr-hero,.vhr-card,.vhr-kpi{box-shadow:none}.vhr-table-wrap{max-height:none;overflow:visible}
        .vhr-table{min-width:100%;font-size:7px}.vhr-table th,.vhr-table td{padding:4px}.vhr-table thead th{position:static}
        @page{size:landscape;margin:7mm}
    }
</style>
@endsection

@section('content')
@php
    $hasFilters = collect(['from','to','search','distributor','status','usage','event','order_status'])
        ->contains(fn ($key) => request()->filled($key));
    $histories = $rows->mapWithKeys(fn ($row) => [$row->id => [
        'code' => $row->code,
        'distributor' => $row->distributor,
        'discount' => $row->discount_label,
        'status' => $row->status,
        'timeline' => $row->timeline,
    ]]);
@endphp

<div class="container-fluid vhr-page">
    <section class="vhr-hero">
        <div>
            <div class="vhr-eyebrow">Voucher Intelligence</div>
            <h3>Voucher History Report</h3>
            <p>Voucher configuration, audit changes, and purchase-order usage from {{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }}.</p>
        </div>
        <div class="vhr-actions">
            <span class="vhr-period"><i class="ti ti-calendar-stats"></i>{{ $from->format('M d') }} – {{ $to->format('M d, Y') }}</span>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="ti ti-printer"></i>Print</button>
            <a href="{{ route('voucher-history.export', request()->query()) }}" class="btn btn-success"><i class="ti ti-file-spreadsheet"></i>Export Excel</a>
        </div>
    </section>

    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-md-6"><article class="vhr-kpi"><span class="vhr-kpi-icon is-voucher"><i class="ti ti-ticket"></i></span><div><small>Vouchers</small><strong>{{ number_format($summary->vouchers) }}</strong></div></article></div>
        <div class="col-xl-3 col-md-6"><article class="vhr-kpi"><span class="vhr-kpi-icon is-used"><i class="ti ti-shopping-cart-check"></i></span><div><small>Usage Events</small><strong>{{ number_format($summary->usage_events) }}</strong></div></article></div>
        <div class="col-xl-3 col-md-6"><article class="vhr-kpi"><span class="vhr-kpi-icon is-rebate"><i class="ti ti-discount-2"></i></span><div><small>Voucher Rebates</small><strong>₱{{ number_format($summary->rebate_total, 2) }}</strong></div></article></div>
        <div class="col-xl-3 col-md-6"><article class="vhr-kpi"><span class="vhr-kpi-icon is-audit"><i class="ti ti-history"></i></span><div><small>Audit Events</small><strong>{{ number_format($summary->audit_events) }}</strong></div></article></div>
    </div>

    <section class="vhr-card vhr-filters">
        <form method="GET" action="{{ route('voucher-history') }}" class="row g-3 align-items-end">
            <div class="col-12 d-flex justify-content-between align-items-center"><h5 class="mb-0 fs-6 fw-bold">Report Filters</h5>@if($hasFilters)<span class="badge rounded-pill text-bg-primary">Filters applied</span>@endif</div>
            <div class="col-xl-2 col-md-6"><label class="form-label">From</label><input type="date" name="from" class="form-control" value="{{ request('from', $from->format('Y-m-d')) }}"></div>
            <div class="col-xl-2 col-md-6"><label class="form-label">To</label><input type="date" name="to" class="form-control" value="{{ request('to', $to->format('Y-m-d')) }}"></div>
            <div class="col-xl-2 col-md-6"><label class="form-label">Distributor</label><select name="distributor" class="form-select"><option value="">All distributors</option>@foreach($distributors as $distributor)<option value="{{ $distributor }}" {{ request('distributor') === $distributor ? 'selected' : '' }}>{{ $distributor }}</option>@endforeach</select></div>
            <div class="col-xl-2 col-md-6"><label class="form-label">Voucher status</label><select name="status" class="form-select"><option value="">All statuses</option>@foreach($statuses as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>@endforeach</select></div>
            <div class="col-xl-2 col-md-6"><label class="form-label">Usage</label><select name="usage" class="form-select"><option value="">All vouchers</option><option value="used" {{ request('usage') === 'used' ? 'selected' : '' }}>Used in period</option><option value="unused" {{ request('usage') === 'unused' ? 'selected' : '' }}>Unused in period</option></select></div>
            <div class="col-xl-2 col-md-6"><label class="form-label">Audit event</label><select name="event" class="form-select"><option value="">All events</option>@foreach(['created','updated','deleted','restored'] as $event)<option value="{{ $event }}" {{ request('event') === $event ? 'selected' : '' }}>{{ ucfirst($event) }}</option>@endforeach</select></div>
            <div class="col-xl-2 col-md-6"><label class="form-label">Order status</label><select name="order_status" class="form-select"><option value="">All order statuses</option>@foreach($orderStatuses as $status)<option value="{{ $status }}" {{ request('order_status') === $status ? 'selected' : '' }}>{{ $status }}</option>@endforeach</select></div>
            <div class="col-xl-4 col-md-6"><label class="form-label">Voucher search</label><input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Code, distributor, or description"></div>
            <div class="col-xl-3 col-md-6 d-flex gap-2"><button class="btn btn-primary flex-grow-1"><i class="ti ti-filter"></i>Apply filters</button><a href="{{ route('voucher-history') }}" class="btn btn-outline-secondary"><i class="ti ti-refresh"></i>Reset</a></div>
        </form>
    </section>

    <section class="vhr-card">
        <header class="vhr-head"><div><h5>Voucher Register & History</h5><p>{{ $rows->count() }} voucher{{ $rows->count() === 1 ? '' : 's' }} with {{ number_format($summary->usage_events + $summary->audit_events) }} recorded event{{ ($summary->usage_events + $summary->audit_events) === 1 ? '' : 's' }}</p></div><div class="vhr-legend"><span>Lifetime usage</span><span>Period activity</span><span>Audit trail</span></div></header>
        <div class="vhr-tools"><div class="vhr-search-wrap"><i class="ti ti-search"></i><input id="vhrSearch" class="form-control vhr-search" type="search" placeholder="Search visible vouchers…"></div><span class="vhr-visible" id="vhrVisible">{{ $rows->count() }} visible row{{ $rows->count() === 1 ? '' : 's' }}</span></div>
        <div class="vhr-table-wrap">
            <table class="vhr-table">
                <thead><tr><th>Voucher</th><th>Distributor / Areas</th><th>Discount</th><th>Status</th><th>Validity</th><th>Usage</th><th class="text-end">Period Rebate</th><th class="text-end">Order Value</th><th>Events</th><th>History</th></tr></thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $usagePercent = $row->usage_limit ? min(100, ($row->lifetime_used / $row->usage_limit) * 100) : 0;
                            $statusClass = 'is-' . str_replace(' ', '-', strtolower($row->status));
                        @endphp
                        <tr data-vhr-row data-search="{{ strtolower(implode(' ', [$row->code,$row->distributor,$row->description,$row->status,$row->areas->implode(' ')])) }}">
                            <td><span class="vhr-code">{{ $row->code }}</span><span class="vhr-sub">{{ $row->description ?: 'No description' }}</span></td>
                            <td><strong>{{ $row->distributor }}</strong><span class="vhr-sub vhr-areas" title="{{ $row->areas->implode(', ') }}">{{ $row->areas->isNotEmpty() ? $row->areas->implode(', ') : 'All areas' }}</span></td>
                            <td><strong>{{ $row->discount_label }}</strong><span class="vhr-sub">Minimum: ₱{{ number_format($row->minimum_order_amount, 2) }}</span></td>
                            <td><span class="vhr-status {{ $statusClass }}">{{ $row->status }}</span></td>
                            <td><strong>{{ $row->starts_at ?: 'Immediate' }}</strong><span class="vhr-sub">to {{ $row->expires_at ?: 'No expiry' }}</span></td>
                            <td><div class="vhr-usage"><strong>{{ number_format($row->lifetime_used) }} / {{ $row->usage_limit ? number_format($row->usage_limit) : 'Unlimited' }}</strong><span class="vhr-sub">{{ number_format($row->period_used) }} use{{ $row->period_used === 1 ? '' : 's' }} in period</span>@if($row->usage_limit)<div class="vhr-progress"><span style="width:{{ $usagePercent }}%"></span></div>@endif</div></td>
                            <td class="vhr-money">₱{{ number_format($row->rebate_total, 2) }}</td>
                            <td class="vhr-money">₱{{ number_format($row->order_total, 2) }}</td>
                            <td><strong>{{ number_format($row->event_count) }}</strong><span class="vhr-sub">timeline events</span></td>
                            <td><button type="button" class="btn btn-sm btn-outline-primary vhr-history-btn" data-voucher-history="{{ $row->id }}"><i class="ti ti-history"></i>View history</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="vhr-empty"><i class="ti ti-ticket-off"></i><strong>No voucher history found</strong><span>Adjust the filters or reporting period.</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="modal fade vhr-modal" id="voucherHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="vhr-modal-head"><div class="d-flex justify-content-between gap-3"><div><div class="vhr-eyebrow">Voucher Timeline</div><h5 class="mb-0 fw-bold" id="vhrModalTitle">Voucher History</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="vhr-modal-meta" id="vhrModalMeta"></div></div>
            <div class="modal-body p-4"><div class="vhr-timeline" id="vhrTimeline"></div></div>
        </div>
    </div>
</div>

<script type="application/json" id="voucherHistoryData">@json($histories)</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = Array.from(document.querySelectorAll('[data-vhr-row]'));
    const search = document.getElementById('vhrSearch');
    const visible = document.getElementById('vhrVisible');
    const historyData = JSON.parse(document.getElementById('voucherHistoryData').textContent || '{}');
    const modalElement = document.getElementById('voucherHistoryModal');
    const modal = modalElement && window.bootstrap ? new bootstrap.Modal(modalElement) : null;
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));

    if (search) search.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        let count = 0;
        rows.forEach(row => { const show = !query || row.dataset.search.includes(query); row.classList.toggle('vhr-row-hidden', !show); if (show) count++; });
        if (visible) visible.textContent = count + ' visible row' + (count === 1 ? '' : 's');
    });

    document.querySelectorAll('[data-voucher-history]').forEach(button => button.addEventListener('click', function () {
        const data = historyData[this.dataset.voucherHistory];
        if (!data || !modal) return;
        document.getElementById('vhrModalTitle').textContent = data.code + ' History';
        document.getElementById('vhrModalMeta').innerHTML = [data.distributor, data.discount, data.status].map(value => '<span>' + escapeHtml(value) + '</span>').join('');
        document.getElementById('vhrTimeline').innerHTML = data.timeline.length ? data.timeline.map(event => {
            const changes = (event.changes || []).length ? '<div class="vhr-change">' + event.changes.map(change => '<div class="vhr-change-row"><strong>' + escapeHtml(change.field) + '</strong><span>' + escapeHtml(change.old) + '</span><span>' + escapeHtml(change.new) + '</span></div>').join('') + '</div>' : '';
            const link = event.url ? '<a class="btn btn-sm btn-outline-primary mt-2" href="' + escapeHtml(event.url) + '">Open order</a>' : '';
            return '<article class="vhr-event ' + (event.type === 'usage' ? 'is-usage' : '') + '"><div class="d-flex justify-content-between gap-2"><h6>' + escapeHtml(event.title) + '</h6><span class="vhr-event-time">' + escapeHtml(event.date) + '</span></div><p>' + escapeHtml(event.description) + '</p><span class="vhr-sub">By ' + escapeHtml(event.actor || 'System') + '</span>' + changes + link + '</article>';
        }).join('') : '<div class="text-center text-muted py-5">No events in this reporting period.</div>';
        modal.show();
    }));
});
</script>
@endsection
