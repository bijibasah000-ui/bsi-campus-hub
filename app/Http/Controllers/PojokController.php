<?php
namespace App\Http\Controllers;

use App\Models\Lapak;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PojokController extends Controller
{
    /* ── Index: tampilkan semua produk & lapak ── */
    public function index()
    {
        $lapaks   = Lapak::where('status','aktif')->with('produks')->get();
        $produks  = Produk::where('status','tersedia')->with('lapak')->latest()->get();
        $myLapak  = Auth::check() ? Auth::user()->lapak : null;
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

        $user    = Auth::user();
        $lapak   = $user->lapak ?? new Lapak(['user_id' => $user->id]);
        $data    = $request->only(['nama_toko','deskripsi_toko','kontak']);

        if ($request->hasFile('foto_toko')) {
            if ($lapak->foto_toko) Storage::delete('public/'.$lapak->foto_toko);
            $data['foto_toko'] = $request->file('foto_toko')->store('lapak','public');
        }

        $lapak->fill($data);
        $lapak->save();

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
        if ($produk->foto) Storage::delete('public/'.$produk->foto);
        $produk->delete();
        return back()->with('success','Produk dihapus.');
    }
}
