@extends('layouts.app')
@section('title', $course['name'] ?? 'Detail Materi')
@section('page-title', $course['name'] ?? 'Detail Materi')

@section('content')
<div style="margin-bottom:14px;">
    <a href="{{ route('course.index') }}" style="font-size:12px; color:var(--primary);">&larr; Kembali ke Daftar Materi</a>
</div>

<div class="card" style="margin-bottom:14px;">
    <div style="display:flex; align-items:center; gap:14px; margin-bottom:14px;">
        <div class="cc-icon" style="background:{{ $course['bg'] ?? '#EEF2FF' }}; width:48px; height:48px; font-size:24px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
            {{ $course['icon'] ?? '📚' }}
        </div>
        <div>
            <div style="font-size:15px; font-weight:700; color:var(--text);">{{ $course['name'] ?? '-' }}</div>
            <div style="font-size:11px; color:var(--text-3); margin-top:2px;">{{ $course['meta'] ?? '' }}</div>
            <span class="badge badge-{{ $course['badge_color'] ?? 'blue' }}" style="margin-top:5px; display:inline-block;">
                {{ $course['badge_label'] ?? 'Tersedia' }}
            </span>
        </div>
    </div>
    <hr class="divider">
    <p style="font-size:12px; color:var(--text-2); line-height:1.7;">
        Deskripsi lengkap matakuliah ini akan ditampilkan di sini. Modul-modul yang tersedia dapat diakses oleh mahasiswa yang terdaftar pada matakuliah ini.
    </p>
</div>

{{-- Daftar modul --}}
<div class="card">
    <div class="card-hd"><h3>Daftar Modul</h3></div>
    @for ($i = 1; $i <= 5; $i++)
    <div class="ev-item">
        <div class="ev-date" style="background:#F3F4F6; border-radius:7px;">
            <span class="ev-day" style="color:var(--text-2); font-size:11px;">M{{ $i }}</span>
        </div>
        <div>
            <div class="ev-title">Modul {{ $i }}: Materi akan diisi segera</div>
            <div class="ev-sub">PDF &bull; Video &bull; Kuis</div>
        </div>
    </div>
    @endfor
</div>
@endsection
