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
    @if(session('error'))
        <div class="notice" style="background:#FFF1F2;border-color:#FECDD3;color:#E11D48;margin-bottom:16px;">⚠️ {{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="notice" style="background:#FFF1F2;border-color:#FECDD3;color:#E11D48;margin-bottom:14px;">{{ $errors->first() }}</div>
    @endif

    {{-- ================================================================
         KONDISI 1: Sudah punya lapak yang disetujui admin -> kelola seperti biasa
    ================================================================= --}}
    @if($myLapak)

    <div class="card" style="margin-bottom:18px;">
        <div class="card-hd"><h3>⚙️ Edit Info Lapak</h3></div>

        <form action="{{ route('pojok.simpan-lapak') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Foto Lapak</label>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:8px;">
                    <div id="fotoPreview" style="width:80px;height:80px;border-radius:10px;background:var(--bg);border:1.5px dashed var(--border);display:flex;align-items:center;justify-content:center;font-size:28px;overflow:hidden;">
                        @if($myLapak->foto_toko)
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
                <input type="file" id="foto_toko" name="foto_toko" accept="image/*" style="display:none;" onchange="previewImg(this,'fotoPreview')">
            </div>

            <div class="form-group">
                <label class="form-label">Nama Toko <span style="color:#E11D48;">*</span></label>
                <input class="form-input" type="text" name="nama_toko" value="{{ old('nama_toko', $myLapak->nama_toko) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Toko</label>
                <textarea class="form-input" name="deskripsi_toko" rows="3">{{ old('deskripsi_toko', $myLapak->deskripsi_toko) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Kontak (WhatsApp)</label>
                <input class="form-input" type="text" name="kontak" value="{{ old('kontak', $myLapak->kontak) }}">
            </div>

            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    {{-- Daftar produk milik lapak ini --}}
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
            <form action="{{ route('pojok.hapus-produk',$p) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
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

    {{-- ================================================================
         KONDISI 2: Ada pengajuan yang masih PENDING -> tampilkan status, tidak ada form
    ================================================================= --}}
    @elseif($pengajuan && $pengajuan->isPending())

    <div class="card" style="text-align:center;padding:32px 20px;">
        <div style="font-size:42px;margin-bottom:10px;">⏳</div>
        <h3 style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:6px;">Menunggu Persetujuan Admin</h3>
        <p style="font-size:13px;color:var(--text-3);margin-bottom:20px;">
            Permintaan pembukaan lapak <b>"{{ $pengajuan->nama_toko }}"</b> sudah terkirim ke admin.
            Lapak akan tayang di Pojok Jajan setelah disetujui.
        </p>

        <div style="text-align:left;background:var(--bg);border-radius:12px;padding:14px 16px;margin-bottom:20px;font-size:13px;">
            <div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:var(--text-3);">Nama Lapak</span><b>{{ $pengajuan->nama_toko }}</b></div>
            <div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:var(--text-3);">Paket</span><b>{{ $pengajuan->durasi_bulan }} bulan</b></div>
            <div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:var(--text-3);">Biaya</span><b>Rp {{ number_format($pengajuan->harga,0,',','.') }}</b></div>
            <div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:var(--text-3);">Metode Bayar</span><b>{{ strtoupper($pengajuan->metode_pembayaran) }}</b></div>
        </div>

        <form action="{{ route('pojok.batal-pengajuan-lapak', $pengajuan) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan ini?')">
            @csrf
            <button type="submit" style="padding:8px 16px;background:#FFF1F2;color:#E11D48;border:none;border-radius:var(--radius-md);font-size:12px;font-weight:600;">
                Batalkan Pengajuan
            </button>
        </form>
    </div>

    {{-- ================================================================
         KONDISI 3 & 4: Belum pernah ajukan, ATAU pengajuan sebelumnya DITOLAK -> tampilkan form pengajuan + pembayaran
    ================================================================= --}}
    @else

        @if($pengajuan && $pengajuan->isDitolak())
        <div class="notice" style="background:#FFF7ED;border-color:#FDBA74;color:#9A3412;margin-bottom:16px;">
            ❌ Pengajuan lapak <b>"{{ $pengajuan->nama_toko }}"</b> sebelumnya ditolak admin.
            @if($pengajuan->catatan_admin)<br><span style="font-size:12px;">Alasan: {{ $pengajuan->catatan_admin }}</span>@endif
            <br><span style="font-size:12px;">Silakan perbaiki dan ajukan ulang di bawah ini.</span>
        </div>
        @endif

        <div class="notice" style="background:#EFF6FF;border-color:#93C5FD;color:#1E3A8A;margin-bottom:16px;font-size:12px;">
            ℹ️ Pembukaan lapak butuh persetujuan admin. Setelah kamu mengajukan &amp; membayar biaya paket, lapak akan tayang di Pojok Jajan setelah disetujui.
        </div>

        <form action="{{ route('pojok.ajukan-lapak') }}" method="POST" enctype="multipart/form-data" id="formAjukanLapak">
            @csrf

            {{-- STEP 1: Info Lapak --}}
            <div class="card" style="margin-bottom:18px;">
                <div class="card-hd"><h3>🏠 Info Lapak</h3></div>

                <div class="form-group">
                    <label class="form-label">Foto Lapak</label>
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:8px;">
                        <div id="fotoPreview" style="width:80px;height:80px;border-radius:10px;background:var(--bg);border:1.5px dashed var(--border);display:flex;align-items:center;justify-content:center;font-size:28px;overflow:hidden;">
                            🏠
                        </div>
                        <div>
                            <label for="foto_toko" style="padding:7px 14px;background:var(--bg);border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:12px;font-weight:600;cursor:pointer;display:inline-block;">
                                Pilih Foto
                            </label>
                            <div style="font-size:10px;color:var(--text-3);margin-top:4px;">JPG, PNG, WEBP • Maks 2MB</div>
                        </div>
                    </div>
                    <input type="file" id="foto_toko" name="foto_toko" accept="image/*" style="display:none;" onchange="previewImg(this,'fotoPreview')">
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Toko <span style="color:#E11D48;">*</span></label>
                    <input class="form-input" type="text" name="nama_toko" value="{{ old('nama_toko', $pengajuan->nama_toko ?? '') }}" placeholder="Contoh: Dapur Kita, Warung Hemat...">
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Toko</label>
                    <textarea class="form-input" name="deskripsi_toko" rows="3" placeholder="Ceritakan sedikit tentang lapakmu...">{{ old('deskripsi_toko', $pengajuan->deskripsi_toko ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Kontak (WhatsApp)</label>
                    <input class="form-input" type="text" name="kontak" value="{{ old('kontak', $pengajuan->kontak ?? '') }}" placeholder="Contoh: 08123456789">
                </div>
            </div>

            {{-- STEP 2: Pilih Durasi Pembukaan --}}
            <div class="card" style="margin-bottom:18px;">
                <div class="card-hd"><h3>🗓️ Pilih Durasi Pembukaan</h3></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    @foreach($paketHarga as $bulan => $harga)
                    <div class="opsi-durasi {{ $loop->first ? 'active' : '' }}" data-bulan="{{ $bulan }}" data-harga="{{ $harga }}" onclick="pilihDurasi(this)"
                         style="border:1.5px solid {{ $loop->first ? '#6d28d9' : '#d1d5db' }};background:{{ $loop->first ? '#f5f3ff' : '#fff' }};border-radius:10px;padding:10px 12px;cursor:pointer;transition:.15s;">
                        <div style="font-size:13px;font-weight:700;color:var(--text);">{{ $bulan }} Bulan</div>
                        <div style="font-size:12px;color:#f59e0b;font-weight:700;">Rp {{ number_format($harga,0,',','.') }}</div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" id="inputDurasi" name="durasi_bulan" value="{{ array_key_first($paketHarga) }}">
            </div>

            {{-- STEP 3: Metode Pembayaran (gimik / tampilan saja) --}}
            <div class="card" style="margin-bottom:18px;">
                <div class="card-hd"><h3>💳 Metode Pembayaran</h3></div>
                <div id="metodeBayarWrap" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                    <div class="opsi-bayar active" data-underlying="qris" onclick="pilihMetodeBayar(this)"
                         style="display:flex;align-items:center;gap:8px;border:1.5px solid #6d28d9;background:#f5f3ff;border-radius:10px;padding:8px 10px;cursor:pointer;transition:.15s;">
                        <span style="width:26px;height:26px;border-radius:6px;background:#00AA13;color:#fff;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center;">GP</span>
                        <span style="font-size:12px;font-weight:700;color:var(--text);">GoPay</span>
                    </div>
                    <div class="opsi-bayar" data-underlying="qris" onclick="pilihMetodeBayar(this)"
                         style="display:flex;align-items:center;gap:8px;border:1.5px solid #d1d5db;background:#fff;border-radius:10px;padding:8px 10px;cursor:pointer;transition:.15s;">
                        <span style="width:26px;height:26px;border-radius:6px;background:#118EEA;color:#fff;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center;">D</span>
                        <span style="font-size:12px;font-weight:700;color:var(--text);">DANA</span>
                    </div>
                    <div class="opsi-bayar" data-underlying="tunai" onclick="pilihMetodeBayar(this)"
                         style="display:flex;align-items:center;gap:8px;border:1.5px solid #d1d5db;background:#fff;border-radius:10px;padding:8px 10px;cursor:pointer;transition:.15s;">
                        <span style="width:26px;height:26px;border-radius:6px;background:#00529C;color:#fff;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center;">BCA</span>
                        <span style="font-size:12px;font-weight:700;color:var(--text);">BCA</span>
                    </div>
                    <div class="opsi-bayar" data-underlying="tunai" onclick="pilihMetodeBayar(this)"
                         style="display:flex;align-items:center;gap:8px;border:1.5px solid #d1d5db;background:#fff;border-radius:10px;padding:8px 10px;cursor:pointer;transition:.15s;">
                        <span style="width:26px;height:26px;border-radius:6px;background:#00529C;color:#fff;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center;">BRI</span>
                        <span style="font-size:12px;font-weight:700;color:var(--text);">BRI</span>
                    </div>
                </div>
                <input type="hidden" id="inputMetodeBayar" name="metode_pembayaran" value="qris">

                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:10px;border-top:1px dashed var(--border);">
                    <span style="font-size:13px;color:var(--text-3);">Total Bayar</span>
                    <span id="totalBayar" style="font-size:16px;font-weight:800;color:var(--text);">Rp {{ number_format(reset($paketHarga),0,',','.') }}</span>
                </div>
            </div>

            <button type="submit" class="btn-primary">💳 Ajukan &amp; Bayar</button>
        </form>

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

var _hargaPaket = @json($paketHarga ?? []);

function pilihDurasi(el) {
    document.querySelectorAll('.opsi-durasi').forEach(function(opt) {
        opt.classList.remove('active');
        opt.style.border = '1.5px solid #d1d5db';
        opt.style.background = '#fff';
    });
    el.classList.add('active');
    el.style.border = '1.5px solid #6d28d9';
    el.style.background = '#f5f3ff';
    document.getElementById('inputDurasi').value = el.dataset.bulan;

    var totalEl = document.getElementById('totalBayar');
    if (totalEl) {
        var harga = parseInt(el.dataset.harga || 0);
        totalEl.textContent = 'Rp ' + harga.toLocaleString('id-ID');
    }
}

function pilihMetodeBayar(el) {
    document.querySelectorAll('#metodeBayarWrap .opsi-bayar').forEach(function(opt) {
        opt.classList.remove('active');
        opt.style.border = '1.5px solid #d1d5db';
        opt.style.background = '#fff';
    });
    el.classList.add('active');
    el.style.border = '1.5px solid #6d28d9';
    el.style.background = '#f5f3ff';
    document.getElementById('inputMetodeBayar').value = el.dataset.underlying;
}
</script>
@endpush
