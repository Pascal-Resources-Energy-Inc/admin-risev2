@extends('layouts.header')
<style>
    .content-area:has(.welcome-client) {
        margin-top: 90px !important;
    }
    
    .transaction-table th {
        text-align: center;
    }
    .btn-view {
        width: 100px;
        font-size: 14px;
    }
    .dashboard-stats {
        display: flex;
        justify-content: space-around;
    }
    .dashboard-stats div {
        text-align: center;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 30%;
    }
    .welcome {
        margin-top: auto !important;
    }
    .card-header {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .card-body {
        padding: 20px;
    }
    .filter-container {
        margin-bottom: 20px;
    }
    .card {
      border-radius: 15px !important;
    }
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 1rem;
            max-width: 100%;
        }

    }

    .search-name-responsive{
        width: 180px !important;
    }

    @media (max-width: 576px) {
        .search-name-responsive{
            font-size: 12px !important;
            width: 170px !important;
            height: 43px !important; 
        }
    }

.transaction-pagination .pagination {
    justify-content: flex-end;
    margin-bottom: 0;
}

.transactions-page { display: grid; gap: 18px; padding-bottom: 28px; --trx-ink: #172f4a; --trx-muted: #718197; --trx-line: #dfe8f0; --trx-accent: #1592bd; }
.transactions-head { display: flex; align-items: end; justify-content: space-between; gap: 18px; }
.transactions-eyebrow { margin: 0; color: #1688b3; font-size: 11px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
.transactions-eyebrow i { margin-right: 5px; }
.transactions-head h1 { margin: 4px 0; color: #102a43; font-size: 28px; font-weight: 900; letter-spacing: -.04em; }
.transactions-head > div:first-child > p:last-child { margin: 0; color: #718197; font-size: 13px; }
.transactions-head-actions { display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap; }
.transactions-head-actions .btn { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; border-radius: 9px; font-size: 12px; font-weight: 800; }
.transactions-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
.transactions-stats article { min-width: 0; min-height: 92px; display: flex; align-items: center; gap: 12px; padding: 15px; background: #fff; border: 1px solid #e1e9f0; border-radius: 12px; box-shadow: 0 8px 20px rgba(20,54,79,.04); }
.transactions-stats small,.transactions-stats article>div>span { display: block; color: #8997a5; font-size: 11px; }
.transactions-stats strong { display: block; margin: 3px 0; overflow: hidden; color: #162e49; font-size: clamp(18px, 2vw, 24px); font-weight: 900; line-height: 1.15; text-overflow: ellipsis; white-space: nowrap; }
.transactions-stat-icon { width: 42px; height: 42px; display: grid; flex: 0 0 auto; place-items: center; border-radius: 10px; font-size: 19px; }
.transactions-stat-icon.sales { color: #1592bd; background: #e4f7fd; }
.transactions-stat-icon.count { color: #d98500; background: #fff4dc; }
.transactions-stat-icon.quantity { color: #198754; background: #e8f8ee; }
.transactions-stat-icon.points { color: #635bdb; background: #efedff; }
.transactions-page .transaction-register-card { overflow: hidden; border: 1px solid var(--trx-line); border-radius: 18px !important; box-shadow: 0 12px 32px rgba(26, 50, 77, .07); }
.transactions-page .transaction-register-card > .card-body { padding: 0; }
.transactions-page .transaction-register-header { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 20px 22px; border-bottom: 1px solid var(--trx-line); }
.transactions-page .transaction-register-title { margin: 0; color: var(--trx-ink); font-size: 17px; font-weight: 800; }
.transactions-page .transaction-register-subtitle { margin: 4px 0 0; color: var(--trx-muted); font-size: 12px; }
.transactions-page .transaction-register-actions { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
.transactions-page .transaction-register-actions .btn { min-height: 40px; border-radius: 10px; font-size: 12px; font-weight: 700; }
.transactions-page .transaction-controls { display: grid; grid-template-columns: minmax(220px, 1fr) auto; align-items: center; gap: 14px; padding: 14px 22px; background: #fbfdff; border-bottom: 1px solid var(--trx-line); }
.transactions-page .transaction-search-slot { min-width: 0; }
.transactions-page .transaction-search-slot .dataTables_filter { float: none; margin: 0; text-align: left; }
.transactions-page .transaction-search-slot .dataTables_filter label { display: block; margin: 0; color: var(--trx-muted); font-size: 11px; }
.transactions-page .transaction-search-slot .dataTables_filter input { width: min(100%, 360px); height: 40px; margin: 0 0 0 8px; padding: 8px 12px; border: 1px solid #d9e2ec; border-radius: 10px; outline: none; }
.transactions-page .transaction-search-slot .dataTables_filter input:focus { border-color: var(--trx-accent); box-shadow: 0 0 0 3px rgba(21,151,184,.12); }
.transactions-page .transaction-filter-caption { color: var(--trx-muted); font-size: 11px; font-weight: 700; white-space: nowrap; }
.transactions-page .transaction-filter-form { display: grid; grid-template-columns: minmax(160px, 1fr) minmax(180px, 1.2fr) auto auto; align-items: end; gap: 12px; padding: 16px 22px; border-bottom: 1px solid var(--trx-line); }
.transactions-page .transaction-filter-field { min-width: 0; }
.transactions-page .transaction-filter-field label { display: block; margin-bottom: 5px; color: #475467; font-size: 10px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
.transactions-page .transaction-filter-field .form-select { min-height: 40px; border-color: #d9e2ec; border-radius: 10px; color: var(--trx-ink); font-size: 12px; }
.transactions-page .transaction-filter-form .btn { min-height: 40px; min-width: 92px; border-radius: 10px; font-size: 12px; font-weight: 700; }
.transactions-page .transaction-filter-form > div > .btn { width: 100%; }
.transactions-page .transaction-table-wrap { padding: 16px 22px 0; }
.transactions-page .transaction-table { margin: 0; border-color: var(--trx-line); }
.transactions-page .transaction-table thead th { padding: 12px 11px; color: #52637a; background: #f5f8fb; border-color: var(--trx-line); font-size: 10px; font-weight: 800; letter-spacing: .035em; text-transform: uppercase; white-space: nowrap; }
.transactions-page .transaction-table tbody td { padding: 11px; color: #344054; border-color: #edf1f5; font-size: 12px; vertical-align: middle; }
.transactions-page .transaction-table tbody tr:hover td { background: #f3fbfd; }
.transactions-page .transaction-table .transaction-mfi-badge { display: inline-block; padding: 4px 8px; color: #116b8a; background: #e8f7fb; border-radius: 999px; font-size: 10px; font-weight: 800; }
.transactions-page .transaction-pagination { padding: 0 22px 18px; }
.transactions-page .transaction-pagination .pagination { gap: 4px; }
.transactions-page .transaction-pagination .page-link { border-radius: 8px; font-size: 11px; }
@media (max-width: 767.98px) {
    .transactions-head { align-items: stretch; flex-direction: column; }
    .transactions-head h1 { font-size: 24px; }
    .transactions-head-actions { justify-content: flex-start; }
    .transactions-head-actions .btn { flex: 1; }
    .transactions-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .transactions-page .transaction-register-header { align-items: flex-start; flex-direction: column; padding: 17px; }
    .transactions-page .transaction-register-actions { width: 100%; }
    .transactions-page .transaction-controls { grid-template-columns: 1fr; padding: 12px 17px; }
    .transactions-page .transaction-filter-caption { white-space: normal; }
    .transactions-page .transaction-filter-form { grid-template-columns: repeat(2, minmax(0, 1fr)); padding: 14px 17px; }
    .transactions-page .transaction-table-wrap { padding: 12px 17px 0; }
    .transactions-page .transaction-pagination { padding: 0 17px 15px; }
}
@media (max-width: 420px) {
    .transactions-stats { grid-template-columns: 1fr; }
    .transactions-page .transaction-filter-form { grid-template-columns: 1fr; }
    .transactions-page .transaction-register-actions .btn { flex: 1; }
    .transactions-page .transaction-search-slot .dataTables_filter input { display: block; width: 100%; margin: 6px 0 0; }
}
</style>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<div class="welcome transactions-page @if(auth()->user()->role === 'Dealer') welcome-client @endif">
    <header class="transactions-head">
        <div>
            <p class="transactions-eyebrow"><i class="bi bi-receipt"></i> Sales management</p>
            <h1>Transactions</h1>
            <p>Review sales activity and filter transaction records by MFI and center.</p>
        </div>
        <div class="transactions-head-actions">
            <a href="{{ route('transactions.export', request()->only('mfi', 'center')) }}" class="btn btn-outline-primary"><i class="bi bi-download"></i> Export CSV</a>
            @if(auth()->user()->role == "Admin")
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModalAdmin"><i class="bi bi-plus-lg"></i> Search Name</button>
                <button type="button" class="btn btn-danger btn-sm" id="deleteSelectedBtn" title="Delete Selected" style="display: none;"><i class="bi bi-trash"></i> Delete All</button>
            @else
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#qrScannerModal"><i class="bi bi-qr-code-scan"></i> Scan QR</button>
                <button type="button" class="btn btn-primary search-name-responsive" data-bs-toggle="modal" data-bs-target="#addTransactionModal"><i class="bi bi-plus-lg"></i> Search Name</button>
            @endif
        </div>
    </header>

    <section class="transactions-stats">
        <article><span class="transactions-stat-icon sales"><i class="ti ti-currency-peso"></i></span><div><small>Total sales</small><strong>₱{{ number_format($transactionSummary->total_sales ?? 0, 2) }}</strong><span>revenue in this result</span></div></article>
        <article><span class="transactions-stat-icon count"><i class="ti ti-receipt"></i></span><div><small>Transactions</small><strong>{{ number_format($transactionSummary->transaction_count ?? 0) }}</strong><span>matching records</span></div></article>
        <article><span class="transactions-stat-icon quantity"><i class="ti ti-package"></i></span><div><small>Quantity sold</small><strong>{{ number_format($transactionSummary->total_qty ?? 0) }}</strong><span>items in this result</span></div></article>
        <article><span class="transactions-stat-icon points"><i class="ti ti-award"></i></span><div><small>Points earned</small><strong>{{ number_format($transactionSummary->total_points ?? 0) }}</strong><span>dealer and customer points</span></div></article>
    </section>

    <div class="row g-3">
        
        <div class="col-12">
            <div class="card w-100 transaction-register-card">
                <div class="card-body">
                  <div class="transaction-register-header">
                    <div>
                      <h2 class="transaction-register-title">Transaction register</h2>
                      <p class="transaction-register-subtitle">Review sales, find records, and filter by MFI or center.</p>
                    </div>
                  </div>

                  <div class="transaction-controls">
                    <div class="transaction-search-slot" id="transactionSearchSlot"></div>
                    <div class="transaction-filter-caption">{{ number_format($transactions->total()) }} matching transactions</div>
                  </div>

                  <form method="GET" action="{{ route('transactions') }}" class="transaction-filter-form">
                      <div class="transaction-filter-field">
                          <label for="transactionMfiFilter">MFI</label>
                          <select name="mfi" id="transactionMfiFilter" class="form-select">
                              <option value="">All MFIs</option>
                              @foreach($mfiOptions as $mfi)
                                  <option value="{{ $mfi }}" {{ $selectedMfi === $mfi ? 'selected' : '' }}>{{ $mfi }}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="transaction-filter-field">
                          <label for="transactionCenterFilter">Center</label>
                          <select name="center" id="transactionCenterFilter" class="form-select">
                              <option value="">All centers</option>
                              @foreach($centerOptions as $center)
                                  <option value="{{ $center->center }}" data-mfi="{{ $center->mfi }}" {{ $selectedCenter === $center->center ? 'selected' : '' }}>{{ $center->center }}{{ $center->mfi ? ' — ' . $center->mfi : '' }}</option>
                              @endforeach
                          </select>
                      </div>
                      <div><button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Apply filters</button></div>
                      <div><a href="{{ route('transactions') }}" class="btn btn-outline-secondary">Reset</a></div>
                  </form>

                  <div class="table-responsive transaction-table-wrap">
                      <table class="table table-bordered table-striped transaction-table" id="example" style="width:100%">
                          <thead>
                              <tr>
                                  @if(auth()->user()->role == "Admin" && auth()->user()->can_delete === "on")
                                      <th scope="col" class="no-sort" style="width: 50px; text-align: center;">
                                          <div class="d-flex align-items-center justify-content-center">
                                              <input type="checkbox" id="selectAll" title="Select All" style="cursor: pointer;">
                                          </div>
                                      </th>
                                  @endif
                                  {{-- <th scope="col">ID</th> --}}
                                  <th scope="col">Date</th>
                                  <th scope="col">Quantity</th>
                                  <th scope="col">Amount</th>
                                  <th scope="col">Dealer</th>
                                  <th scope="col">Customer</th>
                                  <th scope="col">MFI</th>
                                  <th scope="col">Center</th>
                                  {{-- <th scope="col">Dealer Points</th>
                                  <th scope="col">Customer Points</th> --}}
                                  <th scope="col">Item</th>
                                  @if(auth()->user()->role == "Admin" && auth()->user()->can_delete === "on")
                                      <th scope="col" class="no-sort" style="width: 80px; text-align: center;">Actions</th>
                                  @endif
                              </tr>
                          </thead>
                          <tbody id="transactionBody">
                              @foreach($transactions as $transaction)
                                  <tr id="transaction-row-{{$transaction->id}}">
                                      @if(auth()->user()->role == "Admin" && auth()->user()->can_delete === "on")
                                          <td style="text-align: center;">
                                              <input type="checkbox" class="checkbox-item" data-id="{{$transaction->id}}" style="cursor: pointer;">
                                          </td>
                                      @endif
                                      {{-- <td>{{$transaction->id}}</td> --}}
                                      <td data-order="{{ strtotime($transaction->date) }}">{{ date('M. d, Y', strtotime($transaction->date)) }}</td>
                                      <td>{{ number_format($transaction->qty, 2) }}</td>
                                      <td>{{ number_format($transaction->qty * $transaction->price, 2) }}</td>
                                      <td>{{ $transaction->dealer->name ?? '' }}</td>
                                      <td>{{ $transaction->customer->name ?? '' }}</td>
                                      <td><span class="transaction-mfi-badge">{{ optional($transaction->adDealer)->mfi ?: '—' }}</span></td>
                                      <td class="text-nowrap">{{ optional($transaction->adDealer)->center ?: '—' }}</td>
                                      {{-- <td><span class='text-success'>{{ $transaction->points_dealer }}</span></td>
                                      <td><span class='text-success'>{{ $transaction->points_client }}</span></td> --}}
                                      <td>{{ strtoupper($transaction->item) }}</td>
                                      @if(auth()->user()->role == "Admin" && auth()->user()->can_delete === "on")
                                          <td style="text-align: center;">
                                              <button type="button" class="btn btn-danger btn-sm delete-single" 
                                                      data-id="{{ $transaction->id }}" 
                                                      title="Delete"
                                                      style="cursor: pointer;">
                                                  <i class="bi bi-trash"></i>
                                              </button>
                                          </td>
                                      @endif
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
                  @if($transactions->hasPages())
                    <div class="transaction-pagination mt-3">
                      {{ $transactions->links() }}
                    </div>
                  @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role == "Admin")
  @include('new_transaction_admin')
@else
  @include('new_transaction')
@endif
@include('qr_scanner')

@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

{{-- <script>
 $(document).ready(function() {
  $('#customerSelect').select2({
    dropdownParent: $('#addTransactionModal') // ✅ replace with your modal's ID
  });
  $('#customerSelect123').select2({
    dropdownParent: $('#addTransactionModalAdmin') // ✅ replace with your modal's ID
  });
  $('#dealer').select2({
    dropdownParent: $('#addTransactionModalAdmin') // ✅ replace with your modal's ID
  });
});
</script> --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mfi = document.getElementById('transactionMfiFilter');
    const center = document.getElementById('transactionCenterFilter');
    if (!mfi || !center) return;

    function syncCenterOptions(clearInvalid) {
        const selectedMfi = mfi.value;
        Array.from(center.options).forEach(function (option) {
            if (!option.value) return;
            option.hidden = Boolean(selectedMfi && option.dataset.mfi !== selectedMfi);
        });
        const selectedOption = center.options[center.selectedIndex];
        if (clearInvalid && selectedOption && selectedOption.hidden) center.value = '';
    }

    mfi.addEventListener('change', function () { syncCenterOptions(true); });
    syncCenterOptions(false);
});
</script>
<script>
$(function() {
    const $table = $('#example');
    if (!$table.length || !$.fn.DataTable) return;

    const defaultOrderColumn = $table.find('thead th').first().hasClass('no-sort') ? 1 : 0;

    $table.DataTable({
        paging: false,
        info: false,
        searching: true,
        ordering: true,
        autoWidth: false,
        deferRender: true,
        order: [[defaultOrderColumn, 'desc']],
        columnDefs: [
            {
                targets: 'no-sort',
                orderable: false,
                searchable: false
            }
        ],
        language: {
            search: 'Search this page:'
        }
    });

    const $searchControl = $('#example_filter');
    if ($searchControl.length && $('#transactionSearchSlot').length) {
        $searchControl.appendTo('#transactionSearchSlot');
        $searchControl.find('input').attr({ placeholder: 'Search visible rows', 'aria-label': 'Search visible transaction rows' });
    }
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    [
      ['#customerSelect', 'Search Customer'],
      ['#customerSelect123', 'Search Customer'],
      ['#dealer', 'Search Dealer']
    ].forEach(function (config) {
      var element = document.querySelector(config[0]);
      if (!element || element.tomselect || !window.TomSelect) return;

      new TomSelect(element, {
        create: false,
        allowEmptyOption: true,
        placeholder: config[1]
      });
    });
  });
</script>
<script>
$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    $(document).on('change', '.checkbox-item', function() {
        updateUI();
    });

    $(document).on('change', '#selectAll', function() {
        const isChecked = $(this).prop('checked');
        $('.checkbox-item').prop('checked', isChecked);
        updateUI();
    });

    $(document).on('click', '#deleteSelectedBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        performBulkDelete();
    });

    $(document).on('click', '.delete-single', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const transactionId = $(this).data('id');
        performSingleDelete(transactionId);
    });

    function updateUI() {
        const $checkboxes = $('.checkbox-item');
        const $checked = $('.checkbox-item:checked');
        const checkedCount = $checked.length;
        const totalCount = $checkboxes.length;
        
        if (checkedCount > 0) {
            $('#deleteSelectedBtn').show();
        } else {
            $('#deleteSelectedBtn').hide();
        }
        
        const $selectAll = $('#selectAll');
        if (checkedCount === totalCount && totalCount > 0) {
            $selectAll.prop('checked', true);
            $selectAll.prop('indeterminate', false);
        } else if (checkedCount > 0) {
            $selectAll.prop('checked', false);
            $selectAll.prop('indeterminate', true);
        } else {
            $selectAll.prop('checked', false);
            $selectAll.prop('indeterminate', false);
        }
    }

    function performSingleDelete(transactionId) {
        if (!transactionId || isNaN(transactionId)) {
            Swal.fire('Error!', 'Invalid transaction ID', 'error');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'This transaction will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const deleteUrl = `{{ url('/transactions') }}/${transactionId}`;

                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.success || 'Transaction deleted successfully', 'success').then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        let message = 'An error occurred while deleting the transaction';
                        
                        if (xhr.status === 404) {
                            message = 'Route not found. Please check your routes configuration.';
                        } else if (xhr.status === 405) {
                            message = 'Method not allowed. Check if the route accepts DELETE method.';
                        } else if (xhr.status === 403) {
                            message = 'You are not authorized to delete this transaction.';
                        } else if (xhr.status === 500) {
                            message = 'Server error. Please check the server logs.';
                        }
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                message = response.error;
                            } else if (response.message) {
                                message = response.message;
                            }
                        } catch (e) {
                            // Use default message
                        }
                        
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    }

    function performBulkDelete() {
        const selectedIds = $('.checkbox-item:checked').map(function() {
            return parseInt($(this).data('id'));
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('No Selection', 'Please select at least one transaction to delete.', 'warning');
            return;
        }

        const invalidIds = selectedIds.filter(id => isNaN(id) || id <= 0);
        if (invalidIds.length > 0) {
            Swal.fire('Error!', 'Some transaction IDs are invalid', 'error');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedIds.length} transaction(s). This cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the selected transactions.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const bulkDeleteUrl = '{{ url("/transactions/bulk-delete") }}';

                $.ajax({
                    url: bulkDeleteUrl,
                    type: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: csrfToken
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.success || 'Transactions deleted successfully', 'success').then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        let message = 'An error occurred while deleting the transactions';
                        
                        if (xhr.status === 404) {
                            message = 'Route not found. Please check your routes configuration.';
                        } else if (xhr.status === 405) {
                            message = 'Method not allowed. Check if the route accepts POST method.';
                        } else if (xhr.status === 403) {
                            message = 'You are not authorized to delete these transactions.';
                        } else if (xhr.status === 500) {
                            message = 'Server error. Please check the server logs.';
                        }
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                message = response.error;
                            } else if (response.message) {
                                message = response.message;
                            }
                        } catch (e) {
                            // Use default message
                        }
                        
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    }

    setTimeout(function() {
        updateUI();
    }, 100);
});
</script>

<script>
 let html5QrcodeScanner = null;
 let html5QrcodeLoader = null;

function loadQrScannerLibrary() {
    if (window.Html5Qrcode) {
        return Promise.resolve();
    }

    if (!html5QrcodeLoader) {
        html5QrcodeLoader = new Promise(function(resolve, reject) {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/html5-qrcode';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    return html5QrcodeLoader;
}

function startScanner() {
    loadQrScannerLibrary().then(function() {
    if (!html5QrcodeScanner) {
        document.getElementById("reader").innerHTML = "";
        html5QrcodeScanner = new Html5Qrcode("reader");
    }

    const config = { fps: 10, qrbox: 250 };

    html5QrcodeScanner.start(
        { facingMode: "environment" }, 
        config,
        qrCodeMessage => {
            document.getElementById('result').innerText = qrCodeMessage;
            fetchUserInfo(qrCodeMessage);
            html5QrcodeScanner.stop();
        },
        errorMessage => {
            // optional: handle scanning errors
        }
    ).catch(err => {
        console.error("Unable to start scanning.", err);
    });
    }).catch(function() {
        alert('Unable to load QR scanner. Please check your connection and try again.');
    });
}

function stopScanner() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
        }).catch(err => {
            console.warn("Failed to stop scanner", err);
            html5QrcodeScanner = null;
        });
    }
}

function fetchUserInfo(userId) {
      fetch(`{{ url('get-user') }}/${userId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('userId').value = data.user.id;
            document.getElementById('userName').value = data.user.name;

            var qrModal = bootstrap.Modal.getInstance(document.getElementById('qrScannerModal'));
            qrModal.hide();
            var transactionModal = new bootstrap.Modal(document.getElementById('addTransactionModaldd'));
            transactionModal.show();
        } else {
            alert("User not found");
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error fetching user info:', error);
        alert("Error fetching user info");
    });
}

document.getElementById('qrScannerModal').addEventListener('shown.bs.modal', startScanner);
document.getElementById('qrScannerModal').addEventListener('hidden.bs.modal', stopScanner);
</script>

@endsection
