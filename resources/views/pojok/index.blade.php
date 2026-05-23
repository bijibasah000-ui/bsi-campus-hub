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
        {{ $myLapak ? '⚙️ Kelola Lapakku' : '+ Buka Lapakmu' }}
    </a>
</div>

{{-- Filter kategori --}}
<div class="filter-bar" id="filterBar">
    <button class="fb active" data-filter="semua">Semua</button>
    <button class="fb" data-filter="makanan">🍔 Makanan</button>
    <button class="fb" data-filter="minuman">🧋 Minuman</button>
    <button class="fb" data-filter="barang">📦 Barang</button>
</div>

{{-- Chips lapak aktif --}}
@if($lapaks->count())
<div class="lapak-chips">
    @foreach($lapaks as $l)
        <span class="lapak-chip">🏠 {{ $l->nama_toko }}</span>
    @endforeach
</div>
@endif

{{-- Grid produk --}}
@if($produks->count())
<div class="food-grid" id="foodGrid">
    @foreach($produks as $p)
    <div class="food-card" data-jenis="{{ $p->jenis }}">
        <div class="food-thumb">
            @if($p->foto)
                <img src="{{ Storage::url($p->foto) }}" alt="{{ $p->nama_produk }}"
                     style="width:100%;height:100%;object-fit:cover;">
            @else
                {{ $p->jenis === 'makanan' ? '🍱' : ($p->jenis === 'minuman' ? '🧋' : '📦') }}
            @endif
        </div>
        <div class="food-body">
            <div class="food-name">{{ $p->nama_produk }}</div>
            <div class="food-lapak">{{ $p->lapak->nama_toko ?? '-' }}</div>
            @if($p->deskripsi)
                <div style="font-size:10px;color:var(--text-3);margin-top:2px;">{{ Str::limit($p->deskripsi,40) }}</div>
            @endif
            <div class="food-price">Rp {{ number_format($p->harga,0,',','.') }}</div>
            <span class="badge {{ $p->status==='tersedia' ? 'badge-green' : 'badge-red' }}" style="margin-top:4px;">
                {{ $p->status === 'tersedia' ? 'Tersedia' : 'Habis' }}
            </span>
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

@endsection

@push('scripts')
<script>
document.querySelectorAll('#filterBar .fb').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('#filterBar .fb').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        var filter = this.dataset.filter;
        document.querySelectorAll('#foodGrid .food-card').forEach(function(card) {
            card.style.display = (filter === 'semua' || card.dataset.jenis === filter) ? 'block' : 'none';
        });
    });
});
</script>
@endpush
