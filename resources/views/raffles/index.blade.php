@extends('layouts.header')

@section('css')
<style>
    .raffle-page { display: grid; gap: 16px; }
    .raffle-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; }
    .raffle-kicker { color: #b91c1c; font-size: 11px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .raffle-title { margin: 3px 0 0; color: #101828; font-size: 25px; font-weight: 900; }
    .raffle-copy { margin: 4px 0 0; color: #667085; font-size: 13px; }
    .raffle-stats { display: grid; grid-template-columns: repeat(4, minmax(150px, 1fr)); gap: 12px; }
    .raffle-stat { display: flex; align-items: center; gap: 12px; min-height: 84px; padding: 15px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 8px 22px rgba(15, 23, 42, .05); }
    .raffle-stat-icon { width: 42px; height: 42px; display: inline-flex; flex: 0 0 auto; align-items: center; justify-content: center; color: #b91c1c; background: #fef2f2; border-radius: 10px; font-size: 19px; }
    .raffle-stat small { display: block; color: #667085; font-size: 10px; font-weight: 900; text-transform: uppercase; }
    .raffle-stat strong { display: block; margin-top: 3px; color: #101828; font-size: 22px; line-height: 1; }
    .raffle-panel { overflow: hidden; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); }
    .raffle-toolbar { display: flex; align-items: end; justify-content: space-between; gap: 14px; padding: 15px 18px; background: #f8fafc; border-bottom: 1px solid #e9edf2; }
    .raffle-filter { display: grid; grid-template-columns: minmax(240px, 1fr) 170px auto auto; align-items: end; gap: 8px; flex: 1; }
    .raffle-filter label { margin-bottom: 4px; color: #667085; font-size: 10px; font-weight: 900; text-transform: uppercase; }
    .raffle-filter .form-control, .raffle-filter .form-select, .raffle-filter .btn { min-height: 39px; }
    .raffle-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; padding: 16px; }
    .raffle-card { display: flex; flex-direction: column; min-height: 270px; padding: 17px; border: 1px solid #e6e9ef; border-radius: 12px; background: linear-gradient(145deg, #fff 0%, #fcfcfd 100%); }
    .raffle-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
    .raffle-card h5 { margin: 0; color: #111827; font-size: 17px; font-weight: 900; }
    .raffle-prize { margin-top: 5px; color: #b91c1c; font-size: 13px; font-weight: 800; }
    .raffle-status { display: inline-flex; align-items: center; gap: 5px; padding: 5px 9px; border-radius: 999px; font-size: 10px; font-weight: 900; text-transform: uppercase; }
    .raffle-status.open { color: #166534; background: #dcfce7; }
    .raffle-status.scheduled { color: #075985; background: #e0f2fe; }
    .raffle-status.draft { color: #475467; background: #f2f4f7; }
    .raffle-status.closed, .raffle-status.ended { color: #92400e; background: #fef3c7; }
    .raffle-status.drawn { color: #7e22ce; background: #f3e8ff; }
    .raffle-description { min-height: 38px; margin: 13px 0; color: #667085; font-size: 12px; line-height: 1.55; }
    .raffle-meta { display: grid; gap: 7px; padding: 11px; background: #f8fafc; border-radius: 9px; }
    .raffle-meta-row { display: flex; justify-content: space-between; gap: 12px; color: #667085; font-size: 11px; }
    .raffle-meta-row strong { color: #344054; text-align: right; }
    .raffle-winner { margin-top: 11px; padding: 11px; color: #6b21a8; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 9px; font-size: 11px; }
    .raffle-winner strong { display: block; margin-top: 2px; font-size: 13px; }
    .raffle-actions { display: flex; flex-wrap: wrap; gap: 6px; margin-top: auto; padding-top: 14px; }
    .raffle-actions .btn { font-size: 10px; font-weight: 800; }
    .raffle-empty { grid-column: 1 / -1; padding: 48px 15px; color: #98a2b3; text-align: center; }
    .entry-panel-head { display: flex; align-items: center; justify-content: space-between; padding: 15px 18px; background: #f8fafc; border-bottom: 1px solid #e9edf2; }
    .entry-panel-head h6 { margin: 0; color: #1d2939; font-weight: 900; }
    .entry-table { margin: 0; min-width: 880px; }
    .entry-table th { padding: 12px 15px; color: #667085; background: #fff; font-size: 10px; text-transform: uppercase; }
    .entry-table td { padding: 13px 15px; border-color: #f0f2f5; font-size: 12px; vertical-align: middle; }
    .ticket-code { color: #991b1b; font-family: monospace; font-size: 12px; font-weight: 900; }
    .winner-row { background: #faf5ff; }
    .raffle-modal .modal-content { overflow: hidden; border: 0; border-radius: 14px; }
    .raffle-modal .modal-header { padding: 20px 22px; background: linear-gradient(135deg, #fff, #fff5f5); border-bottom-color: #f1e4e4; }
    .raffle-modal .modal-title { color: #101828; font-weight: 900; }
    .raffle-modal .modal-body { padding: 20px 22px; background: #f8fafc; }
    .raffle-form-card { padding: 16px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; }
    .raffle-form-card + .raffle-form-card { margin-top: 12px; }
    .raffle-form-card h6 { margin-bottom: 14px; color: #344054; font-size: 12px; font-weight: 900; text-transform: uppercase; }
    .raffle-modal label { color: #344054; font-size: 11px; font-weight: 800; }
    .raffle-modal .form-control, .raffle-modal .form-select { min-height: 41px; border-color: #dfe4ea; }
    .participant-choice { padding: 12px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 9px; }
    @media (max-width: 1050px) {
        .raffle-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .raffle-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 700px) {
        .raffle-head, .raffle-toolbar { align-items: stretch; flex-direction: column; }
        .raffle-filter { grid-template-columns: 1fr; }
        .raffle-grid, .raffle-stats { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
@php
    $totalEntries = $entries->count();
    $openCount = $raffles->filter(function ($raffle) { return $raffle->acceptsEntries(); })->count();
    $drawnCount = $raffles->where('status', 'drawn')->count();
@endphp

<div class="raffle-page">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle-fill me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="raffle-head">
        <div>
            <div class="raffle-kicker"><i class="bi bi-stars"></i> Promotions</div>
            <h4 class="raffle-title">Raffle Management</h4>
            <p class="raffle-copy">Create raffles, issue participant tickets, and draw one auditable winner.</p>
        </div>
        <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#raffleFormModal" onclick="openCreateRaffle()">
            <i class="bi bi-plus-lg"></i> New Raffle
        </button>
    </div>

    <div class="raffle-stats">
        <div class="raffle-stat">
            <span class="raffle-stat-icon"><i class="bi bi-gift"></i></span>
            <div><small>Total Raffles</small><strong>{{ number_format($raffles->count()) }}</strong></div>
        </div>
        <div class="raffle-stat">
            <span class="raffle-stat-icon"><i class="bi bi-broadcast"></i></span>
            <div><small>Accepting Entries</small><strong>{{ number_format($openCount) }}</strong></div>
        </div>
        <div class="raffle-stat">
            <span class="raffle-stat-icon"><i class="bi bi-ticket-perforated"></i></span>
            <div><small>Issued Tickets</small><strong>{{ number_format($totalEntries) }}</strong></div>
        </div>
        <div class="raffle-stat">
            <span class="raffle-stat-icon"><i class="bi bi-trophy"></i></span>
            <div><small>Completed Draws</small><strong>{{ number_format($drawnCount) }}</strong></div>
        </div>
    </div>

    <div class="raffle-panel">
        <div class="raffle-toolbar">
            <form method="GET" action="{{ route('raffles') }}" class="raffle-filter">
                <div>
                    <label for="raffleSearch">Search</label>
                    <input id="raffleSearch" type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Raffle title or prize">
                </div>
                <div>
                    <label for="raffleStatus">Status</label>
                    <select id="raffleStatus" name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach(['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed', 'drawn' => 'Drawn'] as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i> Filter</button>
                <a class="btn btn-outline-secondary" href="{{ route('raffles') }}">Reset</a>
            </form>
        </div>

        <div class="raffle-grid">
            @forelse($raffles as $raffle)
                @php $statusLabel = $raffle->statusLabel(); @endphp
                <article class="raffle-card">
                    <div class="raffle-card-top">
                        <div>
                            <h5>{{ $raffle->title }}</h5>
                            <div class="raffle-prize"><i class="bi bi-gift-fill"></i> {{ $raffle->prize }}</div>
                        </div>
                        <span class="raffle-status {{ strtolower($statusLabel) }}">{{ $statusLabel }}</span>
                    </div>
                    <p class="raffle-description">{{ $raffle->description ?: 'No description provided.' }}</p>
                    <div class="raffle-meta">
                        <div class="raffle-meta-row"><span>Tickets issued</span><strong>{{ number_format($raffle->entries->count()) }}</strong></div>
                        <div class="raffle-meta-row"><span>Limit per participant</span><strong>{{ number_format($raffle->max_entries_per_participant) }}</strong></div>
                        <div class="raffle-meta-row"><span>Starts</span><strong>{{ $raffle->starts_at ? $raffle->starts_at->format('M d, Y g:i A') : 'Immediately' }}</strong></div>
                        <div class="raffle-meta-row"><span>Ends</span><strong>{{ $raffle->ends_at ? $raffle->ends_at->format('M d, Y g:i A') : 'No deadline' }}</strong></div>
                    </div>
                    @if($raffle->winner)
                        <div class="raffle-winner">
                            <i class="bi bi-trophy-fill"></i> Winning ticket
                            <strong>{{ $raffle->winner->participant_name }} &mdash; {{ $raffle->winner->ticket_number }}</strong>
                        </div>
                    @endif
                    <div class="raffle-actions">
                        @if($raffle->acceptsEntries())
                            <button
                                class="btn btn-success btn-sm js-add-raffle-entry"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#entryModal"
                                data-raffle-id="{{ $raffle->id }}"
                                data-raffle-title="{{ $raffle->title }}"
                                data-max-entries="{{ $raffle->max_entries_per_participant }}"
                            >
                                <i class="bi bi-ticket-perforated"></i> Add Entry
                            </button>
                        @endif
                        @if($raffle->status !== 'drawn' && $raffle->entries->count() > 0)
                            <form method="POST" action="{{ route('raffles.draw', $raffle->id) }}" onsubmit="return confirm('Draw a winner now? This action cannot be undone.');">
                                @csrf
                                <button class="btn btn-warning btn-sm" type="submit"><i class="bi bi-shuffle"></i> Draw Winner</button>
                            </form>
                        @endif
                        @if($raffle->status !== 'drawn')
                            <button
                                class="btn btn-outline-primary btn-sm js-edit-raffle"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#raffleFormModal"
                                data-raffle="{!! e(json_encode([
                                    'id' => $raffle->id,
                                    'title' => $raffle->title,
                                    'description' => $raffle->description,
                                    'prize' => $raffle->prize,
                                    'starts_at' => $raffle->starts_at ? $raffle->starts_at->format('Y-m-d\TH:i') : '',
                                    'ends_at' => $raffle->ends_at ? $raffle->ends_at->format('Y-m-d\TH:i') : '',
                                    'max_entries_per_participant' => $raffle->max_entries_per_participant,
                                    'status' => $raffle->status,
                                ])) !!}"
                            >
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('raffles.destroy', $raffle->id) }}" onsubmit="return confirm('Delete this raffle and all of its entries?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="raffle-empty">
                    <i class="bi bi-gift fs-1"></i>
                    <h6 class="mt-2">No raffles found</h6>
                    <p class="mb-0">Create your first raffle to begin issuing entries.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="raffle-panel">
        <div class="entry-panel-head">
            <h6><i class="bi bi-ticket-detailed me-1"></i> Raffle Entry Register</h6>
            <span class="badge bg-dark">{{ number_format($entries->count()) }} ticket(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table entry-table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Participant</th>
                        <th>Contact</th>
                        <th>Raffle</th>
                        <th>Entered</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr class="{{ $entry->status === 'winner' ? 'winner-row' : '' }}">
                            <td><span class="ticket-code">{{ $entry->ticket_number }}</span></td>
                            <td>
                                <strong>{{ $entry->participant_name }}</strong>
                                <div class="text-muted">{{ $entry->user_id ? 'Registered user' : 'Manual entry' }}</div>
                            </td>
                            <td>{{ $entry->email ?: ($entry->phone ?: '-') }}</td>
                            <td>{{ optional($entry->raffle)->title }}</td>
                            <td>{{ $entry->entered_at->format('M d, Y g:i A') }}</td>
                            <td>
                                @if($entry->status === 'winner')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill"></i> Winner</span>
                                @else
                                    <span class="badge bg-success">Eligible</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($entry->raffle && $entry->raffle->status !== 'drawn')
                                    <form method="POST" action="{{ route('raffles.entries.destroy', [$entry->raffle_id, $entry->id]) }}" onsubmit="return confirm('Remove this raffle ticket?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" title="Remove entry"><i class="bi bi-trash"></i></button>
                                    </form>
                                @else
                                    <span class="text-muted">Locked</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No raffle entries have been issued.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade raffle-modal" id="raffleFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="raffleForm" class="modal-content" method="POST" action="{{ route('raffles.store') }}">
            @csrf
            <input id="raffleMethod" type="hidden" name="_method" value="POST">
            <div class="modal-header">
                <div>
                    <div class="raffle-kicker">Raffle Campaign</div>
                    <h5 id="raffleModalTitle" class="modal-title">Create Raffle</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="raffle-form-card">
                    <h6>Campaign details</h6>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Raffle title *</label>
                            <input id="raffleTitleInput" name="title" class="form-control" maxlength="255" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Prize *</label>
                            <input id="rafflePrizeInput" name="prize" class="form-control" maxlength="255" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea id="raffleDescriptionInput" name="description" class="form-control" rows="3" maxlength="2000"></textarea>
                        </div>
                    </div>
                </div>
                <div class="raffle-form-card">
                    <h6>Schedule and entry rules</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Starts at</label>
                            <input id="raffleStartsInput" type="datetime-local" name="starts_at" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ends at</label>
                            <input id="raffleEndsInput" type="datetime-local" name="ends_at" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tickets per participant *</label>
                            <input id="raffleLimitInput" type="number" name="max_entries_per_participant" class="form-control" min="1" max="1000" value="1" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Status *</label>
                            <select id="raffleStatusInput" name="status" class="form-select" required>
                                <option value="draft">Draft</option>
                                <option value="open">Open for entries</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" type="submit"><i class="bi bi-check-lg"></i> Save Raffle</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade raffle-modal" id="entryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="entryForm" class="modal-content" method="POST">
            @csrf
            <div class="modal-header">
                <div>
                    <div class="raffle-kicker">Issue Ticket</div>
                    <h5 class="modal-title">Add Raffle Entry</h5>
                    <small id="entryRaffleName" class="text-muted"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="raffle-form-card">
                    <h6>Choose a registered participant</h6>
                    <select id="entryUserId" name="user_id" class="form-select">
                        <option value="">Manual / guest participant</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} &mdash; {{ $user->email }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Selecting a user automatically uses their saved name and contact information.</small>
                </div>
                <div id="manualParticipantFields" class="raffle-form-card">
                    <h6>Manual participant information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full name *</label>
                            <input id="participantName" name="participant_name" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input name="phone" class="form-control" maxlength="40">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Number of tickets *</label>
                            <input id="entryQuantity" name="quantity" type="number" class="form-control" min="1" value="1" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" type="submit"><i class="bi bi-ticket-perforated"></i> Issue Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascript')
<script>
    var raffleStoreUrl = {!! json_encode(route('raffles.store')) !!};
    var raffleBaseUrl = {!! json_encode(url('/raffles')) !!};

    function openCreateRaffle() {
        var form = document.getElementById('raffleForm');
        form.reset();
        form.action = raffleStoreUrl;
        document.getElementById('raffleMethod').value = 'POST';
        document.getElementById('raffleModalTitle').textContent = 'Create Raffle';
        document.getElementById('raffleLimitInput').value = 1;
        document.getElementById('raffleStatusInput').value = 'draft';
    }

    function openEditRaffle(raffle) {
        document.getElementById('raffleForm').action = raffleBaseUrl + '/' + raffle.id;
        document.getElementById('raffleMethod').value = 'PUT';
        document.getElementById('raffleModalTitle').textContent = 'Edit Raffle';
        document.getElementById('raffleTitleInput').value = raffle.title || '';
        document.getElementById('rafflePrizeInput').value = raffle.prize || '';
        document.getElementById('raffleDescriptionInput').value = raffle.description || '';
        document.getElementById('raffleStartsInput').value = raffle.starts_at || '';
        document.getElementById('raffleEndsInput').value = raffle.ends_at || '';
        document.getElementById('raffleLimitInput').value = raffle.max_entries_per_participant || 1;
        document.getElementById('raffleStatusInput').value = raffle.status || 'draft';
    }

    function openEntryModal(raffleId, raffleTitle, maxEntries) {
        var form = document.getElementById('entryForm');
        form.reset();
        form.action = raffleBaseUrl + '/' + raffleId + '/entries';
        document.getElementById('entryRaffleName').textContent = raffleTitle;
        document.getElementById('entryQuantity').max = maxEntries;
        document.getElementById('entryQuantity').value = 1;
        toggleParticipantFields();
    }

    function toggleParticipantFields() {
        var hasUser = document.getElementById('entryUserId').value !== '';
        var fields = document.getElementById('manualParticipantFields');
        var nameInput = document.getElementById('participantName');
        fields.style.opacity = hasUser ? '.55' : '1';
        fields.querySelectorAll('input:not([name="quantity"])').forEach(function (input) {
            input.disabled = hasUser;
        });
        nameInput.required = !hasUser;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var entryUser = document.getElementById('entryUserId');

        if (entryUser) {
            entryUser.addEventListener('change', toggleParticipantFields);
        }

        document.querySelectorAll('.js-add-raffle-entry').forEach(function (button) {
            button.addEventListener('click', function () {
                openEntryModal(
                    parseInt(button.dataset.raffleId, 10),
                    button.dataset.raffleTitle || '',
                    parseInt(button.dataset.maxEntries, 10) || 1
                );
            });
        });

        document.querySelectorAll('.js-edit-raffle').forEach(function (button) {
            button.addEventListener('click', function () {
                try {
                    openEditRaffle(JSON.parse(button.dataset.raffle));
                } catch (error) {
                    console.error('Unable to load raffle details.', error);
                }
            });
        });
    });
</script>
@endsection
