<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSI Campus Hub &mdash; @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<div class="app-wrapper">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="sidebar" id="sidebar">

        <div class="sb-top">
            <button class="ham" onclick="toggleSidebar()" title="Toggle Menu">
                <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="logo-wrap">
                <span class="logo-name">BSI Campus Hub</span>
                <small class="logo-sub">Universitas BSI</small>
            </div>
        </div>

        {{-- Profile sidebar: foto/avatar + nama mahasiswa (bukan email) --}}
        <div class="prof-sec">
            @auth
                @if(Auth::user()->foto)
                    <img src="{{ asset('storage/foto-profil/' . Auth::user()->foto) }}"
                         id="sidebarAvatar"
                         alt="Foto Profil"
                         style="width:68px; height:68px; min-width:68px; border-radius:50%; object-fit:cover; border:3px solid var(--primary-light); flex-shrink:0;">
                @else
                    <div class="prof-avatar" id="sidebarAvatar">
                        {{ strtoupper(substr(Auth::user()->username ?? 'MH', 0, 2)) }}
                    </div>
                @endif
            @else
                <div class="prof-avatar" id="sidebarAvatar">MH</div>
            @endauth
            <div class="prof-info">
                {{-- Tampilkan username (nama mahasiswa), bukan email --}}
                <span class="prof-name" id="sidebarName">
                    @auth{{ Auth::user()->username ?? 'Mahasiswa' }}@else Tamu @endauth
                </span>
                {{-- Tampilkan NIM di bawah nama --}}
                <span class="prof-email" id="sidebarEmail">
                    @auth{{ Auth::user()->nim ?? Auth::user()->email }}@else Belum masuk akun @endauth
                </span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sec-lbl">Menu Utama</div>

            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="mi-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
                <span class="mi-label">Dashboard</span>
            </a>

            <a href="{{ Auth::check() ? route('profile') : route('login') }}" class="menu-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <span class="mi-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></span>
                <span class="mi-label">Profile</span>
            </a>

            <a href="{{ Auth::check() ? route('course.index') : route('login') }}" class="menu-item {{ request()->routeIs('course.*') ? 'active' : '' }}">
                <span class="mi-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                <span class="mi-label">Materi & Course</span>
            </a>

            <a href="{{ Auth::check() ? route('konseling.index') : route('login') }}" class="menu-item {{ request()->routeIs('konseling.*') ? 'active' : '' }}">
                <span class="mi-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
                <span class="mi-label">Konseling Chat</span>
            </a>

            <div class="sec-lbl">Lainnya</div>

            <a href="{{ Auth::check() ? route('pojok.index') : route('login') }}" class="menu-item pojok {{ request()->routeIs('pojok.*') ? 'active' : '' }}">
                <span class="mi-icon"><svg viewBox="0 0 24 24"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg></span>
                <span class="mi-label">Pojok Jajan</span>
            </a>

            <a href="{{ Auth::check() ? route('setting') : route('login') }}" class="menu-item {{ request()->routeIs('setting') ? 'active' : '' }}">
                <span class="mi-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
                <span class="mi-label">Pengaturan</span>
            </a>
        </nav>
    </aside>

    {{-- ===================== MAIN AREA ===================== --}}
    <div class="main-area">

        <header class="topbar">
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>

            <div class="search-bar">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" placeholder="Cari materi, dosen...">
            </div>

            {{-- Avatar topbar kanan atas — klik untuk dropdown --}}
            <div class="topbar-av-wrap" id="topbarAvWrap">
                @auth
                    @if(Auth::user()->foto)
                        <img src="{{ asset('storage/foto-profil/' . Auth::user()->foto) }}"
                             id="topbarAv"
                             alt="Foto"
                             onclick="toggleProfileDropdown()"
                             style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid var(--primary-light); cursor:pointer; display:block;">
                    @else
                        <div class="topbar-av" id="topbarAv" onclick="toggleProfileDropdown()">
                            {{ strtoupper(substr(Auth::user()->username ?? 'MH', 0, 2)) }}
                        </div>
                    @endif
                @else
                    <div class="topbar-av" id="topbarAv" onclick="toggleProfileDropdown()">MH</div>
                @endauth

                {{-- Dropdown profil --}}
                <div class="profile-dropdown" id="profileDropdown">
                    @auth
                        {{-- Sudah login --}}
                        <div class="pd-header">
                            @if(Auth::user()->foto)
                                <img src="{{ asset('storage/foto-profil/' . Auth::user()->foto) }}"
                                     style="width:38px; height:38px; border-radius:50%; object-fit:cover; flex-shrink:0;">
                            @else
                                <div class="pd-avatar">
                                    {{ strtoupper(substr(Auth::user()->username ?? 'MH', 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <div class="pd-name">{{ Auth::user()->username }}</div>
                                <div class="pd-email">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="pd-divider"></div>
                        <a href="{{ Auth::check() ? route('profile') : route('login') }}" class="pd-item">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                            Edit Profile
                        </a>
                        <a href="{{ Auth::check() ? route('setting') : route('login') }}" class="pd-item">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v2m0 16v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M2 12h2m16 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                            Pengaturan
                        </a>
                        <div class="pd-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="pd-item pd-logout">
                                <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Keluar / Logout
                            </button>
                        </form>
                    @else
                        {{-- Belum login --}}
                        <div class="pd-header pd-guest">
                            <div class="pd-avatar" style="background:#9CA3AF;">?</div>
                            <div>
                                <div class="pd-name">Tamu</div>
                                <div class="pd-email">Belum masuk akun</div>
                            </div>
                        </div>
                        <div class="pd-divider"></div>
                        <a href="{{ route('login') }}" class="pd-item pd-login-btn">
                            <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            Masuk / Login
                        </a>
                        <a href="{{ route('register') }}" class="pd-item">
                            <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            Daftar / Register
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="page-content">
            @yield('content')
        </main>

    </div>
</div>

{{-- Tutup dropdown jika klik di luar --}}
<script>
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('topbarAvWrap');
    var dd   = document.getElementById('profileDropdown');
    if (wrap && !wrap.contains(e.target)) {
        if (dd) dd.classList.remove('open');
    }
});
</script>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
