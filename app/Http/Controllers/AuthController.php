<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* ── Tampilkan form login ── */
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    /* ── Proses login ── */
    public function login(Request $request)
    {
        $request->validate([
            'nim'      => 'required|string',
            'password' => 'required|string',
        ], [
            'nim.required'      => 'NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Coba login dengan NIM
        $user = User::where('nim', $request->nim)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['nim' => 'NIM atau password salah.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /* ── Tampilkan form register ── */
    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.register');
    }

    /* ── Proses register ── */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'nim'      => 'required|string|max:20|unique:users',
            'email'    => 'required|email|max:150|unique:users',
            'jurusan'  => 'required|string',
            'semester' => 'required|integer|min:1|max:8',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.unique'  => 'Username sudah dipakai.',
            'nim.unique'       => 'NIM sudah terdaftar.',
            'email.unique'     => 'Email sudah terdaftar.',
            'password.min'     => 'Password minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'username' => $request->username,
            'nim'      => $request->nim,
            'email'    => $request->email,
            'jurusan'  => $request->jurusan,
            'semester' => $request->semester,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /* ── Logout ── */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
