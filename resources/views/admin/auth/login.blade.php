<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@lang('login.login') | PIAM</title>
    <link href="{{ asset('assets/admin/imgs/logo.png') }}" rel="icon" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --piam-dark:  #1c2a38;
            --piam-dark2: #243447;
            --piam-red:   #D01C1F;
            --piam-green: #00A95C;
            --piam-gold:  #FFD700;
            --piam-white: #ffffff;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--piam-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* ── Background geometric pattern ─────────────────────────── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(0, 169, 92, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(208, 28, 31, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 60% 80%, rgba(255, 215, 0, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Main wrapper ─────────────────────────────────────────── */
        .login-wrap {
            position: relative;
            z-index: 1;
            display: flex;
            width: 900px;
            max-width: 95vw;
            min-height: 540px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        /* ── Left branding panel ──────────────────────────────────── */
        .login-brand {
            flex: 0 0 340px;
            background: linear-gradient(160deg, var(--piam-dark2) 0%, var(--piam-dark) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 36px;
            position: relative;
            overflow: hidden;
        }

        [dir="rtl"] .login-brand {
            order: 1;
        }

        .login-brand::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255, 215, 0, 0.04);
        }
        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -40px; left: -40px;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: rgba(0, 169, 92, 0.06);
        }

        /* Flag stripe bar at top of brand panel */
        .flag-stripe {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            display: flex;
        }
        .flag-stripe span {
            flex: 1;
        }
        .flag-stripe .s-green { background: var(--piam-green); }
        .flag-stripe .s-gold  { background: var(--piam-gold); }
        .flag-stripe .s-red   { background: var(--piam-red); }

        .brand-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border-radius: 50%;
            border: 3px solid rgba(255, 215, 0, 0.3);
            padding: 6px;
            background: rgba(255,255,255,0.04);
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .brand-title {
            color: var(--piam-gold);
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }

        .brand-subtitle {
            color: rgba(255,255,255,0.55);
            font-size: 0.8rem;
            font-weight: 400;
            text-align: center;
            margin-top: 10px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .brand-desc {
            color: rgba(255,255,255,0.30);
            font-size: 0.72rem;
            text-align: center;
            margin-top: 6px;
            position: relative;
            z-index: 1;
        }

        .brand-divider {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, var(--piam-red), var(--piam-gold), var(--piam-green));
            border-radius: 2px;
            margin: 18px auto;
            position: relative;
            z-index: 1;
        }

        /* Bottom dots decoration */
        .brand-dots {
            display: flex;
            gap: 6px;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }
        .brand-dots span {
            width: 8px; height: 8px;
            border-radius: 50%;
        }
        .brand-dots .d-red   { background: var(--piam-red);   opacity: 0.8; }
        .brand-dots .d-gold  { background: var(--piam-gold);  opacity: 0.8; }
        .brand-dots .d-green { background: var(--piam-green); opacity: 0.8; }

        /* ── Right form panel ─────────────────────────────────────── */
        .login-form-panel {
            flex: 1;
            background: #f7f9fc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 44px;
            position: relative;
        }

        /* Language switcher */
        .lang-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 6px;
        }
        [dir="rtl"] .lang-switcher { right: auto; left: 20px; }

        .lang-btn {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: 'Cairo', sans-serif;
            text-decoration: none;
            border: 1.5px solid transparent;
            transition: all 0.2s;
            cursor: pointer;
        }
        .lang-btn.active {
            background: var(--piam-dark);
            color: var(--piam-gold);
            border-color: var(--piam-dark);
        }
        .lang-btn:not(.active) {
            background: transparent;
            color: #8a9ab5;
            border-color: #d1d9e6;
        }
        .lang-btn:not(.active):hover {
            border-color: var(--piam-green);
            color: var(--piam-green);
        }

        .form-heading {
            text-align: center;
            margin-bottom: 32px;
            width: 100%;
        }
        .form-heading h2 {
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--piam-dark);
            margin-bottom: 6px;
        }
        .form-heading p {
            font-size: 0.82rem;
            color: #8a9ab5;
        }

        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 18px;
        }
        .input-group input {
            width: 100%;
            height: 50px;
            border: 1.5px solid #d1d9e6;
            border-radius: 10px;
            padding: 0 16px 0 46px;
            font-family: 'Cairo', sans-serif;
            font-size: 0.9rem;
            color: var(--piam-dark);
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        [dir="rtl"] .input-group input {
            padding: 0 46px 0 16px;
        }
        .input-group input:focus {
            border-color: var(--piam-green);
            box-shadow: 0 0 0 3px rgba(0, 169, 92, 0.12);
        }
        .input-group input::placeholder { color: #b0bdd0; }
        .input-group .input-icon {
            position: absolute;
            top: 50%; left: 16px;
            transform: translateY(-50%);
            color: #b0bdd0;
            font-size: 0.85rem;
            pointer-events: none;
            transition: color 0.2s;
        }
        [dir="rtl"] .input-group .input-icon { left: auto; right: 16px; }
        .input-group input:focus ~ .input-icon { color: var(--piam-green); }

        .error-msg {
            display: block;
            margin-top: -12px;
            margin-bottom: 10px;
            font-size: 0.77rem;
            color: var(--piam-red);
        }

        .alert-error {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            background: rgba(208, 28, 31, 0.08);
            border: 1px solid rgba(208, 28, 31, 0.2);
            color: var(--piam-red);
            font-size: 0.83rem;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, var(--piam-green), #009950);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Cairo', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            margin-top: 6px;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(0, 169, 92, 0.25);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 169, 92, 0.35);
        }
        .btn-login:active { transform: translateY(0); }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.75rem;
            color: #b0bdd0;
        }

        /* ── Responsive ───────────────────────────────────────────── */
        @media (max-width: 680px) {
            .login-wrap { flex-direction: column; border-radius: 16px; }
            .login-brand { flex: none; padding: 32px 24px; min-height: auto; }
            .login-form-panel { padding: 36px 24px; }
            [dir="rtl"] .login-brand { order: unset; }
        }
    </style>
</head>
<body>
<div class="login-wrap">

    {{-- ── Branding panel ──────────────────────────────────────── --}}
    <div class="login-brand">
        <div class="flag-stripe">
            <span class="s-green"></span>
            <span class="s-gold"></span>
            <span class="s-red"></span>
        </div>
        <img src="{{ asset('assets/admin/imgs/logo.png') }}" class="brand-logo" alt="PIAM Logo">
        <div class="brand-title">PIAM</div>
        <div class="brand-divider"></div>
        @if(app()->getLocale() == 'ar')
            <div class="brand-subtitle">منصة متكاملة لرصد<br>المياه والصرف الصحي</div>
            <div class="brand-desc">الجمهورية الإسلامية الموريتانية</div>
        @else
            <div class="brand-subtitle">Plateforme Intégrée pour la<br>surveillance AEPHA</div>
            <div class="brand-desc">République Islamique de Mauritanie</div>
        @endif
        <div class="brand-dots">
            <span class="d-red"></span>
            <span class="d-gold"></span>
            <span class="d-green"></span>
        </div>
    </div>

    {{-- ── Form panel ──────────────────────────────────────────── --}}
    <div class="login-form-panel">

        {{-- Language switcher --}}
        <div class="lang-switcher">
            @foreach(LaravelLocalization::getSupportedLocales() as $code => $props)
                <a href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}"
                   class="lang-btn {{ app()->getLocale() == $code ? 'active' : '' }}">
                    {{ strtoupper($code) }}
                </a>
            @endforeach
        </div>

        <div class="form-heading">
            <h2>@lang('login.login')</h2>
            <p>{{ app()->getLocale() == 'ar' ? 'أدخل بيانات اعتمادك للمتابعة' : 'Entrez vos identifiants pour continuer' }}</p>
        </div>

        @if(Session::has('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ Session::get('error') }}
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="post" style="width:100%">
            @csrf

            <div class="input-group">
                <input type="email" name="email" placeholder="@lang('login.email')"
                       value="{{ old('email') }}" autocomplete="email">
                <i class="fas fa-envelope input-icon"></i>
            </div>
            @error('email')
                <span class="error-msg"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</span>
            @enderror

            <div class="input-group">
                <input type="password" name="password" placeholder="@lang('login.password')"
                       autocomplete="current-password">
                <i class="fas fa-lock input-icon"></i>
            </div>
            @error('password')
                <span class="error-msg"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</span>
            @enderror

            <button type="submit" class="btn-login">
                @lang('login.enter')
            </button>
        </form>

        <div class="login-footer">
            PIAM &copy; {{ date('Y') }} — Ministère de l'Hydraulique et de l'Assainissement
        </div>
    </div>

</div>

<script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
