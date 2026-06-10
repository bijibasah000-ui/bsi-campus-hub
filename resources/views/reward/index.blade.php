@extends('layouts.app')
@section('title','Pojok Reward')
@section('page-title','Pojok Reward')

@section('content')

{{-- Header banner --}}
<div style="background:linear-gradient(135deg,#7c3aed,#4f46e5,#2563eb);border-radius:18px;padding:20px 22px;margin-bottom:20px;color:#fff;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-size:22px;font-weight:800;margin-bottom:4px;">🎁 Pojok Reward</div>
            <div style="font-size:13px;opacity:.85;">Tukarkan poin kamu dengan hadiah menarik!</div>
        </div>
        <div style="background:rgba(255,255,255,.18);border-radius:14px;padding:12px 20px;text-align:center;">
            <div style="font-size:11px;opacity:.9;font-weight:600;">POIN KAMU</div>
            <div style="font-size:28px;font-weight:900;letter-spacing:-1px;" id="myPoinDisplay">
                {{ number_format($user->poin, 0, ',', '.') }}
            </div>
            <div style="font-size:11px;opacity:.8;">poin tersedia</div>
        </div>
    </div>
    {{-- Progress bar ke reward terdekat --}}
    @php
        $nextReward = $rewards->where('poin_dibutuhkan', '>', $user->poin)->first();
        $pct = $nextReward ? min(100, round($user->poin / $nextReward->poin_dibutuhkan * 100)) : 100;
    @endphp
    @if($nextReward)
    <div style="margin-top:14px;">
        <div style="font-size:11px;opacity:.85;margin-bottom:6px;">
            Butuh <strong>{{ number_format($nextReward->poin_dibutuhkan - $user->poin, 0, ',', '.') }} poin</strong> lagi untuk {{ $nextReward->nama }}
        </div>
        <div style="background:rgba(255,255,255,.2);border-radius:50px;height:8px;overflow:hidden;">
            <div style="width:{{ $pct }}%;background:#fbbf24;height:100%;border-radius:50px;transition:.5s;"></div>
        </div>
    </div>
    @else
    <div style="margin-top:10px;font-size:13px;font-weight:700;opacity:.95;">
        🎉 Kamu sudah bisa menukarkan semua reward!
    </div>
    @endif
</div>

{{-- Cara dapat poin --}}
<div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:14px;padding:14px 16px;margin-bottom:20px;">
    <div style="font-size:13px;font-weight:700;color:#166534;margin-bottom:8px;">⭐ Cara Mendapatkan Poin</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div style="background:#fff;border-radius:10px;padding:10px;text-align:center;">
            <div style="font-size:20px;margin-bottom:4px;">🛒</div>
            <div style="font-size:11px;font-weight:700;color:#166534;">Beli di Pojok Jajan</div>
            <div style="font-size:18px;font-weight:900;color:#16a34a;">+300</div>
            <div style="font-size:10px;color:#6b7280;">poin per item</div>
        </div>
        <div style="background:#fff;border-radius:10px;padding:10px;text-align:center;">
            <div style="font-size:20px;margin-bottom:4px;">⭐</div>
            <div style="font-size:11px;font-weight:700;color:#166534;">Beri Rating Produk</div>
            <div style="font-size:18px;font-weight:900;color:#16a34a;">+50</div>
            <div style="font-size:10px;color:#6b7280;">poin per ulasan</div>
        </div>
    </div>
</div>

{{-- Kategori filter --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;" id="katFilter">
    <button class="kat-btn active-kat" data-kat="semua" onclick="filterKat(this,'semua')">Semua</button>
    <button class="kat-btn" data-kat="streaming" onclick="filterKat(this,'streaming')">📺 Streaming</button>
    <button class="kat-btn" data-kat="pulsa" onclick="filterKat(this,'pulsa')">📱 Pulsa</button>
    <button class="kat-btn" data-kat="voucher" onclick="filterKat(this,'voucher')">🎫 Voucher</button>
    <button class="kat-btn" data-kat="belanja" onclick="filterKat(this,'belanja')">🛍️ Belanja</button>
</div>

{{-- Grid reward katalog --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;margin-bottom:28px;" id="rewardGrid">
    @foreach($rewards as $rw)
    @php
        $bisa = $user->poin >= $rw->poin_dibutuhkan;
        $kurang = $rw->poin_dibutuhkan - $user->poin;
    @endphp
    <div class="reward-card" data-kat="{{ $rw->kategori }}"
         style="border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);background:#fff;
                {{ !$bisa ? 'opacity:.7;' : '' }}">
        {{-- Card header dengan warna --}}
        <div style="background:{{ $rw->warna_bg }};padding:20px;text-align:center;position:relative;">
            {{-- Logo/icon sesuai kategori --}}
            <div style="font-size:48px;margin-bottom:6px;">
                @if($rw->gambar === 'netflix')
                    <div style="background:#000;color:#E50914;border-radius:8px;display:inline-block;padding:4px 10px;font-size:24px;font-weight:900;font-style:italic;letter-spacing:-1px;">N</div>
                @elseif($rw->gambar === 'spotify')
                    <div style="background:#1DB954;color:#fff;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;">♫</div>
                @elseif($rw->gambar === 'pulsa')
                    📱
                @elseif($rw->gambar === 'voucher')
                    🎫
                @elseif($rw->gambar === 'tokopedia')
                    <div style="background:#03AC0E;color:#fff;border-radius:10px;padding:4px 8px;font-size:16px;font-weight:900;">toped</div>
                @elseif($rw->gambar === 'disney')
                    <div style="background:#113CCF;color:#fff;border-radius:8px;padding:4px 8px;font-size:20px;font-weight:900;">D+</div>
                @else
                    🎁
                @endif
            </div>
            {{-- Poin badge --}}
            <div style="background:rgba(255,255,255,.25);border-radius:50px;padding:4px 12px;display:inline-block;font-size:13px;font-weight:800;color:#fff;">
                ⭐ {{ number_format($rw->poin_dibutuhkan, 0, ',', '.') }} poin
            </div>
        </div>
        {{-- Card body --}}
        <div style="padding:14px;">
            <div style="font-size:14px;font-weight:800;color:var(--text);margin-bottom:4px;">{{ $rw->nama }}</div>
            <div style="font-size:11px;color:#6b7280;margin-bottom:12px;line-height:1.5;">{{ Str::limit($rw->deskripsi, 70) }}</div>
            {{-- Syarat mini --}}
            @if($rw->syarat && count($rw->syarat))
            <div style="margin-bottom:12px;">
                @foreach(array_slice($rw->syarat, 0, 2) as $s)
                <div style="font-size:10px;color:#9ca3af;display:flex;align-items:flex-start;gap:4px;margin-bottom:2px;">
                    <span style="color:#d1d5db;flex-shrink:0;">•</span> {{ $s }}
                </div>
                @endforeach
                @if(count($rw->syarat) > 2)
                <div style="font-size:10px;color:#9ca3af;">+{{ count($rw->syarat) - 2 }} syarat lagi</div>
                @endif
            </div>
            @endif
            {{-- Tombol tukar --}}
            @if($bisa)
            <button onclick="konfirmasiTukar({{ $rw->id }}, '{{ addslashes($rw->nama) }}', {{ $rw->poin_dibutuhkan }})"
                    style="width:100%;padding:10px;border-radius:10px;border:none;
                           background:{{ $rw->warna_bg }};color:#fff;font-size:13px;font-weight:700;cursor:pointer;">
                🎁 Tukar Sekarang
            </button>
            @else
            <div style="background:#f3f4f6;border-radius:10px;padding:10px;text-align:center;">
                <div style="font-size:11px;color:#9ca3af;margin-bottom:2px;">Butuh</div>
                <div style="font-size:14px;font-weight:800;color:#6b7280;">+{{ number_format($kurang, 0, ',', '.') }} poin lagi</div>
                <a href="{{ route('pojok.index') }}" style="font-size:11px;color:var(--primary);text-decoration:none;">→ Belanja sekarang</a>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Riwayat poin & penukaran --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
    {{-- Riwayat Poin --}}
    <div class="card" style="margin:0;">
        <div class="card-hd"><h3>📊 Riwayat Poin</h3></div>
        @forelse($poinLogs as $log)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:12px;">
            <div>
                <div style="font-weight:600;color:var(--text);">{{ Str::limit($log->keterangan, 32) }}</div>
                <div style="color:var(--text-3);">{{ $log->created_at->diffForHumans() }}</div>
            </div>
            <div style="font-weight:800;color:{{ $log->jumlah >= 0 ? '#16a34a' : '#dc2626' }};white-space:nowrap;">
                {{ $log->jumlah >= 0 ? '+' : '' }}{{ number_format($log->jumlah, 0, ',', '.') }}
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:20px;color:var(--text-3);font-size:12px;">Belum ada aktivitas poin.</div>
        @endforelse
    </div>

    {{-- Riwayat Penukaran --}}
    <div class="card" style="margin:0;">
        <div class="card-hd"><h3>🎁 Riwayat Penukaran</h3></div>
        @forelse($riwayat as $r)
        <div style="padding:8px 0;border-bottom:1px solid var(--border);font-size:12px;">
            <div style="font-weight:600;color:var(--text);">{{ Str::limit($r->reward->nama ?? '-', 28) }}</div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:2px;">
                <div style="color:var(--text-3);">{{ $r->created_at->diffForHumans() }}</div>
                <span class="badge {{ $r->status === 'selesai' ? 'badge-green' : 'badge-red' }}">
                    {{ ucfirst($r->status) }}
                </span>
            </div>
            @if($r->kode_klaim)
            <div style="font-size:10px;color:#7c3aed;font-weight:700;font-family:monospace;margin-top:3px;">
                Kode: {{ $r->kode_klaim }}
            </div>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:20px;color:var(--text-3);font-size:12px;">Belum ada penukaran.</div>
        @endforelse
    </div>
</div>

{{-- Modal konfirmasi tukar --}}
<div id="modalTukar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1100;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:20px;padding:28px;max-width:360px;width:100%;text-align:center;">
        <div style="font-size:40px;margin-bottom:12px;">🎁</div>
        <div style="font-size:16px;font-weight:800;margin-bottom:6px;">Konfirmasi Penukaran</div>
        <div id="txtKonfirmasi" style="font-size:13px;color:#6b7280;margin-bottom:16px;line-height:1.6;"></div>
        <div style="display:flex;gap:10px;">
            <button onclick="tutupModalTukar()" style="flex:1;padding:11px;border-radius:10px;border:1.5px solid #d1d5db;background:#fff;font-size:13px;font-weight:600;cursor:pointer;color:#6b7280;">
                Batal
            </button>
            <button id="btnKonfirmTukar" onclick="eksekusiTukar()" style="flex:1;padding:11px;border-radius:10px;border:none;background:#7c3aed;color:#fff;font-size:13px;font-weight:700;cursor:pointer;">
                Ya, Tukar!
            </button>
        </div>
    </div>
</div>

{{-- Modal sukses tukar --}}
<div id="modalSuksesTukar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1200;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:20px;padding:28px;max-width:360px;width:100%;text-align:center;">
        <div style="font-size:48px;margin-bottom:12px;">🎉</div>
        <div style="font-size:18px;font-weight:800;margin-bottom:8px;">Penukaran Berhasil!</div>
        <div id="txtNamaReward" style="font-size:14px;color:#6b7280;margin-bottom:12px;"></div>
        <div id="txtKodeKlaim" style="background:#f5f3ff;border-radius:12px;padding:14px;margin-bottom:16px;">
            <div style="font-size:11px;color:#7c3aed;font-weight:600;margin-bottom:4px;">KODE KLAIM KAMU</div>
            <div style="font-size:22px;font-weight:900;font-family:monospace;letter-spacing:3px;color:#4c1d95;" id="kodeDisplay"></div>
            <div style="font-size:10px;color:#9ca3af;margin-top:4px;">Screenshot & simpan kode ini!</div>
        </div>
        <div id="txtPoinSisa" style="font-size:13px;color:#6b7280;margin-bottom:16px;"></div>
        <button onclick="tutupModalSuksesTukar()" class="btn-primary" style="width:100%;background:#7c3aed;">
            Oke, Mantap!
        </button>
    </div>
</div>

@endsection

@push('styles')
<style>
.kat-btn {
    padding: 6px 14px;
    border-radius: 50px;
    border: 1.5px solid var(--border);
    background: var(--bg);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: var(--text);
    transition: all .18s;
}
.kat-btn.active-kat, .kat-btn:hover {
    background: #7c3aed;
    color: #fff;
    border-color: #7c3aed;
}
.reward-card { transition: transform .2s, box-shadow .2s; }
.reward-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
#modalTukar.show, #modalSuksesTukar.show { display: flex !important; }
@media(max-width:600px) {
    div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns: 1fr !important; }
}
</style>
@endpush

@push('scripts')
<script>
var _tukarId   = null;
var _tukarNama = '';
var _tukarPoin = 0;

function filterKat(btn, kat) {
    document.querySelectorAll('.kat-btn').forEach(b => b.classList.remove('active-kat'));
    btn.classList.add('active-kat');
    document.querySelectorAll('.reward-card').forEach(function(c) {
        c.style.display = (kat === 'semua' || c.dataset.kat === kat) ? '' : 'none';
    });
}

function konfirmasiTukar(id, nama, poin) {
    _tukarId   = id;
    _tukarNama = nama;
    _tukarPoin = poin;
    document.getElementById('txtKonfirmasi').innerHTML =
        'Kamu akan menukar <strong>' + poin.toLocaleString('id-ID') + ' poin</strong> untuk:<br>' +
        '<strong style="color:#7c3aed;">' + nama + '</strong>';
    document.getElementById('modalTukar').classList.add('show');
}
function tutupModalTukar() {
    document.getElementById('modalTukar').classList.remove('show');
}

function eksekusiTukar() {
    var btn = document.getElementById('btnKonfirmTukar');
    btn.disabled = true; btn.textContent = '⏳ Memproses...';
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    var csrf = csrfToken ? csrfToken.content : '';

    fetch('/pojok-reward/' + _tukarId + '/tukar', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(function(d) {
        btn.disabled = false; btn.textContent = 'Ya, Tukar!';
        tutupModalTukar();
        if (d.ok) {
            document.getElementById('myPoinDisplay').textContent = d.poin_sisa.toLocaleString('id-ID');
            document.getElementById('txtNamaReward').textContent = d.reward;
            document.getElementById('kodeDisplay').textContent  = d.kode;
            document.getElementById('txtPoinSisa').textContent  = 'Sisa poin kamu: ' + d.poin_sisa.toLocaleString('id-ID') + ' poin';
            document.getElementById('modalSuksesTukar').classList.add('show');
        } else {
            alert(d.msg || 'Gagal menukar reward.');
        }
    })
    .catch(function() { btn.disabled = false; btn.textContent = 'Ya, Tukar!'; alert('Terjadi kesalahan.'); });
}
function tutupModalSuksesTukar() {
    document.getElementById('modalSuksesTukar').classList.remove('show');
    location.reload();
}
</script>
@endpush
