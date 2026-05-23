@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── CAROUSEL BANNER ── --}}
<div class="carousel-wrap" id="carouselWrap">
    <div class="carousel-track" id="carouselTrack">

        <div class="slide slide-purple">
            <div class="slide-deco d1"></div><div class="slide-deco d2"></div><div class="slide-deco d3"></div>
            <span class="slide-tag">🎉 Fun Event</span>
            <h2>BSI Campus Fair 2025</h2>
            <p>Pameran UKM, lomba, dan bazar seru! &bull; 3&ndash;5 Mei 2025 &bull; Lapangan Kampus Utama</p>
        </div>

        <div class="slide slide-teal">
            <div class="slide-deco d1"></div><div class="slide-deco d2"></div><div class="slide-deco d3"></div>
            <span class="slide-tag">🏆 Kompetisi</span>
            <h2>Hackathon BSI &times; Industri 2025</h2>
            <p>48 jam coding marathon berhadiah total Rp 30 juta &bull; Daftar sebelum 28 April</p>
        </div>

        <div class="slide slide-amber">
            <div class="slide-deco d1"></div><div class="slide-deco d2"></div><div class="slide-deco d3"></div>
            <span class="slide-tag">🎤 Seminar</span>
            <h2>Talk Show: Karir di Era AI</h2>
            <p>Bersama alumni &amp; praktisi industri &bull; 26 April 2025 &bull; Aula Gedung B &bull; Gratis!</p>
        </div>

        <div class="slide slide-green">
            <div class="slide-deco d1"></div><div class="slide-deco d2"></div><div class="slide-deco d3"></div>
            <span class="slide-tag">🌿 Sosial</span>
            <h2>BSI Green Campus Day</h2>
            <p>Tanam pohon bareng &amp; kerja bakti lingkungan kampus &bull; 10 Mei 2025</p>
        </div>

        <div class="slide slide-pink">
            <div class="slide-deco d1"></div><div class="slide-deco d2"></div><div class="slide-deco d3"></div>
            <span class="slide-tag">🎬 Hiburan</span>
            <h2>Malam Apresiasi Mahasiswa BSI</h2>
            <p>Pentas seni, awarding maba berprestasi, &amp; live music &bull; 17 Mei 2025</p>
        </div>

    </div>
    <div class="carousel-nav">
        <button class="cnav" id="prevBtn">&#8592;</button>
        <button class="cnav" id="nextBtn">&#8594;</button>
    </div>
</div>
<div class="carousel-dots" id="carouselDots">
    @for ($i = 0; $i < 5; $i++)
        <span class="dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"></span>
    @endfor
</div>

{{-- ── EVENT & PENGUMUMAN ── --}}
<div class="grid2">

    <div class="card">
        <div class="card-hd">
            <h3>📅 Event &amp; Akademik</h3>
            <a href="#" class="see-all">Semua</a>
        </div>
        @foreach ($events as $ev)
        <div class="ev-item">
            <div class="ev-date">
                <span class="ev-day">{{ $ev['day'] }}</span>
                <span class="ev-mon">{{ $ev['month'] }}</span>
            </div>
            <div>
                <div class="ev-title">{{ $ev['title'] }}</div>
                <div class="ev-sub">
                    <span class="badge badge-{{ $ev['color'] }}">{{ $ev['label'] }}</span>
                    &bull; {{ $ev['desc'] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-hd">
            <h3>📢 Pengumuman</h3>
            <a href="#" class="see-all">Semua</a>
        </div>
        @foreach ($announcements as $ann)
        <div class="ev-item">
            <div class="ev-date">
                <span class="ev-day">{{ $ann['day'] }}</span>
                <span class="ev-mon">{{ $ann['month'] }}</span>
            </div>
            <div>
                <div class="ev-title">{{ $ann['title'] }}</div>
                <div class="ev-sub">
                    <span class="badge badge-{{ $ann['color'] }}">{{ $ann['label'] }}</span>
                    &bull; {{ $ann['desc'] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- ── MATERI TERAKHIR ── --}}
<div class="card">
    <div class="card-hd">
        <h3>Materi Terakhir Diakses</h3>
        <a href="{{ route('course.index') }}" class="see-all">Lihat semua &rarr;</a>
    </div>
    <div class="grid3" style="margin-top:4px">
        @foreach ($recentCourses as $c)
        <a href="{{ route('course.show', $c['slug']) }}" class="recent-card">
            <div class="rc-icon">{{ $c['icon'] }}</div>
            <div class="rc-name">{{ $c['name'] }}</div>
            <div class="rc-meta">{{ $c['meta'] }}</div>
        </a>
        @endforeach
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
