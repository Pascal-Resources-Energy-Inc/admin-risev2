@extends('layouts.app')

@section('content')
@include('auth.partials.screen-styles')

<style>
    .otp-screen .auth-form-panel { padding-top: clamp(30px, 5vw, 62px); padding-bottom: clamp(30px, 5vw, 62px); }
    .otp-screen .auth-description { margin-bottom: 22px; }
    .otp-input-container { display: flex; justify-content: space-between; gap: clamp(6px, 1.5vw, 12px); margin: 10px 0 8px; }
    .otp-input { width: min(56px, 14vw) !important; height: 56px !important; border: 1px solid #dbe6ee !important; border-radius: 12px !important; background: #fbfdfe !important; color: #142b3d !important; font-size: 21px !important; font-weight: 750 !important; }
    .otp-input:focus { border-color: #39b3d2 !important; box-shadow: 0 0 0 4px rgba(7,153,196,.1) !important; outline: 0 !important; }
    .otp-input.filled { border-color: #39b3d2 !important; background: #f2fbfd !important; text-align: center; }
    .otp-screen .resend-container { margin-top: 20px; padding: 14px; border: 1px solid #e5eef3; border-radius: 12px; background: #f8fbfd; text-align: center; }
    .otp-screen .resend-text { margin-bottom: 4px; color: #718397; font-size: 13px; }
    .otp-screen .timer { color: #087ca8; font-weight: 700; }
    .otp-screen #resend-link { padding: 4px 8px; border: 0; background: transparent; color: #087ca8; font-size: 13px; font-weight: 700; }
    .otp-screen #resend-link:hover { text-decoration: underline; }
    .otp-screen .auth-feedback { margin: 0 0 16px; padding: 11px 13px; border: 1px solid #f2c7c7; border-radius: 11px; background: #fff6f6; color: #a83232; font-size: 13px; }
    .otp-screen .auth-feedback-info { border-color: #d8eef4; background: #f3fbfd; color: #35677d; }
    .otp-screen .auth-feedback-success { border-color: #c9ead9; background: #f1fbf5; color: #27764b; }
    .otp-screen .auth-back-link { margin: 20px 0 0; color: #718397; font-size: 13px; text-align: center; }
    .otp-screen .auth-back-link a { color: #087ca8; font-weight: 700; text-decoration: none; }
    .otp-screen .auth-back-link a:hover { text-decoration: underline; }
    .otp-screen .signin-button:disabled { cursor: not-allowed; opacity: .55; box-shadow: none; }
    @media (max-width: 420px) { .otp-screen .auth-form-panel { padding: 25px 20px 28px; } .otp-input-container { gap: 6px; } }
</style>

<div class="login-page auth-screen otp-screen" id="verifyOtpPage">
    <div class="auth-card login-card">
        <section class="auth-brand-panel login-brand-panel">
            <div class="brand-glow brand-glow-one"></div>
            <div class="brand-glow brand-glow-two"></div>
            <div class="brand-image-wrap">
                <img src="{{ asset('images/admin-login.png') }}" alt="" class="auth-illustration">
            </div>
            <p class="brand-caption">One quick check keeps your GazLite account secure.</p>
        </section>

        <section class="auth-form-panel">
            <div class="form-topbar form-topbar-single">
                <img src="{{ asset('images/logo_nya.png') }}" alt="GazLite" class="form-logo">
            </div>

            <div class="form-heading">
                <p class="auth-eyebrow">ACCOUNT RECOVERY</p>
                <h1>Verify your email</h1>
                <p class="auth-description">Enter the six-digit code we sent to <strong>{{ $email }}</strong>.</p>
            </div>

            @if (session('success'))
                <div class="auth-feedback auth-feedback-success" role="status">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="auth-feedback" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.verify-otp') }}" id="otp-form">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="input-group">
                    <label class="input-label" for="otp-0">Verification code</label>
                    <div class="otp-input-container" role="group" aria-label="Six-digit verification code">
                        <input id="otp-0" type="text" inputmode="numeric" pattern="[0-9]*" class="otp-input" maxlength="1" data-index="0" autocomplete="one-time-code" aria-label="Digit 1">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" class="otp-input" maxlength="1" data-index="1" autocomplete="off" aria-label="Digit 2">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" class="otp-input" maxlength="1" data-index="2" autocomplete="off" aria-label="Digit 3">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" class="otp-input" maxlength="1" data-index="3" autocomplete="off" aria-label="Digit 4">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" class="otp-input" maxlength="1" data-index="4" autocomplete="off" aria-label="Digit 5">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" class="otp-input" maxlength="1" data-index="5" autocomplete="off" aria-label="Digit 6">
                    </div>
                    <input type="hidden" name="otp" id="otp-hidden" required>
                </div>

                <button class="signin-button submit-button" type="submit" id="verify-btn" disabled>
                    <span class="btn-text">Verify code</span>
                    <span class="btn-loading" style="display: none;">Verifying…</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <div class="resend-container">
                <div class="resend-text">Didn’t receive the code?</div>
                <span class="timer" id="timer-display">Resend in <span id="countdown">60</span>s</span>
                <form method="POST" action="{{ route('password.email') }}" id="resend-form" style="display: none;">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" id="resend-link">Resend code</button>
                </form>
            </div>

            <p class="auth-back-link"><a href="{{ route('password.request') }}">Back to forgot password</a></p>
        </section>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('.otp-input');
    const otpHidden = document.getElementById('otp-hidden');
    const verifyBtn = document.getElementById('verify-btn');
    const otpForm = document.getElementById('otp-form');
    const resendForm = document.getElementById('resend-form');
    const countdown = document.getElementById('countdown');
    const timerDisplay = document.getElementById('timer-display');
    const btnText = document.querySelector('.btn-text');
    const btnLoading = document.querySelector('.btn-loading');
    
    let isSubmitting = false;
    
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            if (isSubmitting) return;
            
            let value = e.target.value;
            
            if (!/^\d$/.test(value) && value !== '') {
                e.target.value = '';
                e.target.classList.add('error');
                setTimeout(() => e.target.classList.remove('error'), 300);
                return;
            }
            
            e.target.classList.remove('error');
            
            if (value) {
                e.target.classList.add('filled');
                
                if (index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            } else {
                e.target.classList.remove('filled');
            }
            
            updateOTP();
        });
        
        input.addEventListener('keydown', function(e) {
            if (isSubmitting) return;
            
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
                otpInputs[index - 1].value = '';
                otpInputs[index - 1].classList.remove('filled');
                updateOTP();
            }
            
            if (e.key === 'ArrowLeft' && index > 0) {
                e.preventDefault();
                otpInputs[index - 1].focus();
            }
            if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                e.preventDefault();
                otpInputs[index + 1].focus();
            }
            
            if (e.key === 'Enter' && !verifyBtn.disabled) {
                e.preventDefault();
                verifyBtn.click();
            }
        });
        
        input.addEventListener('paste', function(e) {
            if (isSubmitting) return;
            
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').replace(/\D/g, '');
            
            if (pasteData.length >= 6) {
                otpInputs.forEach(inp => {
                    inp.value = '';
                    inp.classList.remove('filled');
                });
                
                for (let i = 0; i < Math.min(pasteData.length, otpInputs.length); i++) {
                    otpInputs[i].value = pasteData[i];
                    otpInputs[i].classList.add('filled');
                }
                
                const lastIndex = Math.min(pasteData.length - 1, otpInputs.length - 1);
                otpInputs[lastIndex].focus();
                
                updateOTP();
            }
        });

        input.addEventListener('keypress', function(e) {
            if (!/\d/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
                e.preventDefault();
            }
        });
    });
    
    function updateOTP() {
        const otp = Array.from(otpInputs).map(input => input.value).join('');
        otpHidden.value = otp;
        
        const isComplete = otp.length === 6;
        verifyBtn.disabled = !isComplete || isSubmitting;
        
        if (isComplete) {
            verifyBtn.style.opacity = '1';
        } else {
            verifyBtn.style.opacity = '0.6';
        }
    }
    
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        const finalOtp = otpHidden.value.trim();
        
        if (finalOtp.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete OTP',
                text: 'Please enter all 6 digits of the OTP',
                confirmButtonColor: '#4facfe'
            });
            return;
        }
        
        isSubmitting = true;
        verifyBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        document.querySelector('.otp-screen .auth-form-panel').classList.add('loading');
        
        this.submit();
    });
    
    resendForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Resend OTP?',
            text: 'This will send a new OTP code to your email.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4facfe',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, resend it!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    
    let timeLeft = 60;
    const countdownTimer = setInterval(function() {
        timeLeft--;
        countdown.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(countdownTimer);
            timerDisplay.style.display = 'none';
            resendForm.style.display = 'block';
        }
    }, 1000);
    
    setTimeout(() => {
        otpInputs[0].focus();
    }, 100);
    
    otpInputs.forEach(input => {
        input.value = '';
        input.classList.remove('filled');
    });
    updateOTP();
});
</script>

@if (session('status'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'OTP Sent!',
        text: '{{ session("status") }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4facfe'
    });
});
</script>
@endif

@endsection
