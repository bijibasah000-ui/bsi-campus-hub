@extends('admin.layouts.app')
@section('title', 'Kelola Produk')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Kelola Produk</h5>
        <form action="" method="GET" class="d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari produk..." class="form-control form-control-sm" style="width:200px">
            <button type="submit" class="btn btn-sm btn-primary">Cari</button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Lapak</th>
                    <th>Pemilik</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $produk)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $produk->nama_produk }}</div>
                        <small class="text-muted">{{ ucfirst($produk->jenis) }}</small>
                    </td>
                    <td>{{ $produk->lapak?->nama_toko ?? '-' }}</td>
                    <td>{{ $produk->lapak?->user?->username ?? '-' }}</td>
                    <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $produk->status === 'tersedia' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($produk->status) }}
                        </span>
                    </td>
                    <td>
                        <button onclick="hapusProduk({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}')"
                                class="btn btn-sm btn-outline-danger">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada produk ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $produks->links() }}</div>
</div>

<script>
function hapusProduk(id, nama) {
    if (!confirm('Hapus produk "' + nama + '"?')) return;
    fetch('/admin/produk/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
        else alert(d.message || 'Gagal menghapus.');
    });
}
</script>
@endsection
