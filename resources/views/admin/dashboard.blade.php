{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Administrator</h1>
            <p class="text-gray-500 text-sm mt-1">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-sm text-gray-500 hover:text-red-500 transition px-4 py-2 rounded-xl hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
            <p class="text-blue-600 text-xs font-medium uppercase tracking-wide">Mahasiswa</p>
            <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stats['total_mahasiswa'] }}</p>
        </div>
        <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4">
            <p class="text-orange-600 text-xs font-medium uppercase tracking-wide">Lapak</p>
            <p class="text-3xl font-bold text-orange-700 mt-1">{{ $stats['total_lapak'] }}</p>
        </div>
        <div class="bg-green-50 border border-green-100 rounded-2xl p-4">
            <p class="text-green-600 text-xs font-medium uppercase tracking-wide">Total Order</p>
            <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['total_order'] }}</p>
        </div>
        <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4">
            <p class="text-purple-600 text-xs font-medium uppercase tracking-wide">Produk</p>
            <p class="text-3xl font-bold text-purple-700 mt-1">{{ $stats['total_produk'] }}</p>
        </div>
        <div class="bg-red-50 border border-red-100 rounded-2xl p-4">
            <p class="text-red-600 text-xs font-medium uppercase tracking-wide">Blacklisted</p>
            <p class="text-3xl font-bold text-red-700 mt-1">{{ $stats['blacklisted'] }}</p>
        </div>
    </div>

    {{-- Menu Navigasi --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <a href="{{ route('admin.mahasiswa.index') }}"
            class="group bg-white border border-gray-100 rounded-2xl p-6 hover:border-indigo-200 hover:shadow-md transition-all duration-200">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-200 transition">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-800">Manajemen Mahasiswa</h3>
            <p class="text-gray-500 text-sm mt-1">Edit profil, semester, blacklist/whitelist, reset password, ubah poin.</p>
        </a>

        <a href="{{ route('admin.lapak.index') }}"
            class="group bg-white border border-gray-100 rounded-2xl p-6 hover:border-orange-200 hover:shadow-md transition-all duration-200">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-orange-200 transition">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-800">Pojok Jajan — Lapak</h3>
            <p class="text-gray-500 text-sm mt-1">Hapus lapak duplikat, kelola lapak dan produk seluruh mahasiswa.</p>
        </a>

        <a href="{{ route('admin.produk.index') }}"
            class="group bg-white border border-gray-100 rounded-2xl p-6 hover:border-green-200 hover:shadow-md transition-all duration-200">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-200 transition">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-800">Kelola Produk</h3>
            <p class="text-gray-500 text-sm mt-1">Lihat dan hapus produk dari semua lapak mahasiswa.</p>
        </a>
    </div>

</div>
@endsection
