@extends('layouts.header')

@section('css')
<style>
    .dpo-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 16px; }
    .dpo-title { margin: 0; color: #101828; font-size: 24px; font-weight: 900; }
    .dpo-copy { margin: 4px 0 0; color: #667085; font-size: 13px; }
    .dpo-filter { padding: 14px; border: 1px solid #e6e9ef; border-radius: 8px; background: #fff; box-shadow: 0 10px 24px rgba(15, 23, 42, .04); }
    .dpo-filter-form { display: grid; grid-template-columns: repeat(2, minmax(130px, 1fr)) 150px minmax(220px, 1.5fr) auto auto; gap: 8px; align-items: center; }
    /* .dpo-filter-form .form-control, .dpo-filter-form .form-select, .dpo-filter-form .btn { min-height: 36px; } */
    .dpo-kpis { display: grid; grid-template-columns: repeat(4, minmax(150px, 1fr)); gap: 12px; margin: 16px 0; }
    .dpo-kpi { position: relative; padding: 14px 15px; border: 1px solid #e6e9ef; border-radius: 8px; background: #fff; overflow: hidden; box-shadow: 0 10px 24px rgba(15, 23, 42, .04); }
    .dpo-kpi::before { content: ""; position: absolute; inset: 0 auto 0 0; width: 4px; background: #1d4ed8; }
    .dpo-kpi:nth-child(2)::before { background: #6d28d9; }
    .dpo-kpi:nth-child(3)::before { background: #027a48; }
    .dpo-kpi:nth-child(4)::before { background: #b45309; }
    .dpo-kpi span { display: block; color: #667085; font-size: 11px; font-weight: 900; letter-spacing: .04em; text-transform: uppercase; }
    .dpo-kpi strong { display: block; margin-top: 4px; color: #101828; font-size: 22px; font-weight: 900; line-height: 1.15; overflow-wrap: anywhere; }
    .dpo-panel { border: 1px solid #e6e9ef; border-radius: 8px; background: #fff; overflow: hidden; box-shadow: 0 12px 30px rgba(15, 23, 42, .05); }
    .dpo-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; border-bottom: 1px solid #edf0f5; background: #fcfcfd; }
    .dpo-panel-title { margin: 0; color: #101828; font-size: 15px; font-weight: 900; }
    .dpo-panel-count { color: #667085; font-size: 12px; font-weight: 800; }
    .dpo-table { margin: 0; }
    .dpo-table thead th { padding: 12px 14px; border-bottom: 1px solid #e6e9ef; background: #f8fafc; color: #667085; font-size: 11px; font-weight: 900; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
    .dpo-table tbody td { padding: 13px 14px; border-bottom: 1px solid #f1f3f6; color: #344054; vertical-align: middle; }
    .dpo-table tbody tr:last-child td { border-bottom: 0; }
    .dpo-ref, .dpo-doc { color: #101828; font-weight: 900; white-space: nowrap; }
    .dpo-sub { display: block; margin-top: 2px; color: #667085; font-size: 11px; font-weight: 700; }
    .dpo-status { display: inline-flex; align-items: center; justify-content: center; min-width: 100px; border-radius: 999px; padding: 6px 10px; font-size: 11px; font-weight: 900; white-space: nowrap; }
    .dpo-status.pending { background: #fff7ed; color: #c2410c; }
    .dpo-status.for-delivery, .dpo-status.so-created { background: #eff6ff; color: #1d4ed8; }
    .dpo-status.partial-received, .dpo-status.partial-confirmed { background: #fffbeb; color: #b45309; }
    .dpo-status.completed, .dpo-status.confirmed { background: #ecfdf3; color: #027a48; }
    .dpo-status.cancelled { background: #fef2f2; color: #b42318; }
    .dpo-empty { padding: 42px 18px; color: #667085; text-align: center; }
    .dpo-item-list { display: grid; gap: 10px; }
    .dpo-item-card { border: 1px solid #e6e9ef; border-radius: 8px; background: #fff; overflow: hidden; }
    .dpo-item-head { display: grid; grid-template-columns: minmax(0, 1fr) repeat(4, minmax(90px, auto)); gap: 10px; align-items: center; padding: 12px; background: #fcfcfd; border-bottom: 1px solid #edf0f5; }
    .dpo-item-name { color: #101828; font-size: 13px; font-weight: 900; overflow-wrap: anywhere; }
    .dpo-item-meta { color: #667085; font-size: 11px; font-weight: 700; }
    .dpo-item-metric { text-align: center; }
    .dpo-item-metric span { display: block; color: #667085; font-size: 10px; font-weight: 900; letter-spacing: .04em; text-transform: uppercase; }
    .dpo-item-metric strong { display: block; margin-top: 2px; color: #101828; font-size: 13px; font-weight: 900; }
    .dpo-receipt-table { margin: 0; font-size: 12px; }
    .dpo-receipt-table th { color: #667085; font-size: 10px; font-weight: 900; letter-spacing: .04em; text-transform: uppercase; }
    .dpo-history-empty { padding: 22px; color: #667085; text-align: center; }
    @media (max-width: 992px) {
        .dpo-head { align-items: flex-start; flex-direction: column; }
        .dpo-filter-form { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dpo-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dpo-table { min-width: 980px; }
        .dpo-item-head { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 576px) {
        .dpo-filter-form, .dpo-kpis { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <script type="application/json" id="dpoItemHistoriesJson">
        @json($itemHistories)
    </script>
    <div class="dpo-head">
        <div>
            <h4 class="dpo-title">Distributor Purchase Order Report</h4>
            <p class="dpo-copy">Complete DPO document register with SO, DR, SI, status, and partial DR breakdowns.</p>
        </div>
    </div>

    <div class="dpo-filter">
        <form method="GET" action="{{ route('dpo') }}" class="dpo-filter-form">
            <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}" aria-label="Date from">
            <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}" aria-label="Date to">
            <select name="status" class="form-select form-select-sm" aria-label="Status">
                <option value="">All Status</option>
                @foreach($statusOptions as $status)
                    <option value="{{ $status }}" @if(request('status') === $status) selected @endif>{{ $status }}</option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Search DPO, SO, DR, SI, business">
            <button class="btn btn-sm btn-primary" type="submit">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route('dpo') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </form>
    </div>

    <div class="dpo-kpis">
        <div class="dpo-kpi">
            <span>DPO Count</span>
            <strong>{{ number_format($summary['adpo_count']) }}</strong>
        </div>
        <div class="dpo-kpi">
            <span>Document Rows</span>
            <strong>{{ number_format($summary['document_count']) }}</strong>
        </div>
        <div class="dpo-kpi">
            <span>Total Qty</span>
            <strong>{{ number_format($summary['total_qty']) }}</strong>
        </div>
        <div class="dpo-kpi">
            <span>Total Amount</span>
            <strong>PHP {{ number_format($summary['total_amount'], 2) }}</strong>
        </div>
    </div>

    <div class="dpo-panel">
        <div class="dpo-panel-head">
            <h6 class="dpo-panel-title">DPO Document Register</h6>
            <div class="dpo-panel-count">{{ number_format($rows->count()) }} rows</div>
        </div>
        <div class="table-responsive">
            <table class="table dpo-table align-middle">
                <thead>
                    <tr>
                        <th>DPO Ref</th>
                        <th>Date</th>
                        <th>SO #</th>
                        <th>DR #</th>
                        <th>SI #</th>
                        <th>Status</th>
                        <th class="text-center">Items</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $statusClass = strtolower(str_replace(' ', '-', $row->status));
                        @endphp
                        <tr>
                            <td>
                                <span class="dpo-ref">{{ $row->adpo_ref ?: 'N/A' }}</span>
                                <span class="dpo-sub">{{ $row->business_name ?: 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="dpo-doc">{{ optional($row->date)->format('M d, Y') ?: 'N/A' }}</span>
                                <span class="dpo-sub">{{ $row->is_partial_receipt ? 'Partial DR receipt' : 'DPO document' }}</span>
                            </td>
                            <td><span class="dpo-doc">{{ $row->so_number ? strtoupper($row->so_number) : 'N/A' }}</span></td>
                            <td>
                                <span class="dpo-doc">{{ $row->dr_number ? strtoupper($row->dr_number) : 'N/A' }}</span>
                                <span class="dpo-sub">{{ $row->product_name ?: 'All products' }} - {{ number_format($row->qty) }} qty</span>
                            </td>
                            <td><span class="dpo-doc">{{ $row->si_number ? strtoupper($row->si_number) : 'N/A' }}</span></td>
                            <td>
                                <span class="dpo-status {{ $statusClass }}">{{ $row->status }}</span>
                                <span class="dpo-sub">{{ $row->is_partial_receipt ? number_format($row->confirmed_qty) . ' confirmed' : 'Order status: ' . $row->order_status }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary js-dpo-items"
                                    data-order-id="{{ $row->order_id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#dpoItemsModal">
                                    <i class="bi bi-list-check"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="dpo-empty">No distributor purchase order records found for the selected filters.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="dpoItemsModal" tabindex="-1" aria-labelledby="dpoItemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="dpoItemsModalLabel">Purchase Order Items</h5>
                        <div class="text-muted small" id="dpoItemsModalMeta">Item history</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="dpo-item-list" id="dpoItemsHistory"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dataEl = document.getElementById('dpoItemHistoriesJson');
        const historyWrap = document.getElementById('dpoItemsHistory');
        const modalTitle = document.getElementById('dpoItemsModalLabel');
        const modalMeta = document.getElementById('dpoItemsModalMeta');
        let histories = {};

        try {
            histories = JSON.parse(dataEl ? dataEl.textContent : '{}') || {};
        } catch (error) {
            histories = {};
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function numberFormat(value, decimals = 0) {
            return Number(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }

        document.querySelectorAll('.js-dpo-items').forEach(function (button) {
            button.addEventListener('click', function () {
                const history = histories[button.dataset.orderId] || {};
                const items = Array.isArray(history.items) ? history.items : [];

                modalTitle.textContent = history.po_number ? 'Purchase Order Items - ' + history.po_number : 'Purchase Order Items';
                modalMeta.textContent = [history.business_name || 'N/A', history.status || 'N/A'].join(' | ');

                if (!items.length) {
                    historyWrap.innerHTML = '<div class="dpo-history-empty">No item history found for this purchase order.</div>';
                    return;
                }

                historyWrap.innerHTML = items.map(function (item) {
                    const receipts = Array.isArray(item.receipts) ? item.receipts : [];
                    const receiptRows = receipts.length
                        ? receipts.map(function (receipt) {
                            return `
                                <tr>
                                    <td>${escapeHtml(receipt.delivery_date || 'N/A')}</td>
                                    <td><strong>${escapeHtml(receipt.dr_number || 'N/A').toUpperCase()}</strong></td>
                                    <td class="text-center">${numberFormat(receipt.received_qty)}</td>
                                    <td class="text-center">${numberFormat(receipt.confirmed_qty)}</td>
                                    <td class="text-center">${numberFormat(receipt.pending_qty)}</td>
                                    <td>${escapeHtml(receipt.status || 'N/A')}</td>
                                </tr>
                            `;
                        }).join('')
                        : '<tr><td colspan="6" class="text-center text-muted py-3">No DR receipt history.</td></tr>';

                    return `
                        <div class="dpo-item-card">
                            <div class="dpo-item-head">
                                <div>
                                    <div class="dpo-item-name">${escapeHtml(item.name || 'Product')}</div>
                                    <div class="dpo-item-meta">${escapeHtml(item.sku || 'No SKU')} | PHP ${numberFormat(item.unit_price, 2)} unit | PHP ${numberFormat(item.line_total, 2)} total</div>
                                </div>
                                <div class="dpo-item-metric"><span>Ordered</span><strong>${numberFormat(item.ordered_qty)}</strong></div>
                                <div class="dpo-item-metric"><span>Received</span><strong>${numberFormat(item.received_qty)}</strong></div>
                                <div class="dpo-item-metric"><span>Confirmed</span><strong>${numberFormat(item.confirmed_qty)}</strong></div>
                                <div class="dpo-item-metric"><span>Pending</span><strong>${numberFormat(item.pending_qty)}</strong></div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm dpo-receipt-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>DR No.</th>
                                            <th class="text-center">Received</th>
                                            <th class="text-center">Confirmed</th>
                                            <th class="text-center">Pending</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>${receiptRows}</tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }).join('');
            });
        });
    });
</script>
@endsection
