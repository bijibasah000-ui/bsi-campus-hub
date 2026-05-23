@extends('layouts.app')
@section('title', 'Bimbingan Online')
@section('page-title', 'Bimbingan Online')

@section('content')

<div class="notice">Pilih jenis bimbingan dan dosen pembimbing yang tersedia, lalu buat jadwal sesi.</div>

<div class="filter-bar">
    <button class="fb active">Semua</button>
    @foreach (['Skripsi','KP / PKL','Akademik','Tugas Akhir'] as $k)
        <button class="fb">{{ $k }}</button>
    @endforeach
</div>

<div class="course-grid">
    @foreach ($dosens as $d)
    <div class="course-card">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <div class="dosen-av" style="background:{{ $d['av_bg'] }}; color:{{ $d['av_color'] }};">
                {{ $d['initials'] }}
            </div>
            <div>
                <div class="dosen-name">{{ $d['name'] }}</div>
                <div class="dosen-role">{{ $d['role'] }}</div>
            </div>
        </div>
        <span class="badge badge-{{ $d['badge_color'] }}">{{ $d['badge_label'] }}</span>
        @if ($d['badge_color'] !== 'red')
        <button style="float:right; padding:4px 12px; background:var(--primary); color:white; border:none; border-radius:var(--radius-sm); font-size:10px; font-weight:700;">
            Buat Jadwal
        </button>
        @endif
    </div>
    @endforeach
</div>

@endsection
