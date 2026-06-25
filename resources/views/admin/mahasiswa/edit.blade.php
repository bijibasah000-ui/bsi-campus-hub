{{-- resources/views/admin/mahasiswa/edit.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Edit Mahasiswa')

@section('content')
<div class="space-y-5 max-w-2xl">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit Mahasiswa</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->name ?? $user->username }}</p>
        </div>
        <a href="{{ route('admin.mahasiswa.index') }}"
            class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← Kembali
        </a>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.mahasiswa.update', $user) }}"
        class="bg-white rounded-2xl border border-gray-100 divide-y divide-gray-50">

        @csrf
        @method('PUT')

        {{-- Data Akun --}}
        <div class="p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Data Akun</h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300
                            @error('name') border-red-400 @enderror"
                        placeholder="Nama lengkap">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300
                            @error('username') border-red-400 @enderror"
                        placeholder="Username" required>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300
                            @error('email') border-red-400 @enderror"
                        placeholder="email@example.com" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Data Akademik --}}
        <div class="p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Data Akademik</h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                {{-- NIM --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim', $user->nim) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300
                            @error('nim') border-red-400 @enderror"
                        placeholder="NIM mahasiswa">
                    @error('nim')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Semester --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select name="semester"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">-- Pilih Semester --</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" @selected(old('semester', $user->semester) == $i)>
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Prodi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                    <input type="text" name="prodi" value="{{ old('prodi', $user->prodi) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300
                            @error('prodi') border-red-400 @enderror"
                        placeholder="Nama prodi">
                    @error('prodi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Reset Password --}}
        <div class="p-6 space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Reset Password</h3>
                <p class="text-xs text-gray-400 mt-0.5">Kosongkan jika tidak ingin mengubah password.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300
                        @error('password') border-red-400 @enderror"
                    placeholder="Min. 8 karakter">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="p-6 flex items-center justify-between">
            <a href="{{ route('admin.mahasiswa.index') }}"
                class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl hover:bg-gray-100 transition">
                Batal
            </a>
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition">
                Simpan Perubahan
            </button>
        </div>

    </form>

    {{-- Info box --}}
    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-4 text-xs text-gray-500 space-y-1">
        <p><span class="font-medium text-gray-600">ID:</span> {{ $user->id }}</p>
        <p><span class="font-medium text-gray-600">Dibuat:</span> {{ $user->created_at->format('d M Y, H:i') }}</p>
        <p><span class="font-medium text-gray-600">Status:</span>
            @if($user->is_blacklisted)
                <span class="text-red-500 font-medium">Blacklisted</span>
            @else
                <span class="text-green-600 font-medium">Aktif</span>
            @endif
        </p>
        <p><span class="font-medium text-gray-600">Poin:</span> {{ number_format($user->poin ?? 0) }}</p>
    </div>

</div>
@endsection
