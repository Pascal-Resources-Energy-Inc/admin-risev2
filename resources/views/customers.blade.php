@extends('layouts.header')
<link rel="icon" type="image/png" href="{{asset('images/logo_nya.png')}}">
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
  .customers-page { display:grid; gap:18px; padding:4px 0 28px; }
  .customers-head { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; }
  .customers-eyebrow { margin:0; color:#1688b3; font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
  .customers-head h1 { margin:4px 0; color:#102a43; font-size:28px; font-weight:900; letter-spacing:-.04em; }
  .customers-head-copy { margin:0; color:#718197; font-size:13px; }
  .customers-actions { display:flex; flex-wrap:wrap; gap:8px; }
  .customers-actions .btn { min-height:40px; display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:9px; font-size:12px; font-weight:800; }
  .customers-add { border:0; background:#1592bd; color:#fff; box-shadow:0 7px 16px rgba(21,146,189,.2); }
  .customers-add:hover { background:#117da4; color:#fff; }
  .customers-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; }
  .customers-stat { min-height:91px; display:flex; align-items:center; gap:12px; padding:15px; background:#fff; border:1px solid #e1e9f0; border-radius:12px; box-shadow:0 8px 20px rgba(20,54,79,.04); }
  .customers-stat-icon { width:42px; height:42px; flex:0 0 42px; display:grid; place-items:center; border-radius:10px; font-size:20px; }
  .customers-stat-icon.total { background:#e4f7fd; color:#1592bd; }
  .customers-stat-icon.active { background:#e8f8ee; color:#198754; }
  .customers-stat-icon.inactive { background:#fff0ef; color:#d34b45; }
  .customers-stat small,.customers-stat span { color:#8997a5; font-size:11px; }
  .customers-stat strong { display:block; margin:4px 0; color:#162e49; font-size:23px; font-weight:900; line-height:1; }
  .customers-panel { position:relative; overflow:hidden; background:#fff; border:1px solid #dfe8f0; border-radius:13px; box-shadow:0 12px 30px rgba(25,57,85,.055); }
  .customers-panel-head { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:19px 21px 15px; background:linear-gradient(135deg,#fff,#f8fbfc); border-bottom:1px solid #edf1f4; }
  .customers-panel-head h2 { margin:0; color:#172f4a; font-size:17px; font-weight:900; }
  .customers-panel-head p { margin:4px 0 0; color:#8391a0; font-size:12px; }
  .customers-total-badge { padding:7px 10px; border-radius:99px; background:#e4f7fd; color:#18799b; font-size:11px; font-weight:800; white-space:nowrap; }
  .customer-toolbar { display:grid; grid-template-columns:minmax(220px,1fr) minmax(140px,190px) minmax(120px,150px) auto; align-items:end; gap:12px; padding:14px 21px; border-bottom:1px solid #edf1f4; }
  .customer-field { min-width:0; }
  .customer-field label { display:block; margin:0 0 5px; color:#718197; font-size:11px; font-weight:800; }
  .customer-search-wrap { position:relative; }
  .customer-search-wrap i { position:absolute; top:50%; left:12px; transform:translateY(-50%); color:#8795a4; }
  .customer-search-wrap input,.customer-field select { width:100%; height:40px; border:1px solid #dce6ed; border-radius:8px; background:#fff; color:#354c63; font-size:12px; }
  .customer-search-wrap input { padding:0 12px 0 35px; outline:none; }
  .customer-search-wrap input:focus,.customer-field select:focus { border-color:#58a6cb; box-shadow:0 0 0 3px #e4f5fc; outline:0; }
  .customer-field select { padding:0 10px; }
  .customer-filter-actions { display:flex; gap:7px; }
  .customer-filter-actions .btn { min-height:40px; display:inline-flex; align-items:center; justify-content:center; gap:6px; border-radius:8px; font-size:12px; font-weight:800; white-space:nowrap; }
  .customer-table-wrap { overflow-x:auto; padding:0 21px; }
  .customer-table { width:100%!important; min-width:1120px; margin:0!important; }
  .customer-table thead th { padding:12px!important; background:#f8fafc; border-bottom:1px solid #e8eef3!important; color:#718196; font-size:10px; font-weight:900; letter-spacing:.055em; text-transform:uppercase; white-space:nowrap; }
  .customer-table tbody td { padding:13px!important; border-bottom:1px solid #edf2f5; color:#354b61; font-size:12px; vertical-align:middle; }
  .customer-table tbody tr:hover { background:#f7fcfe; }
  .customer-table .dataTables_empty { padding:40px!important; color:#8291a1; text-align:center; }
  .customer-name { color:#203a54; font-weight:800; text-decoration:none; }
  .customer-name:hover { color:#087ca8; text-decoration:none; }
  .customer-reference { color:#18799b; font-weight:800; white-space:nowrap; }
  .customer-status-pill { display:inline-flex; padding:6px 9px; border-radius:99px; font-size:10px; font-weight:900; text-transform:uppercase; white-space:nowrap; }
  .customer-status-pill.active { background:#e8f8ee; color:#16834a; }
  .customer-status-pill.inactive { background:#fff0ef; color:#c43c31; }
  .customers-panel .dataTables_wrapper { padding:0 0 15px; }
  .customers-panel .dataTables_filter,.customers-panel .dataTables_length { display:none; }
  .customers-dt-footer { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:10px; padding-top:13px; border-top:1px solid #edf2f5; }
  .customers-panel .dataTables_info { padding:0; color:#77889a; font-size:11px; }
  .customers-panel .dataTables_paginate { padding:0; }
  .customers-panel .dataTables_processing { position:absolute!important; z-index:3; top:50%!important; left:50%!important; width:auto!important; margin:0!important; padding:13px 20px!important; transform:translate(-50%,-50%); border:1px solid #dce8f0!important; border-radius:10px; background:rgba(255,255,255,.96)!important; box-shadow:0 8px 24px rgba(25,57,85,.09); color:#1688b3!important; font-size:12px!important; font-weight:800; }
  @media(max-width:900px) { .customer-toolbar { grid-template-columns:minmax(200px,1fr) 1fr; } }
  @media(max-width:640px) { .customers-page { gap:14px; }.customers-head { align-items:stretch; flex-direction:column; }.customers-head h1 { font-size:24px; }.customers-actions .btn { flex:1; }.customers-stats { gap:9px; }.customers-stat { min-height:78px; padding:11px; }.customers-stat-icon { width:36px;height:36px;flex-basis:36px; }.customers-stat strong { font-size:20px; }.customers-panel-head { align-items:flex-start; padding:15px 14px; }.customer-toolbar { grid-template-columns:1fr 1fr; padding:13px 14px; }.customer-field.search-field,.customer-filter-actions { grid-column:1/-1; }.customer-filter-actions .btn { flex:1; }.customer-table-wrap { padding:0 14px; }.customers-panel .dataTables_wrapper { padding:0 14px 14px; }.customers-dt-footer { align-items:flex-start; flex-direction:column; }.customers-panel .dataTables_paginate { overflow-x:auto; max-width:100%; white-space:nowrap; } }
  @media(max-width:390px) { .customers-stats { grid-template-columns:1fr; }.customers-total-badge { font-size:10px; } }
</style>
@endsection
@section('content')
<div class="customers-page">
  <header class="customers-head">
    <div>
      <p class="customers-eyebrow"><i class="bi bi-people-fill" aria-hidden="true"></i> Account management</p>
      <h1>Customers</h1>
      <p class="customers-head-copy">Manage customer profiles, contact details, and account status.</p>
    </div>
    <div class="customers-actions">
      <a id="customerExportLink" class="btn btn-outline-primary" href="{{ route('customers.export') }}"><i class="bi bi-download" aria-hidden="true"></i> Export CSV</a>
      <button type="button" class="btn customers-add" data-bs-toggle="modal" data-bs-target="#new_customer"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add customer</button>
    </div>
  </header>

  <section class="customers-stats" aria-label="Customer account summary">
    <article class="customers-stat"><span class="customers-stat-icon total"><i class="bi bi-people-fill" aria-hidden="true"></i></span><div><small>Total customers</small><strong>{{ number_format($activeCustomers + $inactiveCustomers) }}</strong><span>Customer accounts</span></div></article>
    <article class="customers-stat"><span class="customers-stat-icon active"><i class="bi bi-person-check-fill" aria-hidden="true"></i></span><div><small>Active</small><strong>{{ number_format($activeCustomers) }}</strong><span>Currently active accounts</span></div></article>
    <article class="customers-stat"><span class="customers-stat-icon inactive"><i class="bi bi-person-x-fill" aria-hidden="true"></i></span><div><small>Inactive</small><strong>{{ number_format($inactiveCustomers) }}</strong><span>Inactive accounts</span></div></article>
  </section>

  <section class="customers-panel">
    <div class="customers-panel-head">
      <div><h2>Customer directory</h2><p>Search and filter customer records, then open a profile for details.</p></div>
      <span class="customers-total-badge">{{ number_format($activeCustomers + $inactiveCustomers) }} records</span>
    </div>

    <form id="customerFilterForm" class="customer-toolbar">
      <div class="customer-field search-field">
        <label for="customer-search">Search customers</label>
        <div class="customer-search-wrap"><i class="bi bi-search" aria-hidden="true"></i><input id="customer-search" type="search" name="search" value="{{ request('search') }}" placeholder="Name, reference, contact, email, center…" autocomplete="off"></div>
      </div>
      <div class="customer-field">
        <label for="customer-status">Account status</label>
        <select id="customer-status" name="status">
          <option value="">All statuses</option>
          <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
          <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <div class="customer-field">
        <label for="per-page">Rows per page</label>
        <select id="per-page" name="per_page">
          @foreach([10, 15, 25, 50] as $size)
            <option value="{{ $size }}" {{ (int) request('per_page', 15) === $size ? 'selected' : '' }}>{{ $size }} rows</option>
          @endforeach
        </select>
      </div>
      <div class="customer-filter-actions">
        <button class="btn btn-primary" type="submit"><i class="bi bi-funnel" aria-hidden="true"></i> Apply</button>
        <button id="customerResetFilters" class="btn btn-light" type="button">Reset</button>
      </div>
    </form>

    <div class="customer-table-wrap" id="customerTableShell" aria-busy="false">
      <table id="customerTable" class="table customer-table" style="width:100%">
        <thead>
          <tr>
            <th>Customer reference</th>
            <th>Customer name</th>
            <th>Contact number</th>
            <th>Email address</th>
            <th>Serial number</th>
            <th>Address</th>
            <th>Total points</th>
            <th>Center</th>
            <th>SPO</th>
            <th>MFI</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </section>
</div>
@endsection
@include('new_customer')
@section('javascript')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#new_customer').on('shown.bs.modal', function () {
    if (typeof map === 'undefined' || !map) {
      initMap();
    } else {
      setTimeout(function() {
        map.invalidateSize();
      }, 200);
    }
  });
  $('.chosen-select').chosen({
      width: '100%'
    });

  var customerTable = $('#customerTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: Number($('#per-page').val()) || 15,
    lengthMenu: [10, 15, 25, 50],
    dom: 'rt<"customers-dt-footer"ip>',
    search: { search: $('#customer-search').val() || '' },
    order: [[0, 'desc']],
    ajax: {
      url: "{{ route('customers.data') }}",
      data: function (data) {
        data.status = $('#customer-status').val();
      }
    },
    columns: [
      { data: 'reference', name: 'clients.client_reference' },
      { data: 'customer_name', name: 'clients.name' },
      { data: 'number', name: 'clients.number' },
      { data: 'email_address', name: 'clients.email_address' },
      { data: 'serial_number', orderable: false, searchable: false },
      { data: 'address', orderable: false, searchable: false },
      { data: 'points', orderable: false, searchable: false },
      { data: 'center', name: 'clients.center' },
      { data: 'spo', name: 'clients.spo' },
      { data: 'mfi', name: 'clients.mfi' },
      { data: 'status_badge', name: 'clients.status' }
    ],
    language: {
      processing: 'Loading customers…',
      emptyTable: 'No customers found.',
      zeroRecords: 'No customers match your search.'
    }
  });

  $('#customerFilterForm').on('submit', function (event) {
    event.preventDefault();
    customerTable.search($('#customer-search').val()).draw();
  });
  $('#customer-status').on('change', function () { customerTable.ajax.reload(); });
  $('#per-page').on('change', function () { customerTable.page.len(Number(this.value)).draw(); });
  $('#customerExportLink').on('click', function () {
    const url = new URL(this.href, window.location.origin);
    const search = customerTable.search().trim();
    const status = $('#customer-status').val();
    if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
    if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');
    this.href = url.toString();
  });
  $('#customerResetFilters').on('click', function () {
    $('#customer-search').val('');
    $('#customer-status').val('');
    $('#per-page').val('15');
    customerTable.search('').page.len(15).draw();
  });
});
</script>

@endsection
