@extends('layouts.app')
@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
<div class="profile-box">
    <div class="card">

        {{-- Avatar besar di tengah dengan preview foto --}}
        <div style="text-align:center; padding: 16px 0 14px;">

            {{-- Avatar / Foto profil --}}
            <div style="position:relative; display:inline-block; margin-bottom:8px;">
                @if(Auth::user()->foto)
                    <img id="fotoPreview"
                         src="{{ asset('storage/foto-profil/' . Auth::user()->foto) }}"
                         alt="Foto Profil"
                         style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid var(--primary, #6366f1);">
                @else
                    <div class="profile-av-center" id="fotoPreview" style="width:80px; height:80px; font-size:28px; display:flex; align-items:center; justify-content:center;">
                        {{ strtoupper(substr(Auth::user()->username ?? 'MH', 0, 2)) }}
                    </div>
                @endif

                {{-- Tombol kamera overlay --}}
                <label for="fotoInput" title="Ganti foto profil (JPG/PNG/GIF/WEBP)" style="
                    position:absolute; bottom:0; right:0;
                    background:var(--primary, #6366f1); color:#fff;
                    border-radius:50%; width:26px; height:26px;
                    display:flex; align-items:center; justify-content:center;
                    cursor:pointer; box-shadow:0 2px 6px rgba(0,0,0,0.2);
                ">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                </label>
            </div>

            <div style="font-size:13px; color:var(--text-3); margin-bottom:4px;">
                Klik ikon kamera untuk ganti foto &bull; Support GIF animasi
            </div>

            <div style="font-size:15px; font-weight:700; color:var(--text);">
                {{ Auth::user()->username ?? 'Nama Mahasiswa' }}
            </div>
            <div style="font-size:11px; color:var(--text-3); margin-top:2px;">
                {{ Auth::user()->email ?? 'email@bsi.ac.id' }}
            </div>
            <div style="display:flex; gap:6px; justify-content:center; margin-top:8px; flex-wrap:wrap;">
                @if(Auth::user()->jurusan)
                    <span class="badge badge-blue">{{ Auth::user()->jurusan }}</span>
                @endif
                @if(Auth::user()->semester)
                    <span class="badge badge-amber">Semester {{ Auth::user()->semester }}</span>
                @endif
                @if(Auth::user()->jenis_kelamin)
                    <span class="badge badge-blue">{{ Auth::user()->jenis_kelamin }}</span>
                @endif
            </div>
        </div>

        <hr class="divider">

        {{-- Alert sukses --}}
        @if(session('success'))
            <div style="background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Alert error --}}
        @if($errors->any())
            <div style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px;">
                @foreach($errors->all() as $err)
                    <div>⚠ {{ $err }}</div>
                @endforeach
            </div>
        @endif

        {{-- Form edit profil --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- Input foto (hidden, dipicu tombol kamera) --}}
            <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;">

            <div class="form-group">
                <label class="form-label">Nama Lengkap / Username</label>
                <input class="form-input" type="text" name="username"
                       value="{{ old('username', Auth::user()->username ?? '') }}"
                       placeholder="Nama lengkap">
            </div>

            <div class="form-group">
                <label class="form-label">NIM</label>
                <input class="form-input" type="text"
                       value="{{ Auth::user()->nim ?? '' }}"
                       placeholder="NIM" readonly
                       style="background:var(--bg-2, #f3f4f6); cursor:not-allowed; opacity:0.7;">
                <small style="color:var(--text-3); font-size:11px;">NIM tidak dapat diubah</small>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="form-input" type="email" name="email"
                       value="{{ old('email', Auth::user()->email ?? '') }}"
                       placeholder="Email">
            </div>

            <div class="form-group">
                <label class="form-label">Jurusan</label>
                <input class="form-input" type="text" name="jurusan"
                       value="{{ old('jurusan', Auth::user()->jurusan ?? '') }}"
                       placeholder="Contoh: Teknik Informatika">
            </div>

            <div class="form-group">
                <label class="form-label">Semester</label>
                <select class="form-input" name="semester">
                    <option value="">-- Pilih Semester --</option>
                    @for ($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ (old('semester', Auth::user()->semester)) == $s ? 'selected' : '' }}>
                            Semester {{ $s }}
                        </option>
                    @endfor
                </select>
            </div>

            {{-- Tanggal Lahir --}}
            <div class="form-group">
                <label class="form-label">Tanggal Lahir</label>
                <input class="form-input" type="date" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', Auth::user()->tanggal_lahir ?? '') }}">
            </div>

            {{-- Jenis Kelamin --}}
            <div class="form-group">
                <label class="form-label">Jenis Kelamin</label>
                <select class="form-input" name="jenis_kelamin">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-laki" {{ (old('jenis_kelamin', Auth::user()->jenis_kelamin)) == 'Laki-laki' ? 'selected' : '' }}>
                        Laki-laki
                    </option>
                    <option value="Perempuan" {{ (old('jenis_kelamin', Auth::user()->jenis_kelamin)) == 'Perempuan' ? 'selected' : '' }}>
                        Perempuan
                    </option>
                </select>
            </div>

            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </form>

    </div>
</div>

@push('scripts')
<script>
// Preview foto sebelum upload (termasuk GIF animasi)
document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const maxSize = 4 * 1024 * 1024; // 4MB
    if (file.size > maxSize) {
        alert('Ukuran file terlalu besar. Maksimal 4MB.');
        this.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(ev) {
        const container = document.getElementById('fotoPreview');
        // Kalau sebelumnya div (inisial), ganti jadi img
        if (container.tagName === 'DIV') {
            const img = document.createElement('img');
            img.id = 'fotoPreview';
            img.style.cssText = 'width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid var(--primary, #6366f1);';
            img.alt = 'Foto Profil';
            container.parentNode.replaceChild(img, container);
        }
        document.getElementById('fotoPreview').src = ev.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
@endpush

@endsection