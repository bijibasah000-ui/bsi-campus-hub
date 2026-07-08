<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BSI Campus Hub</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="overflow:auto;">
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3L2 9l10 6 10-6-10-6zM2 15l10 6 10-6M2 9l10 6 10-6"/></svg>
            </div>
            <h1>BSI Campus Hub</h1>
            <p>Universitas BSI</p>
        </div>

        <div class="auth-title">Selamat datang kembali 👋</div>
        <div class="auth-sub">Masuk dengan NIM dan password kamu</div>

        @if ($errors->any())
            <div class="notice" style="background:#FFF1F2;border-color:#FECDD3;color:#E11D48;margin-bottom:14px;">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="notice" style="margin-bottom:14px;">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">NIM</label>
                <input class="form-input" type="text" name="nim"
                       value="{{ old('nim') }}" placeholder="Contoh: 22410100123" autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input class="form-input" type="password" name="password" placeholder="••••••••">
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-2);cursor:pointer;">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>
            <button type="submit" class="btn-primary">Masuk</button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </div>

        {{-- PATCH: Link Login Administrator --}}
        <div class="auth-footer" style="margin-top:16px; padding-top:12px; border-top:1px solid #e5e7eb;">
            <a href="{{ route('admin.login') }}" style="font-size:11px; color:#9ca3af; display:inline-flex; align-items:center; gap:4px; text-decoration:none;"
               onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#9ca3af'">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Login Administrator
            </a>
        </div>
    </div>
</div>
</body>
</html>
