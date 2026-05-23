@extends('layouts.app')
@section('title','Tambah Produk')
@section('page-title','Tambah Produk Baru')

@section('content')
<div style="max-width:520px;margin:0 auto;">

    <a href="{{ route('pojok.buka-lapak') }}" style="font-size:13px;color:var(--primary);display:inline-flex;align-items:center;gap:5px;margin-bottom:18px;">
        ← Kembali ke Lapakku
    </a>

    @if ($errors->any())
        <div class="notice" style="background:#FFF1F2;border-color:#FECDD3;color:#E11D48;margin-bottom:14px;">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <div class="card-hd"><h3>📦 Detail Produk</h3></div>

        <form action="{{ route('pojok.simpan-produk') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Foto produk --}}
            <div class="form-group">
                <label class="form-label">Foto Produk</label>
                <div style="display:flex;align-items:center;gap:14px;">
                    <div id="produkPreview" style="width:90px;height:90px;border-radius:10px;background:var(--bg);border:1.5px dashed var(--border);display:flex;align-items:center;justify-content:center;font-size:36px;overflow:hidden;">
                        📷
                    </div>
                    <div>
                        <label for="foto" style="padding:7px 14px;background:var(--bg);border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:12px;font-weight:600;cursor:pointer;display:inline-block;">
                            Upload Foto
                        </label>
                        <div style="font-size:10px;color:var(--text-3);margin-top:4px;">JPG, PNG, WEBP • Maks 2MB</div>
                    </div>
                </div>
                <input type="file" id="foto" name="foto" accept="image/*" style="display:none;"
                       onchange="previewImg(this,'produkPreview')">
            </div>

            <div class="form-group">
                <label class="form-label">Nama Produk <span style="color:#E11D48;">*</span></label>
                <input class="form-input" type="text" name="nama_produk"
                       value="{{ old('nama_produk') }}" placeholder="Contoh: Nasi Box Ayam Geprek">
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Produk <span style="color:#E11D48;">*</span></label>
                <select class="form-input" name="jenis">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="makanan" {{ old('jenis')==='makanan'?'selected':'' }}>🍔 Makanan</option>
                    <option value="minuman" {{ old('jenis')==='minuman'?'selected':'' }}>🧋 Minuman</option>
                    <option value="barang"  {{ old('jenis')==='barang' ?'selected':'' }}>📦 Barang</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Harga (Rp) <span style="color:#E11D48;">*</span></label>
                <input class="form-input" type="number" name="harga"
                       value="{{ old('harga') }}" placeholder="Contoh: 15000" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Produk</label>
                <textarea class="form-input" name="deskripsi" rows="3"
                          placeholder="Bahan, rasa, porsi, atau info tambahan lainnya...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-input" name="status">
                    <option value="tersedia" {{ old('status','tersedia')==='tersedia'?'selected':'' }}>✅ Tersedia</option>
                    <option value="habis"    {{ old('status')==='habis'?'selected':'' }}>❌ Habis</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">Tambahkan Produk</button>
        </form>
    </div>
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
