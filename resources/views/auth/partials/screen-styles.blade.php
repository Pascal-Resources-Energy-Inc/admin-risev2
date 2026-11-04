<style>
:root {
    --auth-ink: #142b3d;
    --auth-muted: #718397;
    --auth-primary: #0799c4;
    --auth-primary-dark: #087ca8;
    --auth-line: #dbe6ee;
}

body {
    background: #f2f7fa;
    font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

.auth-screen,
.auth-screen * {
    box-sizing: border-box;
}

.auth-screen {
    min-height: 100vh;
    min-height: 100svh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: clamp(18px, 4vw, 56px);
    background:
        radial-gradient(ellipse at 8% 8%, rgba(71, 194, 221, .12), transparent 34%),
        radial-gradient(ellipse at 96% 94%, rgba(12, 148, 190, .09), transparent 32%),
        #f2f7fa;
}

.auth-card {
    width: min(100%, 1080px);
    min-height: min(680px, calc(100svh - 48px));
    display: grid;
    grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(216, 230, 238, .9);
    border-radius: 26px;
    box-shadow: 0 24px 70px rgba(28, 66, 91, .12), 0 3px 12px rgba(28, 66, 91, .04);
}

.auth-brand-panel {
    position: relative;
    isolation: isolate;
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 54px 44px 38px;
    background: linear-gradient(145deg, #dff6fb 0%, #edf9fb 44%, #e5f1fa 100%);
}

.auth-brand-panel::before,
.auth-brand-panel::after {
    position: absolute;
    z-index: -1;
    content: "";
    border-radius: 50%;
    background: rgba(255, 255, 255, .64);
}

.auth-brand-panel::before {
    width: 340px;
    height: 340px;
    top: -130px;
    left: -120px;
}

.auth-brand-panel::after {
    width: 430px;
    height: 430px;
    right: -210px;
    bottom: -250px;
    background: rgba(31, 177, 210, .11);
}

.brand-glow {
    position: absolute;
    z-index: -1;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #35bddd;
    opacity: .35;
}

.brand-glow-one { top: 22%; right: 18%; }
.brand-glow-two { bottom: 23%; left: 15%; width: 9px; height: 9px; }

.brand-image-wrap {
    width: min(100%, 355px);
    height: 350px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-illustration {
    display: block;
    width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 16px 20px rgba(41, 104, 128, .10));
}

.brand-caption {
    max-width: 330px;
    margin: 8px 0 0;
    color: #3e7187;
    font-size: 15px;
    line-height: 1.6;
    text-align: center;
}

.auth-form-panel {
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: clamp(36px, 6vw, 76px);
}

.landing-copy .auth-logo {
    width: min(240px, 70%);
    height: 74px;
    margin-bottom: 48px;
    object-fit: contain;
    object-position: left center;
}

.auth-eyebrow {
    margin: 0 0 12px;
    color: var(--auth-primary);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .16em;
}

.auth-form-panel h1 {
    margin: 0;
    color: var(--auth-ink);
    font-size: clamp(27px, 3vw, 36px);
    font-weight: 750;
    letter-spacing: -.04em;
    line-height: 1.16;
}

.auth-description {
    margin: 12px 0 30px;
    color: var(--auth-muted);
    font-size: 15px;
    line-height: 1.65;
}

.signin-button {
    width: 100%;
    min-height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 12px 20px;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(100deg, #0799c4, #12acd0);
    box-shadow: 0 8px 18px rgba(7, 153, 196, .2);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
}

.signin-button:hover {
    transform: translateY(-1px);
    background: linear-gradient(100deg, #087ca8, #0799c4);
    box-shadow: 0 11px 22px rgba(7, 153, 196, .26);
}

.signin-button:focus-visible,
.back-btn:focus-visible,
.password-toggle:focus-visible,
.form-input:focus-visible,
.forgot-link:focus-visible {
    outline: 3px solid rgba(7, 153, 196, .25);
    outline-offset: 3px;
}

.signin-button svg,
.back-btn svg,
.password-toggle svg,
.role-check {
    width: 20px;
    height: 20px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.auth-footnote {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin: 24px 0 0;
    color: #8a9bab;
    font-size: 12px;
}

.secure-dot {
    width: 7px;
    height: 7px;
    flex: 0 0 7px;
    border-radius: 50%;
    background: #26b77a;
    box-shadow: 0 0 0 3px rgba(38, 183, 122, .12);
}

.form-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 0 0 36px;
}

.back-btn {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--auth-line);
    border-radius: 12px;
    background: #fff;
    color: #446176;
    cursor: pointer;
    transition: border-color .18s ease, color .18s ease, background .18s ease;
}

.back-btn:hover { border-color: #9ed9e9; color: var(--auth-primary-dark); background: #f4fcfe; }
.form-logo { width: 88px; height: 42px; object-fit: contain; }
.form-heading .auth-description { margin-bottom: 23px; }

.role-indicator {
        display: flex;
align-items: center;
    gap: 12px;
    min-height: 62px;
    margin: 0 0 26px;
    padding: 10px 14px;
    border: 1px solid #d8eef4;
    border-radius: 14px;
    background: #f5fbfd;
}

.admin-avatar {
    width: 38px;
    height: 38px;
    display: grid;
    flex: 0 0 38px;
    place-items: center;
    border-radius: 12px;
    background: #dff5fa;
    color: #087fa5;
    font-size: 15px;
    font-weight: 800;
}

.role-label,
.role-name { display: block; }
.role-label { color: #7c91a1; font-size: 11px; line-height: 1.4; }
.role-name { margin-top: 2px; color: #24475b; font-size: 14px; font-weight: 700; }
.role-check { margin-left: auto; color: #25a878; }

.input-group { margin-bottom: 20px; }
.label-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
.input-label { display: block; margin: 0; color: #344c5e; font-size: 13px; font-weight: 650; }

.form-input {
    width: 100%;
    height: 52px;
    padding: 0 15px;
    border: 1px solid var(--auth-line);
    border-radius: 11px;
    background: #fbfdfe;
    color: var(--auth-ink);
    font-size: 15px;
    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}

.form-input::placeholder { color: #a1afba; }
.form-input:hover { border-color: #b8cbd7; }
.form-input:focus { border-color: #39b3d2; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(7, 153, 196, .1); }
.password-wrap { position: relative; width: 100%}
.password-wrap .form-input { padding-right: 52px; }
.password-toggle { position: absolute; top: 50%; right: 8px; width: 38px; height: 38px; display: grid; place-items: center; transform: translateY(-50%); border: 0; border-radius: 9px; background: transparent; color: #7d91a0; cursor: pointer; }
.password-toggle:hover { background: #edf7fa; color: var(--auth-primary-dark); }
.password-toggle svg { width: 19px; height: 19px; }
.forgot-link { color: var(--auth-primary-dark); font-size: 12px; font-weight: 650; text-decoration: none; }
.forgot-link:hover { text-decoration: underline; }
.submit-button { margin-top: 8px; }
.submit-button:disabled { cursor: wait; opacity: .8; transform: none; }
.form-footnote { margin-top: 18px; }

@media (max-width: 760px) {
    .auth-screen { align-items: flex-start; padding: 20px; }
    .auth-card { width: min(100%, 560px); min-height: 0; grid-template-columns: 1fr; border-radius: 22px; }
    .auth-brand-panel { min-height: 195px; padding: 18px 26px 20px; }
    .brand-image-wrap { width: 190px; height: 142px; }
    .brand-caption { margin-top: 0; font-size: 13px; }
    .auth-form-panel { padding: 32px clamp(22px, 7vw, 48px) 36px; }
    .landing-copy .auth-logo { height: 54px; margin-bottom: 27px; }
    .landing-copy .auth-description { margin-bottom: 22px; }
    .form-topbar { margin-bottom: 28px; }
    .form-heading .auth-description { margin-bottom: 20px; }
}

@media (max-width: 420px) {
    .auth-screen { padding: 12px; }
    .auth-card { border-radius: 18px; }
    .auth-brand-panel { min-height: 160px; }
    .brand-image-wrap { width: 155px; height: 112px; }
    .auth-form-panel { padding: 25px 20px 28px; }
    .auth-form-panel h1 { font-size: 27px; }
    .form-topbar { margin-bottom: 24px; }
    .input-group { margin-bottom: 17px; }
    .form-input { height: 50px; font-size: 16px; }
    .signin-button { min-height: 52px; }
}

@media (max-height: 650px) and (min-width: 761px) {
    .auth-screen { align-items: flex-start; }
    .auth-card { min-height: 0; }
    .auth-brand-panel { padding-top: 30px; padding-bottom: 24px; }
    .brand-image-wrap { height: min(42vh, 290px); }
    .auth-form-panel { padding-top: 34px; padding-bottom: 34px; }
    .form-topbar { margin-bottom: 22px; }
}

@media (prefers-reduced-motion: reduce) {
    .auth-screen *, .auth-screen *::before, .auth-screen *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
}
</style>