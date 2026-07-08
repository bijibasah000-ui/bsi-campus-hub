<?php
namespace App\Http\Controllers;

use App\Models\Lapak;
use App\Models\LapakPengajuan;
use App\Models\Produk;
use App\Models\ProdukRating;
use App\Models\Pesanan;
use App\Models\PoinLog;
use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PojokController extends Controller
{
    const POIN_PER_PEMBELIAN = 300;

    /* ── Index: tampilkan semua produk & lapak ── */
    public function index()
    {
        $lapaks  = Lapak::where('status', 'aktif')->with('produks')->get();
        $produks = Produk::where('status', 'tersedia')->with('lapak')->latest()->get();
        $myLapak = Auth::check() ? Auth::user()->lapak : null;
        $myPengajuan = (Auth::check() && !$myLapak)
            ? LapakPengajuan::where('user_id', Auth::id())->latest()->first()
            : null;
        return view('pojok.index', compact('lapaks', 'produks', 'myLapak', 'myPengajuan'));
    }

    /* ── Form buka lapak (kini berupa pengajuan + pembayaran, butuh approval admin) ── */
    public function bukaLapak()
    {
        $user  = Auth::user();
        $myLapak = $user->lapak;

        // Kalau sudah punya lapak aktif, tetap tampilkan halaman kelola (edit info + produk) seperti biasa
        if ($myLapak) {
            return view('pojok.buka-lapak', [
                'myLapak'    => $myLapak,
                'pengajuan'  => null,
                'paketHarga' => LapakPengajuan::PAKET_HARGA,
            ]);
        }

        // Kalau belum punya lapak, cek status pengajuan terakhir
        $pengajuan = LapakPengajuan::where('user_id', $user->id)->latest()->first();

        return view('pojok.buka-lapak', [
            'myLapak'   => null,
            'pengajuan' => $pengajuan, // null = belum pernah ajukan, pending/ditolak/disetujui sesuai status
            'paketHarga' => LapakPengajuan::PAKET_HARGA,
        ]);
    }

    /* ── Simpan pengajuan buka lapak baru (butuh approval admin + pembayaran) ── */
    public function ajukanLapak(Request $request)
    {
        $user = Auth::user();

        // Safety: kalau sudah punya lapak aktif atau pengajuan pending, jangan bisa ajukan lagi
        if ($user->lapak) {
            return redirect()->route('pojok.buka-lapak')->with('error', 'Kamu sudah punya lapak aktif.');
        }
        $pengajuanAktif = LapakPengajuan::where('user_id', $user->id)->where('status', 'pending')->exists();
        if ($pengajuanAktif) {
            return redirect()->route('pojok.buka-lapak')->with('error', 'Kamu masih punya pengajuan yang sedang menunggu approval admin.');
        }

        $request->validate([
            'nama_toko'         => 'required|string|max:100|unique:lapaks,nama_toko',
            'deskripsi_toko'    => 'nullable|string|max:500',
            'kontak'            => 'nullable|string|max:20',
            'foto_toko'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'durasi_bulan'      => 'required|integer|min:1|max:6',
            'metode_pembayaran' => 'required|in:qris,tunai',
        ]);

        $paket = LapakPengajuan::PAKET_HARGA;
        $durasi = (int) $request->durasi_bulan;
        $harga  = $paket[$durasi] ?? $paket[1];

        $data = $request->only(['nama_toko', 'deskripsi_toko', 'kontak']);
        if ($request->hasFile('foto_toko')) {
            $data['foto_toko'] = $request->file('foto_toko')->store('lapak', 'public');
        }

        LapakPengajuan::create(array_merge($data, [
            'user_id'           => $user->id,
            'durasi_bulan'      => $durasi,
            'harga'             => $harga,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status'            => 'pending',
        ]));

        return redirect()->route('pojok.buka-lapak')
            ->with('success', 'Permintaan pembukaan lapak berhasil diajukan! Menunggu persetujuan admin ya.');
    }

    /* ── Batalkan pengajuan yang masih pending ── */
    public function batalkanPengajuanLapak(LapakPengajuan $pengajuan)
    {
        if ($pengajuan->user_id !== Auth::id()) abort(403);
        if (!$pengajuan->isPending()) {
            return redirect()->route('pojok.buka-lapak')->with('error', 'Pengajuan ini sudah tidak bisa dibatalkan.');
        }
        $pengajuan->delete();
        return redirect()->route('pojok.buka-lapak')->with('success', 'Pengajuan dibatalkan.');
    }

    /* ── Update info lapak yang SUDAH disetujui (tidak butuh approval ulang) ── */
    public function simpanLapak(Request $request)
    {
        $user  = Auth::user();
        $lapak = $user->lapak;

        // Buka lapak baru sudah tidak lewat sini lagi — wajib lewat alur pengajuan (ajukanLapak)
        if (!$lapak) {
            return redirect()->route('pojok.buka-lapak')->with('error', 'Silakan ajukan pembukaan lapak terlebih dahulu.');
        }

        $request->validate([
            'nama_toko'      => 'required|string|max:100|unique:lapaks,nama_toko,' . $lapak->id,
            'deskripsi_toko' => 'nullable|string|max:500',
            'kontak'         => 'nullable|string|max:20',
            'foto_toko'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['nama_toko', 'deskripsi_toko', 'kontak']);

        if ($request->hasFile('foto_toko')) {
            if ($lapak->foto_toko) Storage::disk('public')->delete($lapak->foto_toko);
            $data['foto_toko'] = $request->file('foto_toko')->store('lapak', 'public');
        }

        $lapak->fill($data)->save();
        return redirect()->route('pojok.index')->with('success', 'Lapak berhasil disimpan!');
    }

    /* ── Form tambah produk ── */
    public function tambahProduk()
    {
        $lapak = Auth::user()->lapak;
        if (!$lapak) return redirect()->route('pojok.buka-lapak')->with('error', 'Buat lapak dulu ya!');
        return view('pojok.tambah-produk', compact('lapak'));
    }

    /* ── Simpan produk baru ── */
    public function simpanProduk(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'jenis'       => 'required|in:makanan,minuman,barang',
            'harga'       => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string|max:500',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:tersedia,habis',
        ]);

        $lapak = Auth::user()->lapak;
        if (!$lapak) return redirect()->route('pojok.buka-lapak');

        $data             = $request->only(['nama_produk', 'jenis', 'harga', 'deskripsi', 'status']);
        $data['lapak_id'] = $lapak->id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('produk', 'public');
        }

        Produk::create($data);
        return redirect()->route('pojok.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /* ── Hapus produk ── */
    public function hapusProduk(Produk $produk)
    {
        if ($produk->lapak->user_id !== Auth::id()) abort(403);
        if ($produk->foto) Storage::disk('public')->delete($produk->foto);
        $produk->delete();
        return back()->with('success', 'Produk dihapus.');
    }

    /* ── Detail produk (AJAX / modal) ── */
    public function detailProduk(Produk $produk)
    {
        $produk->load('lapak', 'ratings.user');
        $myRating = Auth::check()
            ? ProdukRating::where('produk_id', $produk->id)->where('user_id', Auth::id())->first()
            : null;
        return response()->json([
            'produk'    => $produk,
            'foto_url'  => $produk->foto ? Storage::url($produk->foto) : null,
            'my_rating' => $myRating,
        ]);
    }

    /* ── Pesan produk (PATCH FIX 1: cegah beli sendiri + metode pembayaran) ── */
    public function pesanProduk(Request $request, Produk $produk)
    {
        $user = Auth::user();

        // FIX 1: Cek blacklist
        if ($user->is_blacklisted) {
            return response()->json(['ok' => false, 'msg' => 'Akun Anda telah dinonaktifkan.'], 403);
        }

        // FIX 1: Penjual tidak bisa beli produk dari lapak sendiri
        $lapakMilikUser = Lapak::where('user_id', $user->id)
            ->where('id', $produk->lapak_id)
            ->exists();

        if ($lapakMilikUser) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Anda tidak dapat membeli produk dari lapak Anda sendiri.',
            ], 403);
        }

        $request->validate([
            'jumlah'            => 'required|integer|min:1|max:99',
            'metode_pembayaran' => 'nullable|in:qris,tunai',
        ]);

        if ($produk->status !== 'tersedia') {
            return response()->json(['ok' => false, 'msg' => 'Produk sudah habis.']);
        }

        $jumlah            = $request->jumlah;
        $totalHarga        = $produk->harga * $jumlah;
        $poinDidapat       = self::POIN_PER_PEMBELIAN * $jumlah;
        // Fallback aman: kalau UI tidak mengirim metode (mis. gagal load JS), default ke 'tunai'
        $metodePembayaran  = $request->input('metode_pembayaran', 'tunai');

        DB::transaction(function () use ($request, $produk, $user, $jumlah, $totalHarga, $poinDidapat, $metodePembayaran) {
            // Buat order baru (dengan metode_pembayaran)
            Order::create([
                'user_id'           => $user->id,
                'produk_id'         => $produk->id,
                'jumlah'            => $jumlah,
                'total_harga'       => $totalHarga,
                'metode_pembayaran' => $metodePembayaran,
                'status'            => 'pending',
            ]);

            // Juga catat di pesanans (backward compat)
            Pesanan::create([
                'user_id'      => $user->id,
                'produk_id'    => $produk->id,
                'jumlah'       => $jumlah,
                'total_harga'  => $totalHarga,
                'poin_didapat' => $poinDidapat,
                'status'       => 'pending',
            ]);

            // Tambah poin via kolom langsung di users
            $user->increment('poin', $poinDidapat);

            // Log poin
            PoinLog::create([
                'user_id'    => $user->id,
                'jumlah'     => $poinDidapat,
                'keterangan' => 'Pembelian: ' . $produk->nama_produk . ' (x' . $jumlah . ')',
                'referensi'  => 'produk_' . $produk->id,
                'tipe'       => 'pembelian',
            ]);
        });

        return response()->json([
            'ok'           => true,
            'poin_didapat' => $poinDidapat,
            'poin_total'   => $user->fresh()->poin,
            'message'      => 'Pesanan berhasil dibuat! +' . $poinDidapat . ' poin.',
        ]);
    }

    /* ── Beri rating (PATCH FIX 3: rating hanya setelah beli via Order) ── */
    public function beriRating(Request $request, Produk $produk)
    {
        $user = Auth::user();

        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:300',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        // Jika ada order_id, pakai flow baru (order-verified)
        if ($request->filled('order_id')) {
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->where('produk_id', $produk->id)
                ->where('status', 'selesai')
                ->first();

            if (!$order) {
                return response()->json([
                    'ok'  => false,
                    'msg' => 'Anda hanya dapat memberi rating setelah pesanan selesai.',
                ], 403);
            }

            $sudahRating = Rating::where('user_id', $user->id)
                ->where('produk_id', $produk->id)
                ->where('order_id', $order->id)
                ->exists();

            if ($sudahRating) {
                return response()->json(['ok' => false, 'msg' => 'Rating sudah diberikan untuk pesanan ini.'], 422);
            }

            Rating::create([
                'user_id'   => $user->id,
                'produk_id' => $produk->id,
                'order_id'  => $order->id,
                'bintang'   => $request->rating,
                'komentar'  => $request->komentar,
            ]);
        } else {
            // Fallback: flow lama pakai ProdukRating
            $existing = ProdukRating::where('produk_id', $produk->id)->where('user_id', $user->id)->first();

            if ($existing) {
                $existing->update(['rating' => $request->rating, 'komentar' => $request->komentar]);
            } else {
                ProdukRating::create([
                    'produk_id' => $produk->id,
                    'user_id'   => $user->id,
                    'rating'    => $request->rating,
                    'komentar'  => $request->komentar,
                ]);
            }
        }

        // Recalculate avg dari ProdukRating (existing system)
        $avg = ProdukRating::where('produk_id', $produk->id)->avg('rating');
        $cnt = ProdukRating::where('produk_id', $produk->id)->count();
        $produk->update(['rating_avg' => round($avg, 2), 'rating_count' => $cnt]);

        return response()->json(['ok' => true, 'avg' => $avg, 'count' => $cnt]);
    }

    /* ── Ambil orders belum di-rating (untuk flow rating baru) ── */
    public function getOrdersBelumDirating(Produk $produk)
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('produk_id', $produk->id)
            ->where('status', 'selesai')
            ->whereDoesntHave('rating')
            ->get(['id', 'jumlah', 'created_at']);

        return response()->json([
            'orders'       => $orders,
            'boleh_rating' => $orders->isNotEmpty(),
        ]);
    }

    /* ── PATCH FIX 2: Cek nama lapak unik (AJAX) ── */
    public function checkNamaToko(Request $request)
    {
        $nama = trim($request->nama_toko);

        $exists = Lapak::whereRaw('LOWER(nama_toko) = ?', [strtolower($nama)])->exists();

        return response()->json([
            'available' => !$exists,
            'message'   => $exists
                ? "Nama lapak \"{$nama}\" sudah digunakan. Silakan pilih nama lain."
                : "Nama lapak tersedia!",
        ]);
    }
}
