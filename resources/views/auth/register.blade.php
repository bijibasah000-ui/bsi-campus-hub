<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — BSI Campus Hub</title>
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

        <div class="auth-title">Buat akun baru 🎓</div>
        <div class="auth-sub">Daftarkan dirimu untuk akses semua fitur</div>

        @if ($errors->any())
            <div class="notice" style="background:#FFF1F2;border-color:#FECDD3;color:#E11D48;margin-bottom:14px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Username</label>
                <input class="form-input" type="text" name="username"
                       value="{{ old('username') }}" placeholder="Username unik kamu">
            </div>
            <div class="form-group">
                <label class="form-label">NIM</label>
                <input class="form-input" type="text" name="nim"
                       value="{{ old('nim') }}" placeholder="Nomor Induk Mahasiswa">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="form-input" type="email" name="email"
                       value="{{ old('email') }}" placeholder="email@bsi.ac.id">
            </div>
            <div class="form-group">
                <label class="form-label">Jurusan</label>
                <select class="form-input" name="jurusan">
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach(['Teknik Informatika','Sistem Informasi','Manajemen','Akuntansi','Komunikasi'] as $j)
                        <option value="{{ $j }}" {{ old('jurusan')===$j?'selected':'' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Semester</label>
                <select class="form-input" name="semester">
                    <option value="">-- Pilih Semester --</option>
                    @for($s=1;$s<=8;$s++)
                        <option value="{{ $s }}" {{ old('semester')==$s?'selected':'' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input class="form-input" type="password" name="password" placeholder="Min. 6 karakter">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input class="form-input" type="password" name="password_confirmation" placeholder="Ulangi password">
            </div>
            <button type="submit" class="btn-primary">Daftar Sekarang</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
        </div>
    </div>
</div>
</body>
</html>
