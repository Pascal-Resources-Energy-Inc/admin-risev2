@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
    .mega-page{padding:18px 0}.mega-head,.mega-stat,.mega-directory{background:#fff;border:1px solid #e8edf4;border-radius:16px;box-shadow:0 8px 24px rgba(31,41,55,.06)}
    .mega-head{padding:22px 24px;margin-bottom:18px;border-left:5px solid #1698bd}.mega-eyebrow{font-size:11px;font-weight:800;letter-spacing:.12em;color:#168eb5;text-transform:uppercase}.mega-head h1{font-size:28px;font-weight:800;color:#142a43;margin:5px 0}.mega-stat{padding:18px 20px;min-height:102px}.mega-stat strong{display:block;font-size:29px;line-height:1.1;color:#142a43}.mega-stat span{font-size:12px;color:#718198}.mega-directory{overflow:hidden}.mega-directory-head{padding:20px 22px;border-bottom:1px solid #edf1f6}.mega-directory-head h2{font-size:18px;font-weight:750;color:#18314f;margin:0}.mega-table-wrap{padding:0 18px 16px}.mega-table{margin:0!important}.mega-table thead th{background:#f5f8fb;color:#607087;font-size:11px;letter-spacing:.06em;text-transform:uppercase;white-space:nowrap}.mega-table td{vertical-align:middle;font-size:13px}.dealer-ref{display:inline-flex;padding:5px 10px;border-radius:20px;background:#e6f6fc;color:#087ea6;font-weight:800}.dealer-link{color:#173554;text-decoration:none;font-weight:750}.dealer-status{display:inline-flex;padding:5px 10px;border-radius:20px;font-size:11px;font-weight:750}.dealer-status.active{color:#167348;background:#e5f7ef}.dealer-status.inactive{color:#a53b3b;background:#fff0f0}.dataTables_wrapper{padding-top:14px}@media(max-width:767px){.mega-head{padding:18px}.mega-head h1{font-size:23px}.mega-table-wrap{padding:0 10px 12px}}
</style>
@endsection

@section('content')
<main class="mega-page">
    <section class="mega-head d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div><div class="mega-eyebrow">Partner network</div><h1>Mega Dealers</h1><div class="text-muted">Manage Mega Dealer accounts, territory, and business information.</div></div>
        @if(auth()->user()->role === 'Admin')
            <button type="button" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#new_dealer"><i class="ti ti-plus"></i> Add Mega Dealer</button>
        @endif
    </section>

    <div class="row g-3 mb-3">
        @foreach([['Total Mega Dealers', $totalAds], ['Active', $activeAds], ['Inactive', $inactiveAds]] as $stat)
            <div class="col-12 col-sm-6 col-xl-4"><div class="mega-stat"><strong>{{ number_format($stat[1]) }}</strong><span>{{ $stat[0] }}</span></div></div>
        @endforeach
    </div>

    <section class="mega-directory">
        <div class="mega-directory-head d-flex justify-content-between align-items-center flex-wrap gap-2"><div><h2>Mega Dealer directory</h2><small class="text-muted">Search and manage Mega Dealer records.</small></div><span class="badge rounded-pill bg-info-subtle text-info">{{ number_format($totalAds) }} records</span></div>
        <div class="mega-table-wrap table-responsive">
            <table id="megaDealersTable" class="table table-hover mega-table w-100">
                <thead><tr><th>Partner code</th><th>Mega Dealer</th><th>Business</th><th>Contact</th><th>Stock</th><th>Sold</th><th>Sales territory</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </section>
    @include('new_dealer')
</main>
@endsection

@section('javascript')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#megaDealersTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50],
        order: [[1, 'asc']],
        ajax: @json(route('mega-dealers.data')),
        columns: [
            {data: 'partner_code_display', name: 'partner_code'},
            {data: 'name_display', name: 'name'},
            {data: 'store_name', name: 'store_name'},
            {data: 'contact', name: 'number', orderable: false},
            {data: 'stock_display', name: 'stock_qty', orderable: false, searchable: false},
            {data: 'sold_display', name: 'sold_qty', orderable: false, searchable: false},
            {data: 'territory', name: 'area'},
            {data: 'status_display', name: 'status'},
            {data: 'actions', orderable: false, searchable: false}
        ]
    });
});
</script>
@endsection
