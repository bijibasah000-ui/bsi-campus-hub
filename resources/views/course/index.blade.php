@extends('layouts.app')
@section('title', 'Materi & Course')
@section('page-title', 'Materi & Course')

@section('content')

<div class="notice">Filter materi berdasarkan semester, jurusan, dan matakuliah yang kamu inginkan.</div>

{{-- Filter Semester --}}
<div class="filter-bar">
    <span class="fl-lbl">Semester:</span>
    <button class="fb active">Semua</button>
    @for ($s = 1; $s <= 8; $s++)
        <button class="fb">Sem {{ $s }}</button>
    @endfor
</div>

{{-- Filter Jurusan --}}
<div class="filter-bar">
    <span class="fl-lbl">Jurusan:</span>
    @foreach (['Teknik Informatika','Sistem Informasi','Manajemen','Akuntansi'] as $j)
        <button class="fb {{ $loop->first ? 'active' : '' }}">{{ $j }}</button>
    @endforeach
</div>

{{-- Grid Course --}}
<div class="course-grid">
    @foreach ($courses as $c)
    <a href="{{ route('course.show', $c['slug']) }}" class="course-card">
        <div class="cc-icon" style="background:{{ $c['bg'] }}">{{ $c['icon'] }}</div>
        <div class="cc-name">{{ $c['name'] }}</div>
        <div class="cc-meta">{{ $c['meta'] }}</div>
        <div style="margin-top:8px;">
            <span class="badge badge-{{ $c['badge_color'] }}">{{ $c['badge_label'] }}</span>
        </div>
    </a>
    @endforeach
</div>

@endsection
