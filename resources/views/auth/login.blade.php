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
    </div>
</div>
</body>
</html>
