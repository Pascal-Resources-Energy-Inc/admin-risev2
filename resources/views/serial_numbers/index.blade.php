@extends('layouts.header')

@section('content')
<div class="serial-page">
    <header class="serial-page__head">
        <div>
            <span class="serial-page__eyebrow"><i class="bi bi-upc-scan"></i> Asset management</span>
            <h1>Serial numbers</h1>
            <p>Register cookstove serials, assign them to clients, and track signed contracts.</p>
        </div>
        <button type="button" class="serial-primary-button" id="openSerialForm"><i class="bi bi-plus-lg"></i>Add serial number</button>
    </header>

    @if(session('success'))
        <div class="serial-flash is-success" role="status"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span><button type="button" aria-label="Dismiss"><i class="bi bi-x-lg"></i></button></div>
    @endif
    @if($errors->any())
        <div class="serial-flash is-error" role="alert"><i class="bi bi-exclamation-circle-fill"></i><span>{{ $errors->first() }}</span><button type="button" aria-label="Dismiss"><i class="bi bi-x-lg"></i></button></div>
    @endif

    <section class="serial-stats" aria-label="Serial number summary">
        <article><span class="serial-stat-icon all"><i class="bi bi-upc"></i></span><div><small>Total serials</small><strong>{{ $serialStats['total'] }}</strong><span>registered cookstoves</span></div></article>
        <article><span class="serial-stat-icon assigned"><i class="bi bi-person-check"></i></span><div><small>Assigned</small><strong>{{ $serialStats['assigned'] }}</strong><span>linked to a client</span></div></article>
        <article><span class="serial-stat-icon available"><i class="bi bi-box-seam"></i></span><div><small>Available</small><strong>{{ $serialStats['available'] }}</strong><span>ready to assign</span></div></article>
        <article><span class="serial-stat-icon signed"><i class="bi bi-file-earmark-check"></i></span><div><small>Contract signed</small><strong>{{ $serialStats['signed'] }}</strong><span>assigned clients</span></div></article>
    </section>

    <section class="serial-panel" aria-labelledby="serialListTitle">
        <div class="serial-panel__head">
            <div><h2 id="serialListTitle"><i class="bi bi-upc-scan serial-panel-title-icon" aria-hidden="true"></i>Serial registry</h2><p>Search, filter, and page through the serial inventory.</p></div>
            <span id="serialResults" aria-live="polite">{{ $serials->total() }} serial{{ $serials->total() === 1 ? '' : 's' }}</span>
        </div>
        <form class="serial-tools" method="GET" action="{{ route('serial-numbers.index') }}">
            <label class="serial-search"><i class="bi bi-search"></i><span class="visually-hidden">Search serials</span><input name="search" type="search" value="{{ $search }}" placeholder="Search serial number or client" autocomplete="off"></label>
            <label class="serial-select"><span>Status</span><select name="status"><option value="all" {{ $status === 'all' ? 'selected' : '' }}>All serials</option><option value="available" {{ $status === 'available' ? 'selected' : '' }}>Available</option><option value="assigned" {{ $status === 'assigned' ? 'selected' : '' }}>Assigned</option><option value="signed" {{ $status === 'signed' ? 'selected' : '' }}>Signed contract</option></select></label>
            <label class="serial-select serial-select--size"><span>Rows</span><select name="per_page"><option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10 rows</option><option value="15" {{ $perPage === 15 ? 'selected' : '' }}>15 rows</option><option value="25" {{ $perPage === 25 ? 'selected' : '' }}>25 rows</option><option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50 rows</option></select></label>
            <button type="submit" class="serial-filter-button"><i class="bi bi-funnel"></i>Apply</button>
            @if($search !== '' || $status !== 'all' || $perPage !== 15)<a href="{{ route('serial-numbers.index') }}" class="serial-reset-button">Reset</a>@endif
        </form>

        <div class="serial-list" id="serialList">
            @if($serials->count())
                <div class="serial-table-head" aria-hidden="true">
                    <span>Serial asset</span>
                    <span>Assigned client</span>
                    <span>Contract</span>
                    <span>Actions</span>
                </div>
            @endif
            @forelse($serials as $serial)
                @php
                    $client = $serial->client;
                    $assigned = (bool) $client;
                    $signed = $assigned && filled($client->signature);
                    $searchText = strtolower($serial->serial_number . ' ' . optional($client)->name . ' ' . optional($client)->client_reference);
                @endphp
                <article class="serial-row" data-state="{{ $assigned ? ($signed ? 'signed' : 'assigned') : 'available' }}" data-search="{{ $searchText }}">
                    <div class="serial-code"><i class="bi bi-upc-scan"></i><strong>{{ $serial->serial_number }}</strong><span>Asset ID #{{ $serial->id }}</span></div>
                    <div class="serial-client">
                        @if($client)
                            <span class="serial-client__avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                            <div><strong>{{ $client->name }}</strong><span>{{ $client->client_reference ?: 'Client account' }}</span></div>
                        @else
                            <span class="serial-client__avatar is-empty"><i class="bi bi-person-plus"></i></span>
                            <div><strong>Unassigned</strong><span>Ready for a client</span></div>
                        @endif
                    </div>
                    <div class="serial-contract">
                        @if(!$client)
                            <span class="serial-chip is-available"><i class="bi bi-box-seam"></i>Available</span>
                        @elseif($signed)
                            <span class="serial-chip is-signed"><i class="bi bi-file-earmark-check"></i>Contract signed</span>
                        @else
                            <span class="serial-chip is-pending"><i class="bi bi-file-earmark-text"></i>Contract pending</span>
                        @endif
                    </div>
                    <div class="serial-actions">
                        @if($client)<a href="{{ route('client.view', $client->id) }}" class="serial-icon-button" title="View client"><i class="bi bi-person"></i><span>View client</span></a>@endif
                        <button type="button" class="serial-icon-button edit-serial" data-id="{{ $serial->id }}" data-number="{{ $serial->serial_number }}" data-client-id="{{ $serial->client_id }}" title="Edit serial"><i class="bi bi-pencil"></i><span>Edit</span></button>
                    </div>
                </article>
            @empty
                <div class="serial-empty"><i class="bi bi-upc-scan"></i><h3>No serial numbers yet</h3><p>Add a cookstove serial number to begin assigning assets.</p><button type="button" class="serial-primary-button open-serial-form"><i class="bi bi-plus-lg"></i>Add serial number</button></div>
            @endforelse
        </div>
        @if($serials->total() > 0)
            <footer class="serial-pagination">
                <span>Showing {{ $serials->firstItem() }}–{{ $serials->lastItem() }} of {{ $serials->total() }}</span>
                @if($serials->hasPages())
                    <nav aria-label="Serial number pages"><ul>
                        <li class="{{ $serials->onFirstPage() ? 'is-disabled' : '' }}"><a href="{{ $serials->previousPageUrl() ?: '#' }}" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a></li>
                        @foreach($serials->getUrlRange(max(1, $serials->currentPage() - 2), min($serials->lastPage(), $serials->currentPage() + 2)) as $page => $url)
                            <li class="{{ $page === $serials->currentPage() ? 'is-active' : '' }}"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endforeach
                        <li class="{{ $serials->hasMorePages() ? '' : 'is-disabled' }}"><a href="{{ $serials->nextPageUrl() ?: '#' }}" aria-label="Next page"><i class="bi bi-chevron-right"></i></a></li>
                    </ul></nav>
                @endif
            </footer>
        @endif
    </section>
</div>

<aside class="serial-drawer" id="serialDrawer" aria-labelledby="serialDrawerTitle" aria-hidden="true">
    <div class="serial-drawer__head"><div><span id="serialDrawerKicker">New asset</span><h2 id="serialDrawerTitle">Add serial number</h2></div><button type="button" id="closeSerialForm" aria-label="Close form"><i class="bi bi-x-lg"></i></button></div>
    <form method="POST" action="{{ route('serial-numbers.store') }}" id="serialForm" class="serial-form">@csrf
        <div id="serialMethod"></div>
        <label for="serialNumber">Serial number <b>*</b></label>
        <input id="serialNumber" name="serial_number" required maxlength="255" placeholder="e.g. GL-2026-00001" value="{{ old('serial_number') }}">
        <small>Each serial number must be unique.</small>
        <label for="serialClient">Assign client <em>Optional</em></label>
        <select id="serialClient" name="client_id"><option value="">Keep unassigned</option>@foreach($clients as $client)<option value="{{ $client->id }}" {{ (string) old('client_id') === (string) $client->id ? 'selected' : '' }}>{{ $client->name }}{{ $client->client_reference ? ' · ' . $client->client_reference : '' }}{{ $client->signature ? ' · Contract signed' : '' }}</option>@endforeach</select>
        <div class="serial-form__notice"><i class="bi bi-info-circle"></i><span>Assigning a serial moves it from any previous client and keeps the customer record in sync.</span></div>
        <div class="serial-form__actions"><button type="button" class="serial-secondary-button" id="cancelSerialForm">Cancel</button><button type="submit" class="serial-primary-button"><i class="bi bi-check-lg"></i><span id="serialSubmitText">Save serial number</span></button></div>
    </form>
</aside>
@endsection

@section('css')
<style>
.serial-page{margin:0 auto;display:grid;gap:18px}.serial-page__head{display:flex;align-items:center;justify-content:space-between;gap:20px}.serial-page__eyebrow{display:inline-flex;align-items:center;gap:7px;color:#1592bd;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.serial-page h1{margin:5px 0 0;color:#102a43;font-size:28px;font-weight:900;letter-spacing:-.04em}.serial-page__head p{margin:6px 0 0;color:#6b7b8c;font-size:13px}.serial-primary-button,.serial-secondary-button{border:0;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;padding:9px 14px;font-size:12px;font-weight:800;cursor:pointer;text-decoration:none}.serial-primary-button{background:#1592bd;color:#fff;box-shadow:0 7px 16px rgba(21,146,189,.2)}.serial-primary-button:hover{background:#087da6;color:#fff}.serial-secondary-button{background:#fff;border:1px solid #dbe5ed;color:#526579}.serial-flash{display:flex;align-items:center;gap:10px;border:1px solid;border-radius:10px;padding:12px 14px;font-size:13px;font-weight:700}.serial-flash span{flex:1}.serial-flash button{border:0;background:transparent;color:inherit}.serial-flash.is-success{background:#effbf4;border-color:#c9efd8;color:#15734a}.serial-flash.is-error{background:#fff4f3;border-color:#ffd7d3;color:#b42318}.serial-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:13px}.serial-stats article{display:flex;align-items:center;gap:12px;min-height:92px;padding:15px;background:#fff;border:1px solid #e1e9f0;border-radius:12px;box-shadow:0 8px 20px rgba(20,54,79,.04)}.serial-stat-icon{display:grid;place-items:center;width:42px;height:42px;border-radius:10px;font-size:19px}.serial-stat-icon.all{background:#e4f7fd;color:#1592bd}.serial-stat-icon.assigned{background:#e8f8ee;color:#198754}.serial-stat-icon.available{background:#fff5df;color:#c67b12}.serial-stat-icon.signed{background:#edf0ff;color:#5b5bd6}.serial-stats small,.serial-stats span{display:block;color:#8997a5;font-size:11px}.serial-stats strong{display:block;margin:3px 0;color:#162e49;font-size:24px;line-height:1;font-weight:900}.serial-panel{overflow:hidden;background:#fff;border:1px solid #dfe8f0;border-radius:13px;box-shadow:0 12px 30px rgba(25,57,85,.055)}.serial-panel__head{display:flex;justify-content:space-between;align-items:center;padding:19px 21px 15px;background:linear-gradient(135deg,#fff,#f8fbfc);border-bottom:1px solid #edf1f4}.serial-panel__head h2{margin:0;color:#172f4a;font-size:17px;font-weight:900}.serial-panel__head p{margin:4px 0 0;color:#8391a0;font-size:12px}.serial-panel__head>span{padding:7px 10px;background:#e4f7fd;border-radius:99px;color:#18799b;font-size:11px;font-weight:800}.serial-tools{display:flex;gap:12px;justify-content:space-between;padding:14px 21px;border-bottom:1px solid #edf1f4}.serial-search{display:flex;align-items:center;gap:8px;min-width:270px;padding:0 11px;color:#8795a4;background:#fafcfe;border:1px solid #dce6ed;border-radius:8px}.serial-search:focus-within{border-color:#54afd0;box-shadow:0 0 0 3px #e2f6fc}.serial-search input{width:100%;height:38px;border:0;outline:0;background:transparent;font-size:13px}.serial-filters{display:flex;gap:4px;overflow-x:auto}.serial-filters button{border:0;border-radius:7px;padding:8px 10px;background:transparent;color:#69798a;font-size:12px;font-weight:800;white-space:nowrap}.serial-filters button:hover{background:#f0f8fb}.serial-filters button.is-active{background:#e4f7fd;color:#147b9f}.serial-row{display:grid;grid-template-columns:minmax(190px,1.1fr) minmax(210px,1fr) 150px auto;gap:16px;align-items:center;padding:16px 21px;border-bottom:1px solid #edf2f5}.serial-row:hover{background:#f8fcfe}.serial-code{display:grid;grid-template-columns:34px 1fr;column-gap:9px;align-items:center}.serial-code i{grid-row:span 2;display:grid;place-items:center;width:34px;height:34px;border-radius:8px;background:#edf8fc;color:#1689b3}.serial-code strong{color:#18314e;font-size:13px;font-weight:900;word-break:break-word}.serial-code span{color:#92a0ad;font-size:10px}.serial-client{display:flex;align-items:center;gap:9px;min-width:0}.serial-client__avatar{display:grid;place-items:center;flex:0 0 34px;width:34px;height:34px;border-radius:50%;background:#dff3fb;color:#176d96;font-size:13px;font-weight:900}.serial-client__avatar.is-empty{background:#f2f5f7;color:#8c99a5}.serial-client strong,.serial-client span{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.serial-client strong{color:#344a60;font-size:12px}.serial-client div>span{margin-top:2px;color:#8b98a5;font-size:10px}.serial-chip{display:inline-flex;align-items:center;gap:5px;padding:6px 8px;border-radius:99px;font-size:10px;font-weight:800;white-space:nowrap}.serial-chip.is-available{background:#fff5e4;color:#a45f05}.serial-chip.is-signed{background:#e9f9ef;color:#168450}.serial-chip.is-pending{background:#fff0ef;color:#bb392e}.serial-actions{display:flex;justify-content:flex-end;gap:6px}.serial-icon-button{display:inline-flex;align-items:center;gap:5px;padding:7px 9px;border:1px solid #dce6ed;border-radius:7px;background:#fff;color:#526d82;font-size:11px;font-weight:800;text-decoration:none}.serial-icon-button:hover{background:#eaf8fc;color:#147b9f;border-color:#bce8f5}.serial-empty{padding:54px 20px;text-align:center}.serial-empty>i{display:grid;place-items:center;width:50px;height:50px;margin:0 auto 12px;border-radius:50%;background:#e9f7fc;color:#4998b8;font-size:22px}.serial-empty h3{margin:0;color:#344d64;font-size:15px;font-weight:900}.serial-empty p{margin:7px 0 16px;color:#8492a0;font-size:12px}.serial-drawer{position:fixed;z-index:1500;top:0;right:0;display:flex;flex-direction:column;width:min(440px,100vw);height:100vh;background:#fff;box-shadow:-18px 0 50px rgba(16,42,67,.22);transform:translateX(102%);transition:transform .22s ease}.serial-drawer.is-open{transform:translateX(0)}.serial-drawer__head{display:flex;justify-content:space-between;align-items:flex-start;padding:22px;background:linear-gradient(130deg,#123c61,#1592bd);color:#fff}.serial-drawer__head span{color:#bae8f8;font-size:10px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.serial-drawer__head h2{margin:4px 0 0;color:#fff;font-size:19px;font-weight:900}.serial-drawer__head button{width:32px;height:32px;border:1px solid rgba(255,255,255,.25);border-radius:7px;background:rgba(255,255,255,.1);color:#fff}.serial-form{display:grid;gap:8px;padding:24px;overflow:auto}.serial-form label{margin-top:8px;color:#344d64;font-size:12px;font-weight:800}.serial-form label b{color:#d3392e}.serial-form label em{float:right;color:#8997a5;font-size:10px;font-style:normal;font-weight:700}.serial-form input,.serial-form select{min-height:42px;padding:9px 10px;border:1px solid #dbe5ed;border-radius:8px;background:#fff;color:#273e55;font-size:13px;outline:0}.serial-form input:focus,.serial-form select:focus{border-color:#52b0d0;box-shadow:0 0 0 3px #e2f6fc}.serial-form small{color:#91a0ad;font-size:10px}.serial-form__notice{display:flex;gap:8px;margin-top:10px;padding:11px;border-radius:8px;background:#f0f9fc;color:#5c7183;font-size:11px;line-height:1.45}.serial-form__notice i{color:#1592bd}.serial-form__actions{display:flex;justify-content:flex-end;gap:8px;margin-top:12px}.serial-form__actions .serial-primary-button{flex:1}@media(max-width:1024px){.serial-stats{grid-template-columns:repeat(2,1fr)}.serial-row{grid-template-columns:minmax(180px,1fr) minmax(190px,1fr) 140px auto}}@media(max-width:760px){.serial-page{padding:24px 14px 36px}.serial-page__head{align-items:stretch;flex-direction:column}.serial-page__head .serial-primary-button{width:100%}.serial-tools{display:block;padding:13px 16px}.serial-search{width:100%;min-width:0;margin-bottom:10px}.serial-filters{padding-bottom:2px}.serial-panel__head{padding:17px 16px}.serial-row{grid-template-columns:1fr auto;gap:12px;padding:15px 16px}.serial-client{grid-column:1}.serial-contract{grid-column:1}.serial-actions{grid-column:2;grid-row:1 / span 3;align-self:center;flex-direction:column}.serial-icon-button span{display:none}.serial-icon-button{justify-content:center;width:34px;height:34px;padding:0}.serial-icon-button i{font-size:14px}}@media(max-width:460px){.serial-stats{grid-template-columns:1fr}.serial-stats article{min-height:72px}.serial-stat-icon{width:38px;height:38px}.serial-page h1{font-size:24px}.serial-panel__head>span{display:none}.serial-drawer__head{padding:18px}.serial-form{padding:18px}.serial-form__actions{flex-direction:column-reverse}.serial-form__actions .serial-secondary-button{width:100%}}@media(max-width:340px){.serial-page{padding-left:10px;padding-right:10px}.serial-row{padding-left:12px;padding-right:12px}.serial-code strong{font-size:12px}}
</style>
<style>
.serial-tools{align-items:end;display:grid;grid-template-columns:minmax(240px,1fr) 150px 96px auto auto;gap:10px}.serial-select{display:grid;gap:4px}.serial-select>span{color:#748395;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.serial-select select{height:40px;padding:0 9px;background:#fff;border:1px solid #dce6ed;border-radius:8px;color:#40576b;font-size:12px;outline:0}.serial-filter-button,.serial-reset-button{height:40px;padding:0 12px;border-radius:8px;font-size:11px;font-weight:800;text-decoration:none;white-space:nowrap}.serial-filter-button{border:1px solid #1592bd;background:#1592bd;color:#fff}.serial-reset-button{display:inline-flex;align-items:center;justify-content:center;border:1px solid #dce6ed;background:#fff;color:#607286}.serial-pagination{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 20px;background:#f8fafc;border-top:1px solid #eaf0f4;color:#718196;font-size:11px;font-weight:700}.serial-pagination ul{display:flex;gap:4px;list-style:none;margin:0;padding:0}.serial-pagination a{display:grid;place-items:center;min-width:32px;height:32px;padding:0 8px;border:1px solid #dce6ed;border-radius:7px;background:#fff;color:#597084;text-decoration:none}.serial-pagination li.is-active a{border-color:#1592bd;background:#1592bd;color:#fff}.serial-pagination li.is-disabled{opacity:.45;pointer-events:none}@media(max-width:760px){.serial-tools{grid-template-columns:1fr 1fr;gap:9px}.serial-search{grid-column:1 / -1;margin-bottom:0}.serial-filter-button,.serial-reset-button{width:100%}.serial-pagination{align-items:flex-start;flex-direction:column;padding:13px 16px}}@media(max-width:380px){.serial-tools{grid-template-columns:1fr}.serial-search{grid-column:auto}}
.serial-table-head{display:grid;grid-template-columns:minmax(190px,1.1fr) minmax(210px,1fr) 150px auto;gap:16px;padding:10px 21px;background:#f8fafc;border-bottom:1px solid #e8eef3;color:#718196;font-size:10px;font-weight:900;letter-spacing:.055em;text-transform:uppercase}.serial-table-head span:last-child{text-align:right}.serial-row{min-height:68px}.serial-row:nth-child(even){background:#fcfdfe}.serial-row:hover{background:#f2fbfe}.serial-actions .serial-icon-button{min-height:34px}.serial-code strong{letter-spacing:.01em}.serial-client__avatar{box-shadow:inset 0 0 0 1px rgba(21,146,189,.08)}@media(max-width:1024px){.serial-table-head{grid-template-columns:minmax(180px,1fr) minmax(190px,1fr) 140px auto}}@media(max-width:760px){.serial-table-head{display:none}.serial-row:nth-child(even){background:#fff}.serial-row{border-left:3px solid transparent}.serial-row:has(.serial-chip.is-signed){border-left-color:#35a56b}.serial-row:has(.serial-chip.is-pending){border-left-color:#e26d64}.serial-row:has(.serial-chip.is-available){border-left-color:#e9a13b}}
.serial-panel-title-icon{color:#1592bd;font-size:16px;margin-right:8px;vertical-align:-1px}
.serial-stats>article>.serial-stat-icon{display:grid!important;place-items:center!important;flex:0 0 42px;font-size:19px!important;line-height:1}.serial-stats>article>.serial-stat-icon i{line-height:1}
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded',function(){var drawer=document.getElementById('serialDrawer'),form=document.getElementById('serialForm'),number=document.getElementById('serialNumber'),client=document.getElementById('serialClient'),method=document.getElementById('serialMethod'),title=document.getElementById('serialDrawerTitle'),kicker=document.getElementById('serialDrawerKicker'),submit=document.getElementById('serialSubmitText'),open=document.getElementById('openSerialForm'),close=document.getElementById('closeSerialForm'),cancel=document.getElementById('cancelSerialForm');function openDrawer(){drawer.classList.add('is-open');drawer.setAttribute('aria-hidden','false');setTimeout(function(){number.focus()},100)}function closeDrawer(){drawer.classList.remove('is-open');drawer.setAttribute('aria-hidden','true')}function resetForm(){form.action='{{ route('serial-numbers.store') }}';number.value='';client.value='';method.innerHTML='';title.textContent='Add serial number';kicker.textContent='New asset';submit.textContent='Save serial number'}if(open)open.addEventListener('click',function(){resetForm();openDrawer()});document.querySelectorAll('.open-serial-form').forEach(function(button){button.addEventListener('click',function(){resetForm();openDrawer()})});document.querySelectorAll('.edit-serial').forEach(function(button){button.addEventListener('click',function(){resetForm();form.action='{{ url('/serial-numbers') }}/'+button.dataset.id;method.innerHTML='<input type="hidden" name="_method" value="PUT">';number.value=button.dataset.number;client.value=button.dataset.clientId||'';title.textContent='Edit serial number';kicker.textContent='Serial registry';submit.textContent='Save changes';openDrawer()})});[close,cancel].forEach(function(button){if(button)button.addEventListener('click',closeDrawer)});document.addEventListener('keydown',function(event){if(event.key==='Escape')closeDrawer()});document.querySelectorAll('.serial-flash button').forEach(function(button){button.addEventListener('click',function(){button.parentNode.remove()})});@if($errors->any()) openDrawer(); @endif});
</script>
@endsection
