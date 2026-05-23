@extends('layouts.app')
@section('title', $myLapak ? 'Kelola Lapak' : 'Buka Lapak')
@section('page-title', $myLapak ? 'Kelola Lapakku' : 'Buka Lapak Baru')

@section('content')
<div style="max-width:560px;margin:0 auto;">

    {{-- Navigasi back --}}
    <a href="{{ route('pojok.index') }}" style="font-size:13px;color:var(--primary);display:inline-flex;align-items:center;gap:5px;margin-bottom:18px;">
        ← Kembali ke Pojok Jajan
    </a>

    @if(session('success'))
        <div class="notice" style="background:#ECFDF5;border-color:#6EE7B7;color:#065F46;margin-bottom:16px;">✅ {{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="notice" style="background:#FFF1F2;border-color:#FECDD3;color:#E11D48;margin-bottom:14px;">{{ $errors->first() }}</div>
    @endif

    {{-- Form lapak --}}
    <div class="card" style="margin-bottom:18px;">
        <div class="card-hd"><h3>{{ $myLapak ? '⚙️ Edit Info Lapak' : '🏠 Info Lapak' }}</h3></div>

        <form action="{{ route('pojok.simpan-lapak') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Foto toko --}}
            <div class="form-group">
                <label class="form-label">Foto Lapak</label>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:8px;">
                    <div id="fotoPreview" style="width:80px;height:80px;border-radius:10px;background:var(--bg);border:1.5px dashed var(--border);display:flex;align-items:center;justify-content:center;font-size:28px;overflow:hidden;">
                        @if($myLapak && $myLapak->foto_toko)
                            <img src="{{ Storage::url($myLapak->foto_toko) }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            🏠
                        @endif
                    </div>
                    <div>
                        <label for="foto_toko" style="padding:7px 14px;background:var(--bg);border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:12px;font-weight:600;cursor:pointer;display:inline-block;">
                            Pilih Foto
                        </label>
                        <div style="font-size:10px;color:var(--text-3);margin-top:4px;">JPG, PNG, WEBP • Maks 2MB</div>
                    </div>
                </div>
                <input type="file" id="foto_toko" name="foto_toko" accept="image/*" style="display:none;"
                       onchange="previewImg(this,'fotoPreview')">
            </div>

            <div class="form-group">
                <label class="form-label">Nama Toko <span style="color:#E11D48;">*</span></label>
                <input class="form-input" type="text" name="nama_toko"
                       value="{{ old('nama_toko', $myLapak->nama_toko ?? '') }}"
                       placeholder="Contoh: Dapur Kita, Warung Hemat...">
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Toko</label>
                <textarea class="form-input" name="deskripsi_toko" rows="3"
                          placeholder="Ceritakan sedikit tentang lapakmu...">{{ old('deskripsi_toko', $myLapak->deskripsi_toko ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Kontak (WhatsApp)</label>
                <input class="form-input" type="text" name="kontak"
                       value="{{ old('kontak', $myLapak->kontak ?? '') }}"
                       placeholder="Contoh: 08123456789">
            </div>

            <button type="submit" class="btn-primary">
                {{ $myLapak ? 'Simpan Perubahan' : '🚀 Buka Lapak Sekarang' }}
            </button>
        </form>
    </div>

    {{-- Daftar produk milik lapak ini --}}
    @if($myLapak)
    <div class="card">
        <div class="card-hd">
            <h3>📦 Produk di Lapakku ({{ $myLapak->produks->count() }})</h3>
            <a href="{{ route('pojok.tambah-produk') }}" class="btn-lapak" style="font-size:12px;padding:6px 12px;">+ Tambah Produk</a>
        </div>

        @forelse($myLapak->produks as $p)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
            <div style="width:50px;height:50px;border-radius:8px;background:var(--bg);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:22px;">
                @if($p->foto)
                    <img src="{{ Storage::url($p->foto) }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    {{ $p->jenis==='makanan'?'🍱':($p->jenis==='minuman'?'🧋':'📦') }}
                @endif
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $p->nama_produk }}</div>
                <div style="font-size:11px;color:var(--text-3);">{{ ucfirst($p->jenis) }} • Rp {{ number_format($p->harga,0,',','.') }}</div>
                <span class="badge {{ $p->status==='tersedia'?'badge-green':'badge-red' }}">{{ ucfirst($p->status) }}</span>
            </div>
            <form action="{{ route('pojok.hapus-produk',$p) }}" method="POST"
                  onsubmit="return confirm('Hapus produk ini?')">
                @csrf @method('DELETE')
                <button type="submit" style="padding:5px 10px;background:#FFF1F2;color:#E11D48;border:none;border-radius:var(--radius-sm);font-size:11px;font-weight:600;">Hapus</button>
            </form>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:var(--text-3);font-size:13px;">
            Belum ada produk. Tambahkan produk pertamamu!
        </div>
        @endforelse
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function previewImg(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
