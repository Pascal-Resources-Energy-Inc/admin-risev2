@extends('layouts.header')

@section('css')
<style>
    .account-page{padding:0 18px 0;color:#17324d}
    .account-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:22px}
    .account-heading h1{font-size:clamp(24px,3vw,32px);font-weight:750;margin:0;color:#102d49}
    .account-heading p{margin:6px 0 0;color:#708399}
    .account-grid{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(320px,.9fr);gap:20px;align-items:start}
    .account-card{background:#fff;border:1px solid #e3ebf2;border-radius:16px;box-shadow:0 8px 26px rgba(24,58,88,.07);overflow:hidden}
    .account-card-head{padding:18px 22px;border-bottom:1px solid #edf1f5;display:flex;align-items:center;gap:12px}
    .account-card-head h2{font-size:17px;font-weight:700;margin:0;color:#17324d}
    .account-card-head p{font-size:13px;margin:4px 0 0;color:#7b8da0}
    .account-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:#e9f7fc;color:#1394b9;font-size:19px;flex:none}
    .account-card-body{padding:22px}
    .account-identity{display:flex;align-items:center;gap:16px;padding-bottom:20px;margin-bottom:6px;border-bottom:1px solid #edf1f5}
    .account-avatar{width:68px;height:68px;border-radius:50%;object-fit:cover;background:#e9f7fc;border:3px solid #fff;box-shadow:0 0 0 1px #dce8f0}
    .account-name{font-size:20px;font-weight:750;margin:0 0 4px;color:#17324d}
    .account-email{margin:0;color:#74869a;overflow-wrap:anywhere}
    .account-role{display:inline-flex;margin-top:8px;border-radius:999px;background:#e9f7fc;color:#08799e;padding:5px 10px;font-size:12px;font-weight:700}
    .account-details{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 22px}
    .account-detail{padding:13px 0;border-bottom:1px solid #f0f3f6;min-width:0}
    .account-detail dt{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8797a8;font-weight:700;margin-bottom:5px}
    .account-detail dd{font-size:14px;color:#28445e;margin:0;overflow-wrap:anywhere}
    .account-form .form-label{font-size:13px;font-weight:650;color:#425b73}
    .account-form .form-control{min-height:46px;border-color:#d8e3ec;border-radius:9px;padding:10px 13px}
    .account-form .form-control:focus{border-color:#28a0c5;box-shadow:0 0 0 .2rem rgba(40,160,197,.14)}
    .password-hint{font-size:12px;color:#8292a2;margin-top:5px}
    .account-submit{background:#1196ba;border:0;border-radius:9px;color:#fff;font-weight:700;padding:11px 18px}
    .account-submit:hover{background:#087fa2;color:#fff}
    .account-alert{border-radius:10px}
    .account-transactions{margin-top:20px}
    .account-transactions .table{margin:0;white-space:nowrap}
    .account-transactions thead th{font-size:11px;text-transform:uppercase;color:#75879a;background:#f7fafc;border-bottom-color:#e6edf3}
    @media(max-width:850px){.account-grid{grid-template-columns:1fr}.account-page{margin-top:22px}}
    @media(max-width:520px){.account-page{padding:0 12px 24px}.account-card-body{padding:17px}.account-card-head{padding:15px 17px}.account-details{grid-template-columns:1fr}.account-heading{align-items:flex-start;flex-direction:column}}
</style>
@endsection

@section('content')
@php
    $value = function ($source, $key, $fallback = 'Not provided') {
        $item = data_get($source, $key);
        return filled($item) ? $item : $fallback;
    };
@endphp
<main class="account-page">
    <div class="account-heading">
        <div>
            <h1>My Profile</h1>
            <p>Review your account details and keep your sign-in secure.</p>
        </div>
    </div>

    <div class="account-grid">
        <section class="account-card" aria-labelledby="account-details-title">
            <div class="account-card-head">
                <div class="account-icon"><i class="bi bi-person-vcard"></i></div>
                <div><h2 id="account-details-title">Account details</h2><p>Your profile information</p></div>
            </div>
            <div class="account-card-body">
                <div class="account-identity">
                    <img class="account-avatar" src="{{ $user->avatar ?: asset('design/assets/images/profile/user-1.png') }}" onerror="this.src='{{ asset('design/assets/images/profile/user-1.png') }}'" alt="Profile photo">
                    <div class="min-w-0">
                        <h3 class="account-name">{{ $user->name ?: 'User' }}</h3>
                        <p class="account-email">{{ $user->email }}</p>
                        <span class="account-role">{{ $user->role ?: 'Account' }}</span>
                    </div>
                </div>
                <dl class="account-details mb-0">
                    <div class="account-detail"><dt>Status</dt><dd>{{ $value($account, 'status', $value($user, 'status', 'Active')) }}</dd></div>
                    <div class="account-detail"><dt>Designation</dt><dd>{{ $value($user, 'designation') }}</dd></div>
                    <div class="account-detail"><dt>Employee number</dt><dd>{{ $value($user, 'employee_number') }}</dd></div>
                    <div class="account-detail"><dt>Department</dt><dd>{{ $value($user, 'department') }}</dd></div>
                    <div class="account-detail"><dt>Phone</dt><dd>{{ $value($account, 'number') }}</dd></div>
                    <div class="account-detail"><dt>MFI</dt><dd>{{ $value($account, 'mfi') }}</dd></div>
                    <div class="account-detail"><dt>Center</dt><dd>{{ $value($account, 'center') }}</dd></div>
                    <div class="account-detail"><dt>Sales territory</dt><dd>{{ $value($account, 'area') }}</dd></div>
                    <div class="account-detail" style="grid-column:1/-1"><dt>Address</dt><dd>{{ $value($account, 'address', $value($account, 'street_address')) }}</dd></div>
                </dl>
            </div>
        </section>

        <section class="account-card" aria-labelledby="change-password-title">
            <div class="account-card-head">
                <div class="account-icon"><i class="bi bi-shield-lock"></i></div>
                <div><h2 id="change-password-title">Change password</h2><p>Confirm your current password to update it.</p></div>
            </div>
            <div class="account-card-body">
                <form class="account-form" method="POST" action="{{ route('user-profile.password') }}" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="current_password">Current password</label>
                        <input class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}" type="password" id="current_password" name="current_password" required autocomplete="current-password">
                        @if($errors->has('current_password'))<div class="invalid-feedback">{{ $errors->first('current_password') }}</div>@endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">New password</label>
                        <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" id="password" name="password" minlength="8" required autocomplete="new-password">
                        <div class="password-hint">Use at least 8 characters.</div>
                        @if($errors->has('password'))<div class="invalid-feedback">{{ $errors->first('password') }}</div>@endif
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Confirm new password</label>
                        <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" minlength="8" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn account-submit"><i class="bi bi-key me-2"></i>Update password</button>
                </form>
            </div>
        </section>
    </div>

    @if($transactions->isNotEmpty())
        <section class="account-card account-transactions" aria-labelledby="profile-transactions-title">
            <div class="account-card-head"><div class="account-icon"><i class="bi bi-receipt"></i></div><div><h2 id="profile-transactions-title">Recent transactions</h2><p>Your latest 50 transactions</p></div></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Transaction</th><th>Product</th><th>Quantity</th><th>Amount</th><th>Date</th></tr></thead>
                    <tbody>
                    @foreach($transactions as $transaction)
                        <tr><td>#{{ $transaction->id }}</td><td>{{ $transaction->item ?: '—' }}</td><td>{{ $transaction->qty ?? '—' }}</td><td>{{ isset($transaction->price, $transaction->qty) ? number_format($transaction->price * $transaction->qty, 2) : '—' }}</td><td>{{ $transaction->created_at ? $transaction->created_at->format('M d, Y') : '—' }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</main>
@endsection
