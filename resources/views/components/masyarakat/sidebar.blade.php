
<aside class="sidebar">
    <!-- LOGO -->
    <div class="brand">
        <div class="brand-logo">
            <span class="brand-symbol">🌿</span>
            <strong>PERLAHAN</strong>
            <small>PELAYANAN LAHAN</small>
        </div>
    </div>

    <!-- AKUN MASYARAKAT -->
    <div class="account-box">
        <div class="avatar">MK</div>
        <div>
            <strong>Masyarakat / Komunitas</strong>
            <small>Portal PERLAHAN</small>
        </div>
    </div>

    <!-- MENU -->
    <p class="sidebar-label">MENU UTAMA</p>

    <nav class="sidebar-nav">
        <a
            href="{{ route('dashboard') }}"
            class="nav-link {{ request()->routeIs('dashboard', 'home') ? 'active' : '' }}"
        >
            <span>▦</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <a
            href="{{ route('data-lahan') }}"
            class="nav-link {{ request()->routeIs('data-lahan') ? 'active' : '' }}"
        >
            <span>♧</span>
            <span class="nav-text">Data Lahan</span>
        </a>

        <a
            href="{{ url('/peta-lahan') }}"
            class="nav-link {{ request()->is('peta-lahan') ? 'active' : '' }}"
        >
            <span>▤</span>
            <span class="nav-text">Peta Lahan</span>
        </a>

        <a
            href="{{ url('/rekomendasi') }}"
            class="nav-link {{ request()->is('rekomendasi') ? 'active' : '' }}"
        >
            <span>♙</span>
            <span class="nav-text">Rekomendasi</span>
        </a>

        <a
            href="{{ url('/pengajuan') }}"
            class="nav-link {{ request()->is('pengajuan') ? 'active' : '' }}"
        >
            <span>▧</span>
            <span class="nav-text">Pengajuan Saya</span>
        </a>

        <a
            href="{{ url('/riwayat-pengajuan') }}"
            class="nav-link {{ request()->is('riwayat-pengajuan') ? 'active' : '' }}"
        >
            <span>◷</span>
            <span class="nav-text">Riwayat Pengajuan</span>
        </a>

        <a
            href="{{ url('/perkembangan-kegiatan') }}"
            class="nav-link {{ request()->is('perkembangan-kegiatan') ? 'active' : '' }}"
        >
            <span>♧</span>
            <span class="nav-text">Perkembangan Kegiatan</span>
        </a>
    </nav>

    <!-- BAGIAN BAWAH -->
    <div class="sidebar-bottom">
        <div class="sdg-box">
            <span>🌱</span>
            <div>
                <strong>Mendukung SDG 15</strong>
                <small>Life on Land</small>
            </div>
        </div>

        <a href="{{ url('/keluar') }}" class="logout-link">
            ⇥ <span>Keluar</span>
        </a>
    </div>
</aside>
