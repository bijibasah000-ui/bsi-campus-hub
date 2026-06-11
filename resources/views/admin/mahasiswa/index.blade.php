{{-- resources/views/admin/mahasiswa/index.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Manajemen Mahasiswa')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Manajemen Mahasiswa</h2>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Dashboard</a>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" class="flex gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIM..."
            class="flex-1 min-w-48 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
        <select name="status" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Semua Status</option>
            <option value="active" @selected(request('status') === 'active')>Aktif</option>
            <option value="blacklisted" @selected(request('status') === 'blacklisted')>Blacklisted</option>
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-xl text-sm hover:bg-indigo-700 transition">Cari</button>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Mahasiswa</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">NIM / Semester</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Poin</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($mahasiswas as $mhs)
                <tr class="hover:bg-gray-50/50 transition {{ $mhs->is_blacklisted ? 'bg-red-50/30' : '' }}">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $mhs->name }}</div>
                        <div class="text-gray-400 text-xs">{{ $mhs->email }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-700">{{ $mhs->nim ?? '-' }}</div>
                        <div class="text-gray-400 text-xs">{{ $mhs->semester ? 'Sem ' . $mhs->semester : '-' }} • {{ $mhs->prodi ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-semibold text-orange-600">{{ number_format($mhs->poin?->jumlah_poin ?? 0) }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($mhs->is_blacklisted)
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Blacklisted
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.mahasiswa.edit', $mhs) }}"
                                class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                                Edit
                            </a>
                            <button onclick="toggleBlacklist({{ $mhs->id }}, '{{ $mhs->name }}', {{ $mhs->is_blacklisted ? 'true' : 'false' }})"
                                class="text-xs font-medium px-3 py-1.5 rounded-lg transition
                                    {{ $mhs->is_blacklisted
                                        ? 'text-green-600 hover:text-green-800 hover:bg-green-50'
                                        : 'text-red-500 hover:text-red-700 hover:bg-red-50' }}">
                                {{ $mhs->is_blacklisted ? 'Whitelist' : 'Blacklist' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-gray-400">Tidak ada data mahasiswa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $mahasiswas->withQueryString()->links() }}
</div>

<script>
async function toggleBlacklist(id, name, isBlacklisted) {
    const action = isBlacklisted ? 'whitelist' : 'blacklist';
    if (!confirm(`Yakin ingin ${action} akun ${name}?`)) return;

    const res = await fetch(`/admin/mahasiswa/${id}/blacklist`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    });
    const data = await res.json();
    if (data.success) {
        alert(data.message);
        location.reload();
    }
}
</script>
@endsection
