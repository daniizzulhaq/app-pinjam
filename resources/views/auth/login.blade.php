<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — DanaKita</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .wrap {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 580px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }

        /* ── LEFT PANEL ── */
        .left {
            flex: 1;
            background: #0a1628;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .left::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(37,99,235,0.15);
            pointer-events: none;
        }
        .left::after {
            content: '';
            position: absolute;
            bottom: -40px; left: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(16,185,129,0.1);
            pointer-events: none;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        .brand-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            display: flex; align-items: center; justify-content: center;
        }
        .brand-icon svg { width: 20px; height: 20px; fill: none; stroke: white; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .brand-name { font-family: 'Playfair Display', serif; font-size: 18px; color: white; }
        .brand-tagline { font-size: 11px; color: rgba(255,255,255,0.4); letter-spacing: 1.4px; text-transform: uppercase; margin-top: 2px; }

        .left-hero { position: relative; z-index: 1; }
        .left-hero h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px; color: white;
            line-height: 1.35; margin-bottom: 12px;
        }
        .left-hero p { font-size: 13px; color: rgba(255,255,255,0.5); line-height: 1.7; }

        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            position: relative; z-index: 1;
        }
        .stat {
            background: rgba(255,255,255,0.05);
            border: 0.5px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 14px 16px;
        }
        .stat-num { font-family: 'Playfair Display', serif; font-size: 20px; color: white; font-weight: 700; }
        .stat-label { font-size: 11px; color: rgba(255,255,255,0.4); margin-top: 2px; }

        /* ── RIGHT PANEL ── */
        .right {
            flex: 1.1;
            background: #ffffff;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header { margin-bottom: 32px; }
        .form-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 26px; color: #0f172a;
            font-weight: 600; margin-bottom: 6px;
        }
        .form-header p { font-size: 13px; color: #64748b; }

        /* Session status */
        .session-status {
            background: #f0fdf4;
            border: 0.5px solid #86efac;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #166534;
            margin-bottom: 20px;
        }

        /* Fields */
        .field { margin-bottom: 20px; }
        .field label {
            display: block;
            font-size: 12px; font-weight: 500;
            color: #64748b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .field input {
            width: 100%; height: 44px;
            border: 0.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 0 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px; color: #0f172a;
            background: #ffffff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .field input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .field input.is-invalid { border-color: #ef4444; }

        .field-pw { position: relative; }
        .field-pw input { padding-right: 44px; }
        .eye-btn {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer; color: #94a3b8;
            display: flex; align-items: center; padding: 4px;
            line-height: 1;
        }
        .eye-btn:hover { color: #64748b; }

        /* Error messages */
        .error-msg { font-size: 12px; color: #ef4444; margin-top: 6px; }

        /* Row: remember + forgot */
        .row {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember {
            display: flex; align-items: center;
            gap: 8px; font-size: 13px;
            color: #64748b; cursor: pointer;
        }
        .remember input[type="checkbox"] {
            width: 16px; height: 16px;
            border-radius: 4px;
            accent-color: #2563eb;
            cursor: pointer;
        }
        .forgot { font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500; }
        .forgot:hover { text-decoration: underline; }

        /* Buttons */
        .btn-login {
            width: 100%; height: 46px;
            background: #2563eb; color: white;
            border: none; border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px; font-weight: 500;
            cursor: pointer; letter-spacing: 0.3px;
            transition: background 0.15s, transform 0.1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { background: #1d4ed8; }
        .btn-login:active { transform: scale(0.99); }

        .divider {
            display: flex; align-items: center;
            gap: 12px; margin: 24px 0;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1;
            height: 0.5px; background: #e2e8f0;
        }
        .divider span { font-size: 12px; color: #94a3b8; }

        .register {
            text-align: center;
            margin-top: 24px;
            font-size: 13px; color: #64748b;
        }
        .register a { color: #2563eb; font-weight: 500; text-decoration: none; }
        .register a:hover { text-decoration: underline; }

        .trust {
            display: flex; align-items: center;
            gap: 6px; margin-top: 20px;
            padding-top: 20px;
            border-top: 0.5px solid #f1f5f9;
        }
        .trust-dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; flex-shrink: 0; }
        .trust-text { font-size: 11px; color: #94a3b8; letter-spacing: 0.2px; }

        /* Responsive */
        @media (max-width: 640px) {
            .left { display: none; }
            .right { padding: 36px 28px; }
            .wrap { border-radius: 12px; }
        }
    </style>
</head>
<body>

<div class="wrap">

    {{-- LEFT PANEL --}}
    <div class="left">
        <div>
            <div class="brand-mark">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24"><polyline points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                </div>
                <div>
                    <div class="brand-name">DanaKita</div>
                    <div class="brand-tagline">Platform Pinjam Uang</div>
                </div>
            </div>
            <div class="left-hero">
                <h2>Solusi keuangan cepat &amp; terpercaya</h2>
                <p>Ajukan pinjaman dalam hitungan menit. Dana cair langsung ke rekening Anda.</p>
            </div>
        </div>
        <div class="stats">
            <div class="stat">
                <div class="stat-num">500K+</div>
                <div class="stat-label">Pengguna aktif</div>
            </div>
            <div class="stat">
                <div class="stat-num">2 Menit</div>
                <div class="stat-label">Proses persetujuan</div>
            </div>
            <div class="stat">
                <div class="stat-num">Rp 50 Jt</div>
                <div class="stat-label">Batas pinjaman</div>
            </div>
            <div class="stat">
                <div class="stat-num">OJK</div>
                <div class="stat-label">Terdaftar &amp; diawasi</div>
            </div>
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="right">

        {{-- Session Status --}}
        @if (session('status'))
            <div class="session-status">{{ session('status') }}</div>
        @endif

        <div class="form-header">
            <h1>Masuk ke akun</h1>
            <p>Selamat datang kembali. Silakan masuk untuk melanjutkan.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="field">
                <label for="email">Alamat email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    autocomplete="username"
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                    required
                    autofocus
                />
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="field">
                <label for="password">Kata sandi</label>
                <div class="field-pw">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Masukkan kata sandi"
                        autocomplete="current-password"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        required
                    />
                    <button type="button" class="eye-btn" id="toggle-pw" aria-label="Tampilkan kata sandi">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me + Forgot Password --}}
            <div class="row">
                <label class="remember">
                    <input type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot">Lupa kata sandi?</a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Masuk sekarang
            </button>

            <div class="divider"><span>atau</span></div>

            {{-- Register link --}}
            <div class="register">
                Belum punya akun?
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Daftar sekarang</a>
                @endif
            </div>

            <div class="trust">
                <div class="trust-dot"></div>
                <span class="trust-text">Enkripsi SSL 256-bit &middot; Data Anda aman dan terenkripsi</span>
            </div>
        </form>

    </div>
</div>

<script>
    const btn = document.getElementById('toggle-pw');
    const pw  = document.getElementById('password');
    const ico = document.getElementById('eye-icon');

    const eyeOpen  = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    const eyeClose = '<line x1="1" y1="1" x2="23" y2="23"/><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>';

    btn.addEventListener('click', () => {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        ico.innerHTML = show ? eyeClose : eyeOpen;
        btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
</script>

</body>
</html>