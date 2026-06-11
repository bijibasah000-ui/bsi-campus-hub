<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lapak;
use App\Models\Produk;
use App\Models\Order;
use App\Models\Poin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('nim', 'like', "%{$request->search}%");
            });
        }

        if ($request->status === 'blacklisted') {
            $query->where('is_blacklisted', true);
        } elseif ($request->status === 'active') {
            $query->where('is_blacklisted', false);
        }

        $mahasiswas = $query->with('poin')->paginate(20);

        return view('admin.mahasiswa.index', compact('mahasiswas'));
    }

    public function editMahasiswa(User $user)
    {
        return view('admin.mahasiswa.edit', compact('user'));
    }

    public function updateMahasiswa(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nim'      => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:5',
            'prodi'    => 'nullable|string|max:100',
            'password' => ['nullable', Password::min(8)],
        ]);

        $data = $request->only(['name', 'email', 'nim', 'semester', 'prodi']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', "Data mahasiswa {$user->name} berhasil diperbarui.");
    }

    public function toggleBlacklist(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Tidak dapat memblacklist akun admin.'], 403);
        }

        $user->update(['is_blacklisted' => !$user->is_blacklisted]);

        $status = $user->is_blacklisted ? 'diblacklist' : 'diaktifkan kembali';

        return response()->json([
            'success'      => true,
            'blacklisted'  => $user->is_blacklisted,
            'message'      => "Mahasiswa {$user->name} berhasil {$status}.",
        ]);
    }

    public function updatePoin(Request $request, User $user)
    {
        $request->validate([
            'jumlah_poin' => 'required|integer|min:0',
            'alasan'      => 'required|string|max:255',
        ]);

        Poin::updateOrCreate(
            ['user_id' => $user->id],
            ['jumlah_poin' => $request->jumlah_poin]
        );

        return response()->json([
            'success' => true,
            'message' => "Poin {$user->name} diperbarui menjadi {$request->jumlah_poin}.",
        ]);
    }

    // ============================================================
    // MANAJEMEN LAPAK — hapus duplikasi tag
    // ============================================================
    public function indexLapak(Request $request)
    {
        $lapaks = Lapak::with('user')
            ->withCount('produks')
            ->when($request->search, fn($q) => $q->where('nama_lapak', 'like', "%{$request->search}%"))
            ->paginate(20);

        // Deteksi duplikasi nama lapak (case-insensitive)
        $duplikat = Lapak::selectRaw('LOWER(nama_lapak) as nama_lower, COUNT(*) as jumlah')
            ->groupBy('nama_lower')
            ->having('jumlah', '>', 1)
            ->pluck('nama_lower')
            ->toArray();

        return view('admin.lapak.index', compact('lapaks', 'duplikat'));
    }

    public function deleteLapak(Lapak $lapak)
    {
        $nama = $lapak->nama_lapak;
        $lapak->delete(); // cascade ke produk jika ada

        return response()->json([
            'success' => true,
            'message' => "Lapak \"{$nama}\" berhasil dihapus.",
        ]);
    }

    // Hapus semua duplikat, sisakan yang terlama
    public function hapusDuplikatLapak()
    {
        $duplikats = Lapak::selectRaw('LOWER(nama_lapak) as nama_lower, MIN(id) as keep_id')
            ->groupBy('nama_lower')
            ->having(\DB::raw('COUNT(*)'), '>', 1)
            ->get();

        $hapus = 0;
        foreach ($duplikats as $dup) {
            $hapus += Lapak::whereRaw('LOWER(nama_lapak) = ?', [$dup->nama_lower])
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
    // ============================================================
    public function indexProduk(Request $request)
    {
        $produks = Produk::with(['lapak.user'])
            ->when($request->search, fn($q) => $q->where('nama', 'like', "%{$request->search}%"))
            ->paginate(20);

        return view('admin.produk.index', compact('produks'));
    }

    public function deleteProduk(Produk $produk)
    {
        $nama = $produk->nama;
        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => "Produk \"{$nama}\" berhasil dihapus.",
        ]);
    }
}
