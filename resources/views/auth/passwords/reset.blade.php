@extends('layouts.app')

@section('content')
@include('auth.partials.screen-styles')
<style>
    .reset-screen .auth-form-panel { padding-top: clamp(28px, 4vw, 50px); padding-bottom: clamp(28px, 4vw, 50px); }
    .reset-screen .auth-description { margin-bottom: 20px; }
    .reset-screen .reset-notice { margin: 0 0 18px; padding: 12px 14px; border: 1px solid #c9ead9; border-radius: 11px; background: #f1fbf5; color: #27764b; font-size: 13px; }
    .reset-screen .reset-error { margin: 0 0 16px; padding: 11px 13px; border: 1px solid #f2c7c7; border-radius: 11px; background: #fff6f6; color: #a83232; font-size: 13px; }
    .reset-screen .reset-email { background: #f3f7fa; color: #607487; }
    .reset-screen .password-requirements { margin-top: 10px; padding: 13px 15px; border: 1px solid #e5eef3; border-radius: 11px; background: #f8fbfd; color: #344c5e; font-size: 12px; }
    .reset-screen .password-requirements strong { display: block; margin-bottom: 5px; font-size: 12px; }
    .reset-screen .password-requirements ul { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 4px 14px; margin: 0; padding-left: 18px; }
    .reset-screen .password-requirements li { margin: 0; }
    .reset-screen .requirement-not-met { color: #ad4b4b; }
    .reset-screen .requirement-met { color: #258353; }
    .reset-screen .password-strength { min-height: 18px; margin-top: 7px; color: #718397; font-size: 12px; }
    .reset-screen .strength-weak { color: #b83d3d; font-weight: 700; }
    .reset-screen .strength-medium { color: #9a6700; font-weight: 700; }
    .reset-screen .strength-strong { color: #258353; font-weight: 700; }
    .reset-screen .auth-back-link { margin: 20px 0 0; color: #718397; font-size: 13px; text-align: center; }
    .reset-screen .auth-back-link a { color: #087ca8; font-weight: 700; text-decoration: none; }
    .reset-screen .auth-back-link a:hover { text-decoration: underline; }
    @media (max-width: 460px) { .reset-screen .password-requirements ul { grid-template-columns: 1fr; } }
</style>

<div class="login-page auth-screen reset-screen" id="resetPasswordPage">
    <div class="auth-card login-card">
        <section class="auth-brand-panel login-brand-panel">
            <div class="brand-glow brand-glow-one"></div>
            <div class="brand-glow brand-glow-two"></div>
            <div class="brand-image-wrap">
                <img src="{{ asset('images/admin-login.png') }}" alt="" class="auth-illustration">
            </div>
            <p class="brand-caption">Choose a strong password to secure your GazLite account.</p>
        </section>

        <section class="auth-form-panel">
            <div class="form-topbar form-topbar-single">
                <img src="{{ asset('images/logo_nya.png') }}" alt="GazLite" class="form-logo">
            </div>

            <div class="form-heading">
                <p class="auth-eyebrow">ACCOUNT RECOVERY</p>
                <h1>Reset your password</h1>
                <p class="auth-description">Create a new password for your account.</p>
            </div>

            @if (session('success'))
                <div class="reset-notice" role="status">{{ session('success') }}</div>
            @endif
            <div class="reset-notice" role="status">Your email is verified. You can now set a new password.</div>
            @if ($errors->has('password'))
                <div class="reset-error" role="alert">{{ $errors->first('password') }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="input-group">
                    <label for="reset-email" class="input-label">Email address</label>
                    <input id="reset-email" type="email" class="form-input reset-email" value="{{ $email }}" readonly autocomplete="email">
                </div>
                <div class="input-group">
                    <label for="password" class="input-label">New password</label>
                    <input id="password" type="password" class="form-input" placeholder="Create a new password" name="password" minlength="8" autocomplete="new-password" required>
                    <div class="password-strength" id="password-strength" aria-live="polite"></div>
                    <div class="password-requirements">
                        <strong>Password requirements</strong>
                        <ul id="password-requirements">
                            <li id="req-length" class="requirement-not-met">At least 8 characters</li>
                            <li id="req-uppercase" class="requirement-not-met">One uppercase letter</li>
                            <li id="req-lowercase" class="requirement-not-met">One lowercase letter</li>
                            <li id="req-number" class="requirement-not-met">One number</li>
                            <li id="req-special" class="requirement-not-met">One special character (@$!%*?&)</li>
                        </ul>
                    </div>
                </div>
                <div class="input-group">
                    <label for="password-confirm" class="input-label">Confirm new password</label>
                    <input id="password-confirm" type="password" class="form-input" placeholder="Re-enter your new password" name="password_confirmation" autocomplete="new-password" required>
                    <div id="password-match" class="password-strength" aria-live="polite"></div>
                </div>
                <button class="signin-button submit-button" type="submit" id="reset-btn">
                    <span>Reset password</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>
            <p class="auth-back-link">Remembered your password? <a href="{{ route('login', ['direct' => 'true']) }}">Back to sign in</a></p>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password-confirm');
    const strengthDiv = document.getElementById('password-strength');
    const matchDiv = document.getElementById('password-match');
    const resetBtn = document.getElementById('reset-btn');
    
    // Password strength checker
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        
        strengthDiv.innerHTML = `Password strength: <span class="strength-${strength.level}">${strength.text}</span>`;
        
        updateRequirements(password);
        checkPasswordMatch();
    });
    
    confirmInput.addEventListener('input', checkPasswordMatch);
    
    function checkPasswordStrength(password) {
        let score = 0;
        let feedback = [];
        
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;
        
        if (score < 3) return {level: 'weak', text: 'Weak'};
        if (score < 5) return {level: 'medium', text: 'Medium'};
        return {level: 'strong', text: 'Strong'};
    }
    
    function updateRequirements(password) {
        const requirements = [
            {id: 'req-length', test: password.length >= 8},
            {id: 'req-lowercase', test: /[a-z]/.test(password)},
            {id: 'req-uppercase', test: /[A-Z]/.test(password)},
            {id: 'req-number', test: /[0-9]/.test(password)},
            {id: 'req-special', test: /[@$!%*?&]/.test(password)}
        ];
        
        requirements.forEach(req => {
            const element = document.getElementById(req.id);
            if (req.test) {
                element.className = 'requirement-met';
            } else {
                element.className = 'requirement-not-met';
            }
        });
    }
    
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmInput.value;
        
        if (confirmPassword === '') {
            matchDiv.innerHTML = '';
            return;
        }
        
        if (password === confirmPassword) {
            matchDiv.innerHTML = '<span style="color: #28a745;">✓ Passwords match</span>';
        } else {
            matchDiv.innerHTML = '<span style="color: #dc3545;">✗ Passwords do not match</span>';
        }
    }
});
</script>

@endsection
