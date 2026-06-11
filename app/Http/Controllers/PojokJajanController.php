<?php
// app/Http/Controllers/PojokJajanController.php
namespace App\Http\Controllers;

use App\Models\Lapak;
use App\Models\Produk;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Poin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PojokJajanController extends Controller
{
    // ============================================================
    // FIX 1: Penjual tidak bisa membeli produknya sendiri
    // ============================================================
    public function pesanProduk(Request $request, Produk $produk)
    {
        $user = Auth::user();

        // Cek apakah user punya lapak yang memiliki produk ini
        $lapakMilikUser = Lapak::where('user_id', $user->id)
            ->where('id', $produk->lapak_id)
            ->exists();

        if ($lapakMilikUser) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat membeli produk dari lapak Anda sendiri.',
            ], 403);
        }

        $request->validate([
            'jumlah'            => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:qris,tunai',
        ]);

        DB::transaction(function () use ($request, $produk, $user) {
            $jumlah     = $request->jumlah;
            $totalHarga = $produk->harga * $jumlah;
            $poinDapat  = 300 * $jumlah;

            // Buat order
            $order = Order::create([
                'user_id'           => $user->id,
                'produk_id'         => $produk->id,
                'jumlah'            => $jumlah,
                'total_harga'       => $totalHarga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status'            => 'pending',
            ]);

            // Tambah poin pembeli
            Poin::updateOrCreate(
                ['user_id' => $user->id],
                ['jumlah_poin' => DB::raw("jumlah_poin + {$poinDapat}")]
            );

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat! +300 poin per item.',
        ]);
    }

    // ============================================================
    // FIX 2: Cek duplikasi nama lapak
    // ============================================================
    public function checkNamaLapak(Request $request)
    {
        $nama = trim($request->nama_lapak);

        $exists = Lapak::whereRaw('LOWER(nama_lapak) = ?', [strtolower($nama)])->exists();

        return response()->json([
            'available' => !$exists,
            'message'   => $exists
                ? "Nama lapak \"{$nama}\" sudah digunakan. Silakan pilih nama lain."
                : "Nama lapak tersedia!",
        ]);
    }

    public function storeLapak(Request $request)
    {
        $request->validate([
            'nama_lapak' => [
                'required',
                'string',
                'max:100',
                // Unique case-insensitive
                function ($attribute, $value, $fail) {
                    $exists = Lapak::whereRaw('LOWER(nama_lapak) = ?', [strtolower($value)])->exists();
                    if ($exists) {
                        $fail("Nama lapak \"{$value}\" sudah digunakan. Silakan pilih nama lain.");
                    }
                },
            ],
            'deskripsi' => 'nullable|string',
            'kategori'  => 'required|string',
        ]);

        $lapak = Lapak::create([
            'user_id'    => Auth::id(),
            'nama_lapak' => $request->nama_lapak,
            'deskripsi'  => $request->deskripsi,
            'kategori'   => $request->kategori,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lapak berhasil dibuat!',
            'lapak'   => $lapak,
        ]);
    }

    // ============================================================
    // FIX 3: Rating hanya bisa setelah membeli (berdasarkan order)
    // ============================================================
    public function submitRating(Request $request, Produk $produk)
    {
        $user = Auth::user();

        $request->validate([
            'bintang'  => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
            'order_id' => 'required|exists:orders,id',
        ]);

        // Verifikasi order milik user ini dan sudah selesai
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->where('produk_id', $produk->id)
            ->where('status', 'selesai')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat memberi rating setelah membeli dan menyelesaikan pesanan produk ini.',
            ], 403);
        }

        // Cek sudah pernah rating di order ini
        $sudahRating = Rating::where('user_id', $user->id)
            ->where('produk_id', $produk->id)
            ->where('order_id', $order->id)
            ->exists();

        if ($sudahRating) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan rating untuk pesanan ini.',
            ], 422);
        }

        Rating::create([
            'user_id'   => $user->id,
            'produk_id' => $produk->id,
            'order_id'  => $order->id,
            'bintang'   => $request->bintang,
            'komentar'  => $request->komentar,
        ]);

        // Update rata-rata rating di tabel produk
        $avgRating = Rating::where('produk_id', $produk->id)->avg('bintang');
        $produk->update(['rating' => round($avgRating, 1)]);

        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil dikirim! Terima kasih.',
        ]);
    }

    // ============================================================
    // Ambil orders yang belum di-rating milik user (untuk tombol rating)
    // ============================================================
    public function getOrdersBelumDirating(Produk $produk)
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('produk_id', $produk->id)
            ->where('status', 'selesai')
            ->whereDoesntHave('rating')
            ->get(['id', 'jumlah', 'created_at']);

        return response()->json([
            'orders' => $orders,
            'boleh_rating' => $orders->isNotEmpty(),
        ]);
    }
}
