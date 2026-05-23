<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'username'      => 'required|string|max:100',
            'email'         => 'required|email|max:150|unique:users,email,' . Auth::id(),
            'jurusan'       => 'nullable|string|max:80',
            'semester'      => 'nullable|integer|min:1|max:8',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'foto'          => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ], [
            'email.unique'      => 'Email sudah dipakai akun lain.',
            'foto.mimes'        => 'Foto harus berformat JPG, PNG, GIF, atau WEBP.',
            'foto.max'          => 'Ukuran foto maksimal 4MB.',
        ]);

        $user = Auth::user();

        // Handle upload foto (termasuk GIF)
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $file      = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $filename  = 'foto_' . $user->id . '_' . time() . '.' . $extension;

            // Simpan ke public/storage/foto-profil
            $file->move(public_path('storage/foto-profil'), $filename);

            // Hapus foto lama kalau ada
            if ($user->foto && file_exists(public_path('storage/foto-profil/' . $user->foto))) {
                @unlink(public_path('storage/foto-profil/' . $user->foto));
            }

            $user->foto = $filename;
        }

        // Update data profil
        $user->username      = $request->username;
        $user->email         = $request->email;
        $user->jurusan       = $request->jurusan;
        $user->semester      = $request->semester;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}