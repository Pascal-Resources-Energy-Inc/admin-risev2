@extends('layouts.header')

@section('content')
@php
    $pendingCount = $requests->where('status', 'Pending')->count();
    $approvedCount = $requests->where('status', 'Approved')->count();
    $rejectedCount = $requests->where('status', 'Rejected')->count();
@endphp

<div class="stock-approval-page">

    <header class="page-heading stock-head">
        <a class="back-link" href="{{ url('account') }}" aria-label="Back to account"><i class="bi bi-arrow-left"></i></a>
        <div class="stock-head-copy">
            <p class="eyebrow">Inventory management</p>
            <h1>Stock request approvals</h1>
            <p class="heading-copy">Review dealer inventory requests and keep stock moving with confidence.</p>
        </div>
        <span class="stock-head-date"><i class="bi bi-calendar3"></i>{{ now()->format('M d, Y') }}</span>
    </header>

    @if(session('success'))
        <div class="alert-message alert-success" role="alert"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span><button type="button" aria-label="Dismiss message"><i class="bi bi-x-lg"></i></button></div>
    @endif
    @if($errors->any())
        <div class="alert-message alert-error" role="alert"><i class="bi bi-exclamation-circle-fill"></i><span>{{ $errors->first() }}</span><button type="button" aria-label="Dismiss message"><i class="bi bi-x-lg"></i></button></div>
    @endif

    <section class="summary-grid" aria-label="Request summary">
        <div class="summary-card pending"><span class="summary-icon"><i class="bi bi-hourglass-split"></i></span><div><span>Awaiting review</span><strong>{{ $pendingCount }}</strong><small>requests need action</small></div></div>
        <div class="summary-card approved"><span class="summary-icon"><i class="bi bi-check2-circle"></i></span><div><span>Approved</span><strong>{{ $approvedCount }}</strong><small>all completed requests</small></div></div>
        <div class="summary-card rejected"><span class="summary-icon"><i class="bi bi-x-circle"></i></span><div><span>Rejected</span><strong>{{ $rejectedCount }}</strong><small>all completed requests</small></div></div>
    </section>

    <section class="requests-panel" aria-labelledby="requests-title">
        <div class="panel-header">
            <div><h2 id="requests-title">Dealer requests</h2><p>Newest requests are shown first.</p></div>
            <span class="pending-badge"><span></span>{{ $pendingCount }} pending</span>
        </div>
        <div class="request-tools">
            <label class="search-box"><i class="bi bi-search"></i><input id="requestSearch" type="search" placeholder="Search dealer or product" autocomplete="off" aria-label="Search requests"></label>
            <div class="filter-tabs" role="tablist" aria-label="Filter requests">
                <button type="button" class="filter-tab active" data-filter="all" role="tab" aria-selected="true">All <span>{{ $requests->count() }}</span></button>
                <button type="button" class="filter-tab" data-filter="pending" role="tab" aria-selected="false">Pending <span>{{ $pendingCount }}</span></button>
                <button type="button" class="filter-tab" data-filter="approved" role="tab" aria-selected="false">Approved <span>{{ $approvedCount }}</span></button>
                <button type="button" class="filter-tab" data-filter="rejected" role="tab" aria-selected="false">Rejected <span>{{ $rejectedCount }}</span></button>
            </div>
        </div>
        <p class="request-results" id="requestResults" aria-live="polite">Showing {{ $requests->count() }} request{{ $requests->count() === 1 ? '' : 's' }}</p>

        <div class="request-list" id="requestList">
            @forelse($requests as $request)
                @php
                    $dealerName = optional($request->dealer)->name ?? 'Dealer #' . $request->dealer_id;
                    $storeName = optional(optional($request->dealer)->dealer)->store_name ?: 'Dealer account';
                    $productName = optional($products->get($request->product_id))->product_name ?? 'Product #' . $request->product_id;
                    $status = strtolower($request->status);
                @endphp
                <article class="request-item" data-status="{{ $status }}" data-search="{{ strtolower($dealerName . ' ' . $storeName . ' ' . $productName) }}">
                    <div class="dealer-avatar" aria-hidden="true">{{ strtoupper(substr($dealerName, 0, 1)) }}</div>
                    <div class="request-details">
                        <div class="request-primary"><div><h3>{{ $dealerName }}</h3><p class="store-name"><i class="bi bi-shop"></i>{{ $storeName }}</p></div><span class="status-pill {{ $status }}"><i class="bi {{ $status === 'pending' ? 'bi-clock' : ($status === 'approved' ? 'bi-check-lg' : 'bi-x-lg') }}"></i>{{ $request->status }}</span></div>
                        <div class="request-meta"><span><i class="bi bi-box-seam"></i>{{ $productName }}</span><span><i class="bi bi-layers"></i><b>{{ number_format($request->quantity) }}</b> units requested</span><span><i class="bi bi-calendar3"></i>{{ optional($request->created_at)->format('M d, Y · g:i A') }}</span></div>
                        @if($request->remarks)<p class="request-note"><i class="bi bi-chat-left-text"></i><span><b>{{ $request->status === 'Rejected' ? 'Rejection reason:' : 'Note:' }}</b> {{ $request->remarks }}</span></p>@endif
                        @if($request->status !== 'Pending' && $request->reviewer)<p class="reviewed-by">Reviewed by {{ $request->reviewer->name }}{{ $request->reviewed_at ? ' on ' . $request->reviewed_at->format('M d, Y') : '' }}</p>@endif
                    </div>
                    @if($request->status === 'Pending')
                        <div class="request-actions">
                            <form method="POST" action="{{ route('admin.stock.requests.approve', ['id' => $request->id]) }}" class="approve-form">@csrf<button class="button button-approve" type="submit"><i class="bi bi-check-lg"></i>Approve</button></form>
                            <button class="button button-reject" type="button" data-reject-id="{{ $request->id }}" data-dealer="{{ $dealerName }}" data-product="{{ $productName }}"><i class="bi bi-x-lg"></i>Reject</button>
                        </div>
                    @endif
                </article>
            @empty
                <div class="empty-state"><span><i class="bi bi-inboxes"></i></span><h3>No dealer stock requests yet</h3><p>Requests from dealers will appear here when submitted.</p></div>
            @endforelse
            <div id="noResults" class="empty-state compact" hidden><span><i class="bi bi-search"></i></span><h3>No matching requests</h3><p>Try a different search or filter.</p></div>
        </div>
    </section>


<div class="dialog-backdrop" id="rejectModal" hidden aria-hidden="true">
    <form id="rejectForm" method="POST" class="decision-dialog" aria-labelledby="rejectTitle">@csrf
        <button type="button" class="dialog-close" data-close-dialog aria-label="Close"><i class="bi bi-x-lg"></i></button>
        <span class="dialog-icon reject-icon"><i class="bi bi-x-lg"></i></span>
        <h2 id="rejectTitle">Reject stock request?</h2>
        <p id="rejectDescription">Tell the dealer why this request cannot be approved.</p>
        <label for="rejectionRemarks">Reason for rejection <span>*</span></label>
        <textarea id="rejectionRemarks" name="remarks" maxlength="500" required placeholder="Enter a clear reason for the dealer..."></textarea>
        <div class="character-count"><span id="characterCount">0</span>/500</div>
        <div class="dialog-actions"><button type="button" class="button button-cancel" data-close-dialog>Cancel</button><button class="button button-confirm-reject" type="submit"><i class="bi bi-x-lg"></i>Reject request</button></div>
    </form>
</div>
</div>
@endsection

@section('css')
<style>
    /* Match the shared dashboard cards, type scale, and aqua accent used elsewhere. */
    .stock-approval-page { display: grid; gap: 16px; }
    .stock-head { align-items: center; margin-bottom: 0; }
    .stock-head-copy { flex: 1; min-width: 0; }
    .stock-head-date { align-items: center; align-self: center; background: #e2f7ff; border: 1px solid #c5edf9; border-radius: 8px; color: #167a9f; display: inline-flex; font-size: 11px; font-weight: 800; gap: 6px; padding: 9px 11px; white-space: nowrap; }
    .summary-grid { margin-bottom: 0; }
    .summary-card { border-radius: 10px; overflow: hidden; position: relative; }
    .summary-card::before { background: #5BC2E7; content: ""; inset: 0 auto 0 0; position: absolute; width: 4px; }
    .summary-card.approved::before { background: #16a34a; }
    .summary-card.rejected::before { background: #dc2626; }
    .summary-card.pending .summary-icon { background: #e2f7ff; color: #1688b3; }
    .requests-panel { border-radius: 12px; }
    .panel-header { background: linear-gradient(135deg, #fff 0%, #f8fafc 100%); border-bottom: 1px solid #edf0f5; }
    .panel-header h2::before { color: #5BC2E7; content: "\F32B"; font-family: "bootstrap-icons"; font-size: 16px; margin-right: 8px; }
    .pending-badge { background: #e2f7ff; color: #167a9f; }
    .pending-badge span { background: #5BC2E7; }
    .filter-tab.active { background: #e2f7ff; color: #167a9f; }
    .filter-tab.active span { color: #167a9f; }
    .request-item:hover { background: #f6fcff; }
    .request-results { background: #fff; }
body.main-layout{background:#f4f7fb!important}.page-heading{align-items:flex-start;display:flex;gap:16px;margin-bottom:26px}.back-link{background:#fff;border:1px solid #dce7f0;border-radius:12px;box-shadow:0 3px 8px rgba(26,52,75,.04);color:#316a8e;display:grid;flex:0 0 44px;font-size:19px;height:44px;margin-top:3px;place-items:center;text-decoration:none;transition:.2s;width:44px}.back-link:hover{background:#eaf5fb;color:#1479ae;transform:translateX(-2px)}.eyebrow{color:#1688b3;font-size:11px;font-weight:800;letter-spacing:.1em;margin:0 0 5px;text-transform:uppercase}.page-heading h1{color:#102a43;font-size:28px;font-weight:800;letter-spacing:-.035em;margin:0}.heading-copy{color:#6c7a89;font-size:14px;margin:7px 0 0}.alert-message{align-items:center;border:1px solid;border-radius:12px;display:flex;font-size:13px;font-weight:600;gap:10px;margin:0 0 18px;padding:13px 14px}.alert-message span{flex:1}.alert-message button{background:transparent;border:0;color:inherit;cursor:pointer}.alert-success{background:#effbf4;border-color:#c8efd8;color:#15734a}.alert-error{background:#fff4f3;border-color:#ffd7d3;color:#b42318}.summary-grid{display:grid;gap:16px;grid-template-columns:repeat(3,1fr);margin-bottom:22px}.summary-card{align-items:center;background:#fff;border:1px solid #e3eaf2;border-radius:15px;box-shadow:0 7px 20px rgba(22,43,66,.04);display:flex;gap:13px;min-height:104px;padding:17px}.summary-icon{border-radius:12px;display:grid;font-size:20px;height:44px;place-items:center;width:44px}.summary-card>div{display:grid;line-height:1.15}.summary-card>div>span{color:#728096;font-size:12px;font-weight:600}.summary-card strong{color:#172b4d;font-size:25px;font-weight:800;margin:4px 0 3px}.summary-card small{color:#98a4b3;font-size:11px}.summary-card.pending .summary-icon{background:#fff4dd;color:#c77912}.summary-card.approved .summary-icon{background:#e9f9ef;color:#198754}.summary-card.rejected .summary-icon{background:#fff0ef;color:#d44236}.requests-panel{background:#fff;border:1px solid #dfe8f1;border-radius:16px;box-shadow:0 12px 30px rgba(25,57,85,.055);overflow:hidden}.panel-header{align-items:center;display:flex;justify-content:space-between;padding:21px 22px 16px}.panel-header h2{color:#172b4d;font-size:18px;font-weight:800;margin:0}.panel-header p{color:#7b8795;font-size:12px;margin:5px 0 0}.pending-badge{align-items:center;background:#fff6e6;border-radius:99px;color:#a45d00;display:inline-flex;font-size:12px;font-weight:800;gap:7px;padding:7px 10px}.pending-badge span{background:#e9951a;border-radius:99px;height:7px;width:7px}.request-tools{border-bottom:1px solid #eaf0f5;display:flex;gap:14px;justify-content:space-between;padding:0 22px 17px}.search-box{align-items:center;background:#fafcfe;border:1px solid #dce5ee;border-radius:9px;color:#8795a4;display:flex;gap:9px;min-width:235px;padding:0 11px}.search-box:focus-within{border-color:#58a6cb;box-shadow:0 0 0 3px #e4f5fc}.search-box input{background:transparent;border:0;color:#25364b;font-size:13px;height:38px;outline:0;width:100%}.filter-tabs{align-items:center;display:flex;gap:4px;overflow-x:auto}.filter-tab{background:transparent;border:0;border-radius:7px;color:#69788a;cursor:pointer;font-size:12px;font-weight:700;padding:8px 9px;white-space:nowrap}.filter-tab span{color:#98a5b2;font-size:11px;margin-left:3px}.filter-tab:hover{background:#f2f7fa}.filter-tab.active{background:#e5f4fa;color:#157aa8}.filter-tab.active span{color:#157aa8}.request-item{align-items:start;border-bottom:1px solid #edf2f6;display:grid;gap:13px;grid-template-columns:43px minmax(0,1fr) auto;padding:19px 22px;transition:background .2s}.request-item:hover{background:#fbfdff}.request-item:last-child{border-bottom:0}.dealer-avatar{background:linear-gradient(135deg,#dff3fb,#c8e6f3);border-radius:50%;color:#176d96;display:grid;font-size:14px;font-weight:800;height:40px;place-items:center;width:40px}.request-primary{align-items:flex-start;display:flex;gap:15px;justify-content:space-between}.request-primary h3{color:#162d4a;font-size:14px;font-weight:800;margin:0}.store-name{align-items:center;color:#738194;display:flex;font-size:12px;gap:5px;margin:4px 0 0}.store-name i,.request-meta i{color:#56a1c4}.status-pill{align-items:center;border-radius:99px;display:inline-flex;flex:0 0 auto;font-size:10px;font-weight:800;gap:5px;padding:5px 8px}.status-pill.pending{background:#fff5e5;color:#ad6505}.status-pill.approved{background:#eaf9f0;color:#168450}.status-pill.rejected{background:#fff0ef;color:#c9372c}.request-meta{color:#5e6e80;display:flex;flex-wrap:wrap;font-size:12px;gap:7px 17px;margin-top:12px}.request-meta span{align-items:center;display:inline-flex;gap:6px}.request-meta b{color:#273d57}.request-note{background:#fffaf2;border-left:3px solid #e7ae56;border-radius:0 6px 6px 0;color:#715d42;display:flex;font-size:12px;gap:7px;line-height:1.5;margin:10px 0 0;padding:8px 10px}.reviewed-by{color:#8b98a5;font-size:11px;margin:10px 0 0}.request-actions{align-items:center;display:flex;gap:7px;padding-top:2px}.approve-form{margin:0}.button{align-items:center;border:1px solid transparent;border-radius:8px;cursor:pointer;display:inline-flex;font-size:12px;font-weight:800;gap:6px;justify-content:center;line-height:1;padding:9px 11px;transition:.2s}.button-approve{background:#16834a;box-shadow:0 3px 8px rgba(22,131,74,.18);color:#fff}.button-approve:hover{background:#106d3d;transform:translateY(-1px)}.button-reject{background:#fff;border-color:#f6d7d3;color:#bb342b}.button-reject:hover{background:#fff2f0}.empty-state{padding:56px 20px;text-align:center}.empty-state>span{background:#edf6fa;border-radius:50%;color:#5795b4;display:grid;font-size:22px;height:52px;margin:0 auto 12px;place-items:center;width:52px}.empty-state h3{color:#344b63;font-size:15px;font-weight:800;margin:0}.empty-state p{color:#8491a0;font-size:12px;margin:7px 0 0}.empty-state.compact{border-top:1px solid #edf2f6}.dialog-backdrop{align-items:center;background:rgba(13,31,48,.48);backdrop-filter:blur(3px);display:grid;inset:0;padding:20px;place-items:center;position:fixed;z-index:2000}.decision-dialog{background:#fff;border-radius:17px;box-shadow:0 24px 70px rgba(0,0,0,.25);padding:27px;position:relative;width:min(100%,440px)}.dialog-close{background:transparent;border:0;border-radius:6px;color:#8391a0;cursor:pointer;padding:6px;position:absolute;right:14px;top:14px}.dialog-icon{border-radius:12px;display:grid;font-size:19px;height:42px;margin-bottom:14px;place-items:center;width:42px}.reject-icon{background:#fff0ef;color:#d33c32}.decision-dialog h2{color:#1d334b;font-size:19px;font-weight:800;margin:0}.decision-dialog>p{color:#728093;font-size:13px;line-height:1.5;margin:7px 0 19px}.decision-dialog label{color:#344b63;display:block;font-size:12px;font-weight:800;margin-bottom:7px}.decision-dialog label span{color:#d33c32}.decision-dialog textarea{border:1px solid #d9e4ed;border-radius:9px;color:#273b52;display:block;font:inherit;font-size:13px;min-height:102px;outline:none;padding:10px;resize:vertical;width:100%}.decision-dialog textarea:focus{border-color:#60a9cd;box-shadow:0 0 0 3px #e6f5fb}.character-count{color:#98a3af;font-size:11px;margin:6px 0 18px;text-align:right}.dialog-actions{display:flex;gap:8px;justify-content:flex-end}.button-cancel{background:#fff;border-color:#dce5ed;color:#556779}.button-confirm-reject{background:#c7362b;color:#fff}.button:disabled{cursor:wait;opacity:.68;transform:none}@media(max-width:760px){.summary-grid{gap:10px}.summary-card{gap:9px;min-height:91px;padding:12px}.summary-icon{border-radius:10px;font-size:16px;height:35px;width:35px}.summary-card strong{font-size:21px}.summary-card small{display:none}.request-tools{display:block}.search-box{margin-bottom:10px;width:100%}.filter-tabs{padding-bottom:2px}.request-item{grid-template-columns:39px minmax(0,1fr);padding:16px}.request-actions{grid-column:2;justify-content:flex-start;padding-top:0}.request-primary{display:block}.status-pill{margin-top:9px}.request-meta{gap:7px 12px}.panel-header{padding:18px 16px 14px}.request-tools{padding:0 16px 14px}}@media(max-width:460px){.page-heading h1{font-size:23px}.pending-badge{display:none}.request-meta{display:grid;gap:7px}.dialog-actions{display:grid;grid-template-columns:1fr 1fr}.dialog-actions .button{min-height:39px}}
</style>
<style>
    .request-results { color: #7b8795; font-size: 11px; font-weight: 700; margin: 0; padding: 10px 22px; border-bottom: 1px solid #edf2f6; }
    .request-item:focus-within { background: #f4fbff; box-shadow: inset 3px 0 0 #2c9dcc; }
    .filter-tabs::-webkit-scrollbar { height: 5px; }
    .filter-tabs::-webkit-scrollbar-thumb { background: #cad8e4; border-radius: 99px; }

    @media (min-width: 761px) and (max-width: 1024px) {
        .stock-approval-page { padding: 28px 24px 42px; }
        .request-item { grid-template-columns: 43px minmax(0, 1fr); }
        .request-actions { grid-column: 2; justify-content: flex-start; padding-top: 0; }
    }

    @media (max-width: 760px) {
        .stock-approval-page { padding: 24px 14px 36px; }
        .page-heading { margin-bottom: 20px; }
        .heading-copy { max-width: 34rem; }
        .request-results { padding: 9px 16px; }
        .stock-head-date { display: none; }
    }

    @media (max-width: 520px) {
        .page-heading { gap: 11px; }
        .back-link { flex-basis: 40px; height: 40px; width: 40px; }
        .summary-grid { grid-template-columns: 1fr; }
        .summary-card { min-height: 74px; }
        .request-item { grid-template-columns: 36px minmax(0, 1fr); gap: 10px; padding: 15px 14px; }
        .dealer-avatar { height: 36px; width: 36px; }
        .request-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; width: 100%; }
        .approve-form, .approve-form .button, .request-actions > .button { width: 100%; }
        .button { min-height: 42px; padding: 10px; }
        .request-note { font-size: 11px; }
        .decision-dialog { max-height: calc(100vh - 24px); overflow-y: auto; padding: 22px 18px; }
    }

    @media (max-width: 360px) {
        .stock-approval-page { padding-left: 10px; padding-right: 10px; }
        .page-heading h1 { font-size: 21px; }
        .summary-card small { display: none; }
        .request-actions { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var list = document.getElementById('requestList'), search = document.getElementById('requestSearch'), noResults = document.getElementById('noResults'), results = document.getElementById('requestResults');
    var activeFilter = 'all', modal = document.getElementById('rejectModal'), form = document.getElementById('rejectForm'), textarea = document.getElementById('rejectionRemarks');
    function filterRequests() { var term = search.value.toLowerCase().trim(), visible = 0; list.querySelectorAll('.request-item').forEach(function (item) { var show = (activeFilter === 'all' || item.dataset.status === activeFilter) && item.dataset.search.indexOf(term) !== -1; item.hidden = !show; if (show) visible++; }); noResults.hidden = visible > 0 || !list.querySelector('.request-item'); if (results) results.textContent = 'Showing ' + visible + ' request' + (visible === 1 ? '' : 's') + (activeFilter === 'all' ? '' : ' · ' + activeFilter); }
    search.addEventListener('input', filterRequests);
    document.querySelectorAll('.filter-tab').forEach(function (tab) { tab.addEventListener('click', function () { activeFilter = tab.dataset.filter; document.querySelectorAll('.filter-tab').forEach(function (button) { button.classList.toggle('active', button === tab); button.setAttribute('aria-selected', button === tab ? 'true' : 'false'); }); filterRequests(); }); });
    document.querySelectorAll('[data-reject-id]').forEach(function (button) { button.addEventListener('click', function () { form.action = '{{ url('/stock-requests') }}/' + button.dataset.rejectId + '/reject'; document.getElementById('rejectDescription').textContent = 'Reject the request for ' + button.dataset.product + ' from ' + button.dataset.dealer + '? Tell them why.'; textarea.value = ''; document.getElementById('characterCount').textContent = '0'; modal.hidden = false; modal.setAttribute('aria-hidden', 'false'); textarea.focus(); }); });
    function closeModal() { modal.hidden = true; modal.setAttribute('aria-hidden', 'true'); }
    document.querySelectorAll('[data-close-dialog]').forEach(function (button) { button.addEventListener('click', closeModal); });
    modal.addEventListener('click', function (event) { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !modal.hidden) closeModal(); });
    textarea.addEventListener('input', function () { document.getElementById('characterCount').textContent = textarea.value.length; });
    document.querySelectorAll('.approve-form, #rejectForm').forEach(function (requestForm) { requestForm.addEventListener('submit', function () { var submit = requestForm.querySelector('[type="submit"]'); if (submit) { submit.disabled = true; submit.innerHTML = '<i class="bi bi-arrow-repeat"></i> Processing...'; } }); });
    document.querySelectorAll('.alert-message button').forEach(function (button) { button.addEventListener('click', function () { button.closest('.alert-message').remove(); }); });
});
</script>
@endsection
