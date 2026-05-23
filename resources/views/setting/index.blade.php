@extends('layouts.app')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
<div class="profile-box">
    <div class="card">
        <div style="font-size:13px; font-weight:700; margin-bottom:14px; color:var(--text);">Pengaturan Akun</div>

        <div class="setting-row">
            <span class="setting-lbl">Notifikasi Email</span>
            <input type="checkbox" checked>
        </div>
        <div class="setting-row">
            <span class="setting-lbl">Notifikasi Push</span>
            <input type="checkbox">
        </div>
        <div class="setting-row">
            <span class="setting-lbl">Mode Gelap</span>
            <input type="checkbox">
        </div>
        <div class="setting-row">
            <span class="setting-lbl">Bahasa</span>
            <select class="form-input" style="width:auto; padding:4px 8px;">
                <option>Indonesia</option>
                <option>English</option>
            </select>
        </div>

        <hr class="divider">

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-danger">Keluar / Logout</button>
        </form>
    </div>
</div>
@endsection
