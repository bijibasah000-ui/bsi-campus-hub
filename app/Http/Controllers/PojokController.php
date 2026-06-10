<?php
namespace App\Http\Controllers;

use App\Models\Lapak;
use App\Models\Produk;
use App\Models\ProdukRating;
use App\Models\Pesanan;
use App\Models\PoinLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PojokController extends Controller
{
    const POIN_PER_PEMBELIAN = 300; // poin per 1 qty pembelian

    /* ── Index: tampilkan semua produk & lapak ── */
    public function index()
    {
        $lapaks  = Lapak::where('status','aktif')->with('produks')->get();
        $produks = Produk::where('status','tersedia')->with('lapak')->latest()->get();
        $myLapak = Auth::check() ? Auth::user()->lapak : null;
        return view('pojok.index', compact('lapaks','produks','myLapak'));
    }

    /* ── Form buka lapak ── */
    public function bukaLapak()
    {
        $myLapak = Auth::user()->lapak;
        return view('pojok.buka-lapak', compact('myLapak'));
    }

    /* ── Simpan / update lapak ── */
    public function simpanLapak(Request $request)
    {
        $request->validate([
            'nama_toko'      => 'required|string|max:100',
            'deskripsi_toko' => 'nullable|string|max:500',
            'kontak'         => 'nullable|string|max:20',
            'foto_toko'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user  = Auth::user();
        $lapak = $user->lapak ?? new Lapak(['user_id' => $user->id]);
        $data  = $request->only(['nama_toko','deskripsi_toko','kontak']);

        if ($request->hasFile('foto_toko')) {
            if ($lapak->foto_toko) Storage::disk('public')->delete($lapak->foto_toko);
            $data['foto_toko'] = $request->file('foto_toko')->store('lapak','public');
        }

        $lapak->fill($data)->save();
        return redirect()->route('pojok.index')->with('success','Lapak berhasil disimpan!');
    }

    /* ── Form tambah produk ── */
    public function tambahProduk()
    {
        $lapak = Auth::user()->lapak;
        if (!$lapak) return redirect()->route('pojok.buka-lapak')->with('error','Buat lapak dulu ya!');
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

        $data = $request->only(['nama_produk','jenis','harga','deskripsi','status']);
        $data['lapak_id'] = $lapak->id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('produk','public');
        }

        Produk::create($data);
        return redirect()->route('pojok.index')->with('success','Produk berhasil ditambahkan!');
    }

    /* ── Hapus produk ── */
    public function hapusProduk(Produk $produk)
    {
        if ($produk->lapak->user_id !== Auth::id()) abort(403);
        if ($produk->foto) Storage::disk('public')->delete($produk->foto);
        $produk->delete();
        return back()->with('success','Produk dihapus.');
    }

    /* ── Detail produk (AJAX / modal) ── */
    public function detailProduk(Produk $produk)
    {
        $produk->load('lapak','ratings.user');
        $myRating = Auth::check()
            ? ProdukRating::where('produk_id',$produk->id)->where('user_id',Auth::id())->first()
            : null;
        return response()->json([
            'produk'    => $produk,
            'foto_url'  => $produk->foto ? Storage::url($produk->foto) : null,
            'my_rating' => $myRating,
        ]);
    }

    /* ── Beri rating ── */
    public function beriRating(Request $request, Produk $produk)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:300',
        ]);

        $existing = ProdukRating::where('produk_id',$produk->id)->where('user_id',Auth::id())->first();

        if ($existing) {
            $existing->update(['rating' => $request->rating, 'komentar' => $request->komentar]);
        } else {
            ProdukRating::create([
                'produk_id' => $produk->id,
                'user_id'   => Auth::id(),
                'rating'    => $request->rating,
                'komentar'  => $request->komentar,
            ]);
        }

        // Recalculate avg
        $avg = ProdukRating::where('produk_id',$produk->id)->avg('rating');
        $cnt = ProdukRating::where('produk_id',$produk->id)->count();
        $produk->update(['rating_avg' => round($avg,2), 'rating_count' => $cnt]);

        return response()->json(['ok' => true, 'avg' => $avg, 'count' => $cnt]);
    }

    /* ── Pesan produk ── */
    public function pesanProduk(Request $request, Produk $produk)
    {
        $request->validate(['jumlah' => 'required|integer|min:1|max:99']);

        if ($produk->status !== 'tersedia') {
            return response()->json(['ok' => false, 'msg' => 'Produk sudah habis.']);
        }

        $jumlah      = $request->jumlah;
        $totalHarga  = $produk->harga * $jumlah;
        $poinDidapat = self::POIN_PER_PEMBELIAN * $jumlah;
        $user        = Auth::user();

        // Buat pesanan
        Pesanan::create([
            'user_id'      => $user->id,
            'produk_id'    => $produk->id,
            'jumlah'       => $jumlah,
            'total_harga'  => $totalHarga,
            'poin_didapat' => $poinDidapat,
            'status'       => 'pending',
        ]);

        // Tambah poin
        $user->increment('poin', $poinDidapat);

        // Log poin
        PoinLog::create([
            'user_id'    => $user->id,
            'jumlah'     => $poinDidapat,
            'keterangan' => 'Pembelian: '.$produk->nama_produk.' (x'.$jumlah.')',
            'referensi'  => 'produk_'.$produk->id,
            'tipe'       => 'pembelian',
        ]);

        return response()->json([
            'ok'           => true,
            'poin_didapat' => $poinDidapat,
            'poin_total'   => $user->fresh()->poin,
        ]);
    }
}
