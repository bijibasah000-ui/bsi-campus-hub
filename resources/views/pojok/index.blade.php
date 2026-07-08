@extends('layouts.app')
@section('title','Pojok Jajan')
@section('page-title','Pojok Jajan')

@section('content')

@if(session('success'))
    <div class="notice" style="background:#ECFDF5;border-color:#6EE7B7;color:#065F46;margin-bottom:16px;">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Header --}}
<div class="pojok-header">
    <div class="pojok-icon">🍱</div>
    <div>
        <div class="pojok-ttl">Pojok Jajan</div>
        <div class="pojok-sub">Jajanan seru dari lapak mahasiswa BSI</div>
    </div>
    <a href="{{ route('pojok.buka-lapak') }}" class="btn-lapak">
        @if($myLapak)
            ⚙️ Kelola Lapakku
        @elseif($myPengajuan && $myPengajuan->isPending())
            ⏳ Menunggu Approval Lapak
        @elseif($myPengajuan && $myPengajuan->isDitolak())
            🔁 Ajukan Ulang Lapak
        @else
            + Buka Lapakmu
        @endif
    </a>
</div>

{{-- Poin Banner --}}
@auth
<div style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:14px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;gap:14px;color:#fff;">
    <div style="font-size:28px;">⭐</div>
    <div style="flex:1;">
        <div style="font-size:13px;font-weight:700;">Poin Kamu</div>
        <div style="font-size:22px;font-weight:800;">{{ number_format(Auth::user()->poin,0,',','.') }} Poin</div>
        <div style="font-size:11px;opacity:.85;">Setiap pembelian dapat <strong>300 poin</strong> per item!</div>
    </div>
    <a href="{{ route('reward.index') }}" style="background:rgba(255,255,255,.22);color:#fff;padding:8px 14px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;">
        🎁 Tukar Poin
    </a>
</div>
@endauth

{{-- Filter kategori --}}
<div class="filter-bar" id="filterBar">
    <button class="fb active" data-filter="semua">Semua</button>
    <button class="fb" data-filter="makanan">🍔 Makanan</button>
    <button class="fb" data-filter="minuman">🧋 Minuman</button>
    <button class="fb" data-filter="barang">📦 Barang</button>
</div>

{{-- Chips lapak aktif (clickable!) --}}
@if($lapaks->count())
<div class="lapak-chips">
    <button class="lapak-chip lapak-chip-btn active-chip" data-lapak="semua">🏠 Semua Lapak</button>
    @foreach($lapaks as $l)
        <button class="lapak-chip lapak-chip-btn" data-lapak="{{ $l->id }}">🏠 {{ $l->nama_toko }}</button>
    @endforeach
</div>
@endif

{{-- Grid produk --}}
@if($produks->count())
<div class="food-grid" id="foodGrid">
    @foreach($produks as $p)
    <div class="food-card" data-jenis="{{ $p->jenis }}" data-lapak="{{ $p->lapak_id }}"
         onclick="bukaDetailProduk({{ $p->id }})" style="cursor:pointer;">
        <div class="food-thumb">
            @if($p->foto)
                <img src="{{ Storage::url($p->foto) }}" alt="{{ $p->nama_produk }}"
                     style="width:100%;height:100%;object-fit:cover;"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <span style="display:none;width:100%;height:100%;align-items:center;justify-content:center;font-size:36px;">
                    {{ $p->jenis === 'makanan' ? '🍱' : ($p->jenis === 'minuman' ? '🧋' : '📦') }}
                </span>
            @else
                {{ $p->jenis === 'makanan' ? '🍱' : ($p->jenis === 'minuman' ? '🧋' : '📦') }}
            @endif
        </div>
        <div class="food-body">
            <div class="food-name">{{ $p->nama_produk }}</div>
            <div class="food-lapak">{{ $p->lapak->nama_toko ?? '-' }}</div>
            {{-- Rating stars --}}
            <div style="display:flex;align-items:center;gap:4px;margin:4px 0;">
                @for($i=1;$i<=5;$i++)
                    <span style="font-size:12px;color:{{ $i <= round($p->rating_avg) ? '#f59e0b' : '#d1d5db' }};">★</span>
                @endfor
                <span style="font-size:10px;color:var(--text-3);">
                    {{ $p->rating_avg > 0 ? number_format($p->rating_avg,1) : 'Belum ada' }}
                    @if($p->rating_count > 0)({{ $p->rating_count }})@endif
                </span>
            </div>
            @if($p->deskripsi)
                <div style="font-size:10px;color:var(--text-3);margin-top:2px;">{{ Str::limit($p->deskripsi,40) }}</div>
            @endif
            <div class="food-price">Rp {{ number_format($p->harga,0,',','.') }}</div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:4px;">
                <span class="badge {{ $p->status==='tersedia' ? 'badge-green' : 'badge-red' }}">
                    {{ $p->status === 'tersedia' ? 'Tersedia' : 'Habis' }}
                </span>
                <span style="font-size:10px;color:#f59e0b;font-weight:600;">+300 poin</span>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div style="text-align:center;padding:60px 20px;color:var(--text-3);">
    <div style="font-size:48px;margin-bottom:12px;">🛒</div>
    <div style="font-size:14px;font-weight:600;">Belum ada produk tersedia</div>
    <div style="font-size:12px;margin-top:4px;">Jadilah yang pertama buka lapak!</div>
</div>
@endif

{{-- ===================== MODAL DETAIL PRODUK ===================== --}}
<div id="modalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;align-items:center;justify-content:center;padding:16px;">
    <div id="modalBox" style="background:#fff;border-radius:20px;max-width:480px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        {{-- Loading state --}}
        <div id="modalLoading" style="padding:60px;text-align:center;color:#6b7280;">
            <div style="font-size:32px;margin-bottom:10px;">⏳</div>
            <div>Memuat detail produk...</div>
        </div>
        {{-- Content --}}
        <div id="modalContent" style="display:none;">
            {{-- Foto --}}
            <div id="mdFotoWrap" style="border-radius:20px 20px 0 0;overflow:hidden;height:200px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:64px;"></div>
            <div style="padding:20px;">
                {{-- Header info --}}
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px;">
                    <div>
                        <div id="mdNama" style="font-size:18px;font-weight:800;color:var(--text);"></div>
                        <div id="mdLapak" style="font-size:12px;color:var(--text-3);margin-top:2px;"></div>
                    </div>
                    <div id="mdHarga" style="font-size:18px;font-weight:800;color:#E50914;white-space:nowrap;"></div>
                </div>
                {{-- Rating display --}}
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                    <div id="mdStars" style="display:flex;gap:2px;"></div>
                    <span id="mdRatingTxt" style="font-size:12px;color:#6b7280;"></span>
                    <span id="mdBadge" style=""></span>
                </div>
                {{-- Deskripsi --}}
                <div id="mdDesk" style="font-size:13px;color:#4b5563;line-height:1.6;margin-bottom:16px;"></div>
                {{-- Poin info --}}
                <div style="background:#fef3c7;border-radius:10px;padding:10px 14px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                    <span style="font-size:20px;">⭐</span>
                    <div>
                        <div style="font-size:12px;font-weight:700;color:#92400e;">Kamu akan dapat <strong>300 poin</strong> per pembelian!</div>
                        <div style="font-size:11px;color:#78350f;">Tukarkan poin di Pojok Reward</div>
                    </div>
                </div>
                {{-- Jumlah & pesan --}}
                <div id="sectionPesan" style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:6px;">Jumlah Pesanan</label>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                        <button onclick="ubahJumlah(-1)" style="width:34px;height:34px;border-radius:8px;border:1.5px solid #d1d5db;background:#fff;font-size:18px;cursor:pointer;font-weight:700;">−</button>
                        <input id="inputJumlah" type="number" value="1" min="1" max="99"
                               style="width:60px;text-align:center;border:1.5px solid #d1d5db;border-radius:8px;padding:6px;font-size:14px;font-weight:700;">
                        <button onclick="ubahJumlah(1)" style="width:34px;height:34px;border-radius:8px;border:1.5px solid #d1d5db;background:#fff;font-size:18px;cursor:pointer;font-weight:700;">+</button>
                        <span id="totalPoin" style="font-size:12px;color:#f59e0b;font-weight:700;">= 300 poin</span>
                    </div>

                    {{-- Metode Pembayaran (tampilan saja / gimik) --}}
                    <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:8px;">Metode Pembayaran</label>
                    <div id="metodeBayarWrap" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;">
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
                    <input type="hidden" id="inputMetodeBayar" value="qris">

                    <button id="btnPesan" onclick="pesanProduk()" class="btn-primary" style="width:100%;margin-bottom:0;">
                        🛒 Pesan Sekarang
                    </button>
                </div>
                {{-- Rating form --}}
                <div id="sectionRating" style="border-top:1px solid #f3f4f6;padding-top:16px;">
                    <div style="font-size:13px;font-weight:700;margin-bottom:10px;">⭐ Beri Rating</div>
                    <div style="display:flex;gap:6px;margin-bottom:10px;" id="starInput">
                        @for($i=1;$i<=5;$i++)
                        <button onclick="pilihBintang({{ $i }})"
                                style="font-size:26px;background:none;border:none;cursor:pointer;transition:.15s;"
                                data-star="{{ $i }}">☆</button>
                        @endfor
                    </div>
                    <textarea id="inputKomentar" placeholder="Tulis komentar (opsional)..."
                              style="width:100%;border:1.5px solid #d1d5db;border-radius:10px;padding:10px;font-size:13px;resize:vertical;min-height:70px;box-sizing:border-box;"></textarea>
                    <button onclick="kirimRating()" class="btn-primary" style="width:100%;margin-top:8px;background:#6366f1;">
                        Kirim Rating
                    </button>
                </div>
                {{-- Komentar list --}}
                <div id="sectionKomentar" style="border-top:1px solid #f3f4f6;padding-top:14px;margin-top:14px;">
                    <div style="font-size:13px;font-weight:700;margin-bottom:10px;">💬 Ulasan Pembeli</div>
                    <div id="listKomentar"></div>
                </div>
            </div>
        </div>
        {{-- Close btn --}}
        <button onclick="tutupModal()" style="position:sticky;bottom:0;width:100%;padding:14px;background:#f9fafb;border:none;border-top:1px solid #e5e7eb;font-size:13px;font-weight:600;color:#6b7280;cursor:pointer;border-radius:0 0 20px 20px;">
            Tutup
        </button>
    </div>
</div>

{{-- ===================== MODAL SUKSES PESAN ===================== --}}
<div id="modalSukses" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1100;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:20px;padding:32px;max-width:340px;width:100%;text-align:center;">
        <div style="font-size:48px;margin-bottom:12px;">🎉</div>
        <div style="font-size:18px;font-weight:800;margin-bottom:8px;">Pesanan Berhasil!</div>
        <div id="txtSuksesDetail" style="font-size:13px;color:#6b7280;margin-bottom:16px;"></div>
        <div id="txtPoinDapat" style="background:#fef3c7;border-radius:10px;padding:10px;margin-bottom:16px;font-size:14px;font-weight:700;color:#92400e;"></div>
        <button onclick="tutupModalSukses()" class="btn-primary" style="width:100%;">Oke, Lanjut!</button>
    </div>
</div>

@endsection

@push('styles')
<style>
.lapak-chip-btn {
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: 50px;
    padding: 5px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: var(--text);
    transition: all .18s;
}
.lapak-chip-btn:hover, .lapak-chip-btn.active-chip {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
}
#modalOverlay.show { display: flex !important; }
#modalSukses.show  { display: flex !important; }
</style>
@endpush

@push('scripts')
<script>
var _currentProdukId = null;
var _selectedStar    = 0;

/* ─── Filter kategori ─── */
document.querySelectorAll('#filterBar .fb').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('#filterBar .fb').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        filterGrid();
    });
});

/* ─── Filter lapak chips ─── */
document.querySelectorAll('.lapak-chip-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.lapak-chip-btn').forEach(b => b.classList.remove('active-chip'));
        this.classList.add('active-chip');
        filterGrid();
    });
});

function filterGrid() {
    var jenis = (document.querySelector('#filterBar .fb.active') || {}).dataset.filter || 'semua';
    var lapak = (document.querySelector('.lapak-chip-btn.active-chip') || {}).dataset.lapak || 'semua';
    document.querySelectorAll('#foodGrid .food-card').forEach(function(card) {
        var jOk = jenis === 'semua' || card.dataset.jenis === jenis;
        var lOk = lapak === 'semua' || card.dataset.lapak === lapak;
        card.style.display = (jOk && lOk) ? 'block' : 'none';
    });
}

/* ─── Buka modal detail produk ─── */
function bukaDetailProduk(id) {
    _currentProdukId = id;
    _selectedStar    = 0;
    document.getElementById('modalOverlay').classList.add('show');
    document.getElementById('modalLoading').style.display = '';
    document.getElementById('modalContent').style.display = 'none';
    document.body.style.overflow = 'hidden';

    fetch('/pojok-jajan/produk/' + id + '/detail', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        var p = data.produk;
        // Foto
        var fotoEl = document.getElementById('mdFotoWrap');
        if (data.foto_url) {
            fotoEl.innerHTML = '<img src="'+data.foto_url+'" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.innerHTML=getEmoji(\''+p.jenis+'\')">';
        } else {
            fotoEl.innerHTML = getEmoji(p.jenis);
            fotoEl.style.fontSize = '64px';
        }
        // Info
        document.getElementById('mdNama').textContent  = p.nama_produk;
        document.getElementById('mdLapak').textContent = (p.lapak ? '🏠 ' + p.lapak.nama_toko : '');
        document.getElementById('mdHarga').textContent = 'Rp ' + parseInt(p.harga).toLocaleString('id-ID');
        document.getElementById('mdDesk').textContent  = p.deskripsi || 'Tidak ada deskripsi.';
        // Stars
        renderStarsDisplay(p.rating_avg, p.rating_count);
        // Badge status
        var badgeEl = document.getElementById('mdBadge');
        badgeEl.className = 'badge ' + (p.status === 'tersedia' ? 'badge-green' : 'badge-red');
        badgeEl.textContent = p.status === 'tersedia' ? 'Tersedia' : 'Habis';
        // Section pesan
        document.getElementById('sectionPesan').style.display = p.status === 'tersedia' ? '' : 'none';
        // Input bintang (isi dari rating sebelumnya)
        if (data.my_rating) {
            pilihBintang(data.my_rating.rating);
            document.getElementById('inputKomentar').value = data.my_rating.komentar || '';
        } else {
            resetBintang();
        }
        // Komentar
        renderKomentar(p.ratings || []);
        // Jumlah
        document.getElementById('inputJumlah').value = 1;
        updateTotalPoin();
        // Reset metode pembayaran ke default (opsi pertama)
        var opsiBayar = document.querySelectorAll('#metodeBayarWrap .opsi-bayar');
        if (opsiBayar.length) pilihMetodeBayar(opsiBayar[0]);

        document.getElementById('modalLoading').style.display = 'none';
        document.getElementById('modalContent').style.display = '';
    })
    .catch(function() {
        document.getElementById('modalLoading').innerHTML = '<div style="padding:40px;color:#e11d48;">Gagal memuat. Coba lagi.</div>';
    });
}

function tutupModal() {
    document.getElementById('modalOverlay').classList.remove('show');
    document.body.style.overflow = '';
}
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});

function getEmoji(jenis) {
    return jenis === 'makanan' ? '🍱' : (jenis === 'minuman' ? '🧋' : '📦');
}

function renderStarsDisplay(avg, count) {
    var starsEl = document.getElementById('mdStars');
    starsEl.innerHTML = '';
    for (var i=1;i<=5;i++) {
        var s = document.createElement('span');
        s.textContent = '★';
        s.style.cssText = 'font-size:18px;color:'+(i<=Math.round(avg)?'#f59e0b':'#d1d5db')+';';
        starsEl.appendChild(s);
    }
    document.getElementById('mdRatingTxt').textContent = avg > 0
        ? (parseFloat(avg).toFixed(1) + ' / 5 (' + count + ' ulasan)')
        : 'Belum ada rating';
}

function renderKomentar(ratings) {
    var el = document.getElementById('listKomentar');
    if (!ratings || !ratings.length) {
        el.innerHTML = '<div style="font-size:12px;color:#9ca3af;text-align:center;padding:16px;">Belum ada ulasan.</div>';
        return;
    }
    el.innerHTML = ratings.slice(0,5).map(function(r) {
        var stars = '';
        for (var i=1;i<=5;i++) stars += '<span style="color:'+(i<=r.rating?'#f59e0b':'#d1d5db')+';">★</span>';
        var nama = r.user ? r.user.username : 'Anonim';
        return '<div style="padding:10px 0;border-bottom:1px solid #f3f4f6;">' +
            '<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">' +
            '<div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">'+ nama.substring(0,2).toUpperCase() +'</div>' +
            '<div>' +
            '<div style="font-size:12px;font-weight:600;">'+ nama +'</div>' +
            '<div style="font-size:12px;">'+ stars +'</div>' +
            '</div></div>' +
            (r.komentar ? '<div style="font-size:12px;color:#4b5563;padding-left:36px;">'+ r.komentar +'</div>' : '') +
            '</div>';
    }).join('');
}

/* ─── Rating bintang ─── */
function pilihBintang(n) {
    _selectedStar = n;
    document.querySelectorAll('#starInput button').forEach(function(btn) {
        btn.textContent = parseInt(btn.dataset.star) <= n ? '★' : '☆';
        btn.style.color = parseInt(btn.dataset.star) <= n ? '#f59e0b' : '#d1d5db';
    });
}
function resetBintang() {
    _selectedStar = 0;
    document.querySelectorAll('#starInput button').forEach(function(btn) {
        btn.textContent = '☆';
        btn.style.color = '#d1d5db';
    });
    document.getElementById('inputKomentar').value = '';
}

function kirimRating() {
    if (!_selectedStar) { alert('Pilih bintang dulu!'); return; }
    var btn = document.querySelector('#sectionRating .btn-primary');
    btn.disabled = true; btn.textContent = 'Menyimpan...';
    var komentar = document.getElementById('inputKomentar').value;
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    var csrf = csrfToken ? csrfToken.content : '';

    fetch('/pojok-jajan/produk/'+_currentProdukId+'/rating', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf,'X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify({ rating: _selectedStar, komentar: komentar })
    })
    .then(r => r.json())
    .then(function(d) {
        renderStarsDisplay(d.avg, d.count);
        btn.disabled = false; btn.textContent = '✅ Rating Tersimpan!';
        setTimeout(function(){ btn.textContent = 'Kirim Rating'; }, 2000);
    })
    .catch(function() { btn.disabled = false; btn.textContent = 'Kirim Rating'; });
}

/* ─── Pesan produk ─── */
function ubahJumlah(delta) {
    var inp = document.getElementById('inputJumlah');
    var val = Math.max(1, Math.min(99, parseInt(inp.value||1) + delta));
    inp.value = val;
    updateTotalPoin();
}
document.getElementById('inputJumlah').addEventListener('input', updateTotalPoin);
function updateTotalPoin() {
    var qty = parseInt(document.getElementById('inputJumlah').value || 1);
    document.getElementById('totalPoin').textContent = '= '+(qty*300).toLocaleString('id-ID')+' poin';
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

function pesanProduk() {
    var jumlah = parseInt(document.getElementById('inputJumlah').value || 1);
    var metodeBayar = document.getElementById('inputMetodeBayar').value || 'qris';
    var btn    = document.getElementById('btnPesan');
    btn.disabled = true; btn.textContent = '⏳ Memproses...';
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    var csrf = csrfToken ? csrfToken.content : '';

    fetch('/pojok-jajan/produk/'+_currentProdukId+'/pesan', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf,'X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify({ jumlah: jumlah, metode_pembayaran: metodeBayar })
    })
    .then(r => r.json())
    .then(function(d) {
        btn.disabled = false; btn.textContent = '🛒 Pesan Sekarang';
        if (d.ok) {
            tutupModal();
            // update poin di banner
            var poinEl = document.querySelector('.poin-display');
            if (poinEl) poinEl.textContent = d.poin_total.toLocaleString('id-ID') + ' Poin';
            document.getElementById('txtSuksesDetail').textContent = 'Pesananmu sedang diproses. Seller akan segera mengkonfirmasi.';
            document.getElementById('txtPoinDapat').textContent = '⭐ +' + d.poin_didapat.toLocaleString('id-ID') + ' poin masuk ke akunmu!';
            document.getElementById('modalSukses').classList.add('show');
        } else {
            alert(d.msg || 'Gagal memesan.');
        }
    })
    .catch(function() { btn.disabled = false; btn.textContent = '🛒 Pesan Sekarang'; });
}

function tutupModalSukses() {
    document.getElementById('modalSukses').classList.remove('show');
    location.reload();
}
</script>
@endpush
