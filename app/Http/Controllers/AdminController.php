<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lapak;
use App\Models\LapakPengajuan;
use App\Models\Produk;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    // ============================================================
    // AUTH ADMIN
    // ============================================================
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->with('error', 'Akun ini bukan akun administrator.');
            }
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    // ============================================================
    // DASHBOARD
    // ============================================================
    public function dashboard()
    {
        $stats = [
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'total_lapak'     => Lapak::count(),
            'total_order'     => Order::count(),
            'total_produk'    => Produk::count(),
            'blacklisted'     => User::where('is_blacklisted', true)->count(),
            'pengajuan_lapak_pending' => LapakPengajuan::where('status', 'pending')->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    // ============================================================
    // MANAJEMEN MAHASISWA
    // ============================================================
    public function indexMahasiswa(Request $request)
    {
        $query = User::where('role', 'mahasiswa');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('nim', 'like', "%{$request->search}%");
            });
        }

        if ($request->status === 'blacklisted') {
            $query->where('is_blacklisted', true);
        } elseif ($request->status === 'active') {
            $query->where('is_blacklisted', false);
        }

        $mahasiswas = $query->paginate(20);

        return view('admin.mahasiswa.index', compact('mahasiswas'));
    }

    public function editMahasiswa(User $user)
    {
        return view('admin.mahasiswa.edit', compact('user'));
    }

    public function updateMahasiswa(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nim'      => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:5',
            'prodi'    => 'nullable|string|max:100',
            'password' => ['nullable', Password::min(8)],
        ]);

        $data = $request->only(['name', 'username', 'email', 'nim', 'semester', 'prodi']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $namaMahasiswa = $user->fresh()->name ?? $user->fresh()->username;
        return redirect()->route('admin.mahasiswa.index')
            ->with('success', "Data mahasiswa {$namaMahasiswa} berhasil diperbarui.");
    }

    public function toggleBlacklist(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Tidak dapat memblacklist akun admin.'], 403);
        }

        $user->update(['is_blacklisted' => !$user->is_blacklisted]);

        $status = $user->is_blacklisted ? 'diblacklist' : 'diaktifkan kembali';
        $nama   = $user->name ?? $user->username;

        return response()->json([
            'success'     => true,
            'blacklisted' => $user->is_blacklisted,
            'message'     => "Mahasiswa {$nama} berhasil {$status}.",
        ]);
    }

    public function updatePoin(Request $request, User $user)
    {
        $request->validate([
            'jumlah_poin' => 'required|integer|min:0',
            'alasan'      => 'required|string|max:255',
        ]);

        // Gunakan kolom poin langsung di users table (bukan model Poin terpisah)
        $user->update(['poin' => $request->jumlah_poin]);

        return response()->json([
            'success' => true,
            'message' => "Poin " . ($user->name ?? $user->username) . " diperbarui menjadi {$request->jumlah_poin}.",
        ]);
    }

    // ============================================================
    // MANAJEMEN LAPAK
    // BUG FIX: ganti 'nama_lapak' -> 'nama_toko' (sesuai kolom di DB)
    // ============================================================
    public function indexLapak(Request $request)
    {
        $lapaks = Lapak::with('user')
            ->withCount('produks')
            ->when($request->search, fn($q) => $q->where('nama_toko', 'like', "%{$request->search}%"))
            ->paginate(20);

        // Deteksi duplikasi nama lapak (case-insensitive)
        $duplikat = Lapak::selectRaw('LOWER(nama_toko) as nama_lower, COUNT(*) as jumlah')
            ->groupBy('nama_lower')
            ->having('jumlah', '>', 1)
            ->pluck('nama_lower')
            ->toArray();

        // Permintaan pembukaan lapak baru yang menunggu approval
        $pengajuans = LapakPengajuan::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $riwayatPengajuan = LapakPengajuan::with('user')
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.lapak.index', compact('lapaks', 'duplikat', 'pengajuans', 'riwayatPengajuan'));
    }

    /* ── Setujui pengajuan buka lapak: buat record Lapak baru ── */
    public function approvePengajuanLapak(LapakPengajuan $pengajuan)
    {
        if (!$pengajuan->isPending()) {
            return response()->json(['success' => false, 'message' => 'Pengajuan ini sudah diproses sebelumnya.'], 422);
        }

        // Cegah nama_toko bentrok kalau ada lapak lain terlanjur pakai nama yang sama
        $namaSudahDipakai = Lapak::whereRaw('LOWER(nama_toko) = ?', [strtolower($pengajuan->nama_toko)])->exists();
        if ($namaSudahDipakai) {
            return response()->json(['success' => false, 'message' => 'Nama lapak sudah dipakai lapak lain. Tolak pengajuan ini dan minta penjual ganti nama.'], 422);
        }

        $lapak = Lapak::create([
            'user_id'        => $pengajuan->user_id,
            'nama_toko'      => $pengajuan->nama_toko,
            'deskripsi_toko' => $pengajuan->deskripsi_toko,
            'foto_toko'      => $pengajuan->foto_toko,
            'kontak'         => $pengajuan->kontak,
            'kategori'       => $pengajuan->kategori,
            'status'         => 'aktif',
        ]);

        $pengajuan->update([
            'status'       => 'disetujui',
            'lapak_id'     => $lapak->id,
            'disetujui_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Lapak \"{$lapak->nama_toko}\" disetujui dan sudah tayang di Pojok Jajan.",
        ]);
    }

    /* ── Tolak pengajuan buka lapak ── */
    public function rejectPengajuanLapak(Request $request, LapakPengajuan $pengajuan)
    {
        if (!$pengajuan->isPending()) {
            return response()->json(['success' => false, 'message' => 'Pengajuan ini sudah diproses sebelumnya.'], 422);
        }

        $request->validate(['catatan_admin' => 'nullable|string|max:255']);

        $pengajuan->update([
            'status'        => 'ditolak',
            'catatan_admin' => $request->catatan_admin ?: 'Ditolak oleh admin.',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pengajuan lapak \"{$pengajuan->nama_toko}\" ditolak.",
        ]);
    }

    public function deleteLapak(Lapak $lapak)
    {
        $nama = $lapak->nama_toko;
        $lapak->delete();

        return response()->json([
            'success' => true,
            'message' => "Lapak \"{$nama}\" berhasil dihapus.",
        ]);
    }

    public function hapusDuplikatLapak()
    {
        $duplikats = Lapak::selectRaw('LOWER(nama_toko) as nama_lower, MIN(id) as keep_id')
            ->groupBy('nama_lower')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        $hapus = 0;
        foreach ($duplikats as $dup) {
            $hapus += Lapak::whereRaw('LOWER(nama_toko) = ?', [$dup->nama_lower])
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "{$hapus} lapak duplikat berhasil dihapus.",
        ]);
    }

    // ============================================================
    // MANAJEMEN PRODUK
    // BUG FIX: existing Produk uses 'nama_produk', patch used 'nama'
    // ============================================================
    public function indexProduk(Request $request)
    {
        $produks = Produk::with(['lapak.user'])
            ->when($request->search, fn($q) => $q->where('nama_produk', 'like', "%{$request->search}%"))
            ->paginate(20);

        return view('admin.produk.index', compact('produks'));
    }

    public function deleteProduk(Produk $produk)
    {
        $nama = $produk->nama_produk;
        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => "Produk \"{$nama}\" berhasil dihapus.",
        ]);
    }
}
