@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="login-page auth-screen" id="loginFormPage">
    <div class="auth-card login-card">
        <section class="auth-brand-panel login-brand-panel">
            <div class="brand-glow brand-glow-one"></div>
            <div class="brand-glow brand-glow-two"></div>
            <div class="brand-image-wrap">
                <img src="{{ asset('images/admin-login.png') }}" alt="" class="auth-illustration">
            </div>
            <p class="brand-caption">Keep your business moving with one connected workspace.</p>
        </section>

        <section class="auth-form-panel">
            <div class="form-topbar form-topbar-single">
                <img src="{{ asset('images/logo_nya.png') }}" alt="GazLite" class="form-logo">
            </div>

            <div class="form-heading">
                <p class="auth-eyebrow">ADMIN PORTAL</p>
                <h1>Sign in to your account</h1>
                <p class="auth-description">Enter your credentials to continue.</p>
            </div>

            <div class="role-indicator" id="roleIndicator">
                <span class="admin-avatar" aria-hidden="true">A</span>
                <span><span class="role-label">Signing in as</span><strong class="role-name" id="selectedRoleName">Admin</strong></span>
                <svg class="role-check" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
            </div>

            <form id="loginForm" aria-label="{{ __('Login') }}">
                @csrf
                <input type="hidden" name="selected_role" id="selectedRoleInput" value="Admin">

                <div class="input-group">
                    <label for="email" class="input-label">Email or phone number</label>
                    <input
                        id="email"
                        type="text"
                        class="form-input"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="name@example.com or phone number"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="input-group">
                    <div class="label-row">
                        <label class="input-label" for="password">Password</label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    </div>
                    <div class="password-wrap">
                        <input
                            id="password"
                            type="password"
                            class="form-input"
                            placeholder="Enter your password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >
                        <button class="password-toggle" type="button" id="passwordToggle" aria-label="Show password" aria-pressed="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                        </button>
                    </div>
                </div>

                <button class="signin-button submit-button" type="submit" id="signinButton">
                    <span>Sign in as Admin</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
                <p class="auth-footnote form-footnote"><span class="secure-dot"></span> Your sign-in is protected and encrypted</p>
            </form>
        </section>
    </div>
</div>
@include('auth.partials.screen-styles')
<script>
    document.getElementById('passwordToggle').addEventListener('click', function () {
        const password = document.getElementById('password');
        const isVisible = password.type === 'text';
        password.type = isVisible ? 'password' : 'text';
        this.setAttribute('aria-pressed', String(!isVisible));
        this.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
    });

    document.getElementById('loginForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn = document.getElementById('signinButton');
        const formData = new FormData(this);

        btn.disabled = true;
        btn.querySelector("span").textContent = "Signing in as: Admin";

        try {
            const res = await fetch("{{ route('login') }}", {
                method: "POST",
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                Swal.fire("Error", data.message, "error");
            }

        } catch (err) {
            Swal.fire("Error", "Server error", "error");
        }

        btn.disabled = false;
        btn.querySelector("span").textContent = "Sign in as Admin";
    });
</script>

@endsection
