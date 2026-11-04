@extends('layouts.app')

@section('content')
@include('auth.partials.screen-styles')

<style>
    .auth-feedback { margin: 0 0 18px; padding: 12px 14px; border: 1px solid #f2c7c7; border-radius: 11px; background: #fff6f6; color: #a83232; font-size: 13px; }
    .auth-back-link { margin: 22px 0 0; color: #718397; font-size: 13px; text-align: center; }
    .auth-back-link a { color: #087ca8; font-weight: 700; text-decoration: none; }
    .auth-back-link a:hover { text-decoration: underline; }
</style>

<div class="login-page auth-screen" id="forgotPasswordPage">
    <div class="auth-card login-card">
        <section class="auth-brand-panel login-brand-panel">
            <div class="brand-glow brand-glow-one"></div>
            <div class="brand-glow brand-glow-two"></div>
            <div class="brand-image-wrap">
                <img src="{{ asset('images/admin-login.png') }}" alt="" class="auth-illustration">
            </div>
            <p class="brand-caption">A simple, secure way to get back into your GazLite account.</p>
        </section>

        <section class="auth-form-panel">
            <div class="form-topbar form-topbar-single">
                <img src="{{ asset('images/logo_nya.png') }}" alt="GazLite" class="form-logo">
            </div>

            <div class="form-heading">
                <p class="auth-eyebrow">ACCOUNT RECOVERY</p>
                <h1>Forgot your password?</h1>
                <p class="auth-description">Enter the email address linked to your account and we’ll send you a verification code.</p>
            </div>

            @if ($errors->has('email'))
                <div class="auth-feedback" role="alert">{{ $errors->first('email') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="input-group">
                    <label for="email" class="input-label">Email address</label>
                    <input id="email" type="email" class="form-input" name="email" value="{{ old('email') }}" placeholder="name@example.com" autocomplete="email" required autofocus>
                </div>

                <button class="signin-button submit-button" type="submit">
                    <span>Send verification code</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <p class="auth-back-link">Remember your password? <a href="{{ route('login', ['direct' => 'true']) }}">Back to sign in</a></p>
        </section>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('status'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'OTP Sent!',
            text: 'We have sent a 6-digit OTP code to your email address. Please check your email.',
            confirmButtonText: 'Enter OTP',
            confirmButtonColor: '#5DADE2',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            },
            customClass: {
                popup: 'swal-custom-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('password.otp') }}?email={{ request('email') }}";
            }
        });
    });
</script>
<style>
    .swal-custom-popup {
        border-radius: 15px !important;
    }
    
    .swal2-popup {
        font-family: inherit;
    }
    
    .swal2-title {
        color: #4facfe;
        font-size: 1.5rem;
        font-weight: 600;
    }
</style>

<script>
function goToLogin(event) {
    event.preventDefault();
    window.location.href = "{{url('/')}}#login-page";
}
</script>



@endif

@endsection
