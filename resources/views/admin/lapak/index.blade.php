{{-- resources/views/admin/lapak/index.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Kelola Lapak — Pojok Jajan')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kelola Lapak — Pojok Jajan</h2>
            @if(count($duplikat) > 0)
                <p class="text-sm text-red-500 mt-0.5">⚠ Ditemukan {{ count($duplikat) }} nama lapak duplikat</p>
            @endif
        </div>
        <div class="flex gap-2">
            @if(count($duplikat) > 0)
                <button onclick="hapusDuplikat()"
                    class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Semua Duplikat
                </button>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">← Dashboard</a>
        </div>
    </div>

    {{-- Peringatan duplikat --}}
    @if(count($duplikat) > 0)
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <h3 class="font-semibold text-red-700 text-sm mb-2">Nama lapak yang terduplikasi:</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($duplikat as $nama)
                <span class="bg-red-100 text-red-700 text-xs font-medium px-3 py-1 rounded-full">{{ $nama }}</span>
            @endforeach
        </div>
        <p class="text-red-500 text-xs mt-3">Klik "Hapus Semua Duplikat" untuk menyisakan 1 lapak per nama (yang dibuat pertama akan dipertahankan).</p>
    </div>
    @endif

    {{-- Search --}}
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lapak..."
            class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
        <button type="submit" class="bg-orange-500 text-white px-5 py-2 rounded-xl text-sm hover:bg-orange-600 transition">Cari</button>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nama Lapak</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Pemilik</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Produk</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($lapaks as $lapak)
                <tr class="hover:bg-gray-50/50 transition
                    {{ in_array(strtolower($lapak->nama_toko), $duplikat) ? 'bg-red-50/40' : '' }}">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800 flex items-center gap-2">
                            {{ $lapak->nama_toko }}
                            @if(in_array(strtolower($lapak->nama_toko), $duplikat))
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">duplikat</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-700">{{ $lapak->user->name ?? '-' }}</div>
                        <div class="text-gray-400 text-xs">{{ $lapak->user->email ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $lapak->produks_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="hapusLapak({{ $lapak->id }}, '{{ addslashes($lapak->nama_toko) }}')"
                            class="text-red-500 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                            Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-12 text-gray-400">Tidak ada lapak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $lapaks->withQueryString()->links() }}
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function hapusLapak(id, nama) {
    if (!confirm(`Hapus lapak "${nama}"? Semua produknya juga akan terhapus.`)) return;
    const res = await fetch(`/admin/lapak/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data.success) { alert(data.message); location.reload(); }
}

async function hapusDuplikat() {
    if (!confirm('Hapus semua lapak duplikat? Lapak yang dibuat pertama akan dipertahankan.')) return;
    const res = await fetch('/admin/lapak/hapus-duplikat', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data.success) { alert(data.message); location.reload(); }
}
</script>
@endsection
