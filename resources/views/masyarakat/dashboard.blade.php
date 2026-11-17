
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Masyarakat | PERLAHAN</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

<div class="app-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">
                <span class="brand-symbol">🌿</span>
                <strong>PERLAHAN</strong>
                <small>PELAYANAN LAHAN</small>
            </div>
        </div>

        <div class="account-box">
            <div class="avatar">MK</div>
            <div>
                <strong>Masyarakat / Komunitas</strong>
                <small>Portal PERLAHAN</small>
            </div>
        </div>

        <p class="sidebar-label">MENU UTAMA</p>

        <nav class="sidebar-nav">
            <a class="nav-link active" href="#dashboard">
                <span>▦</span> Dashboard
            </a>
            <a class="nav-link" href="data-lahan">
                <span>♧</span> Data Lahan
            </a>
            <a class="nav-link" href="#peta-lahan">
                <span>▤</span> Peta Lahan
            </a>
            <a class="nav-link" href="#rekomendasi">
                <span>♙</span> Rekomendasi
            </a>
            <a class="nav-link" href="#pengajuan">
                <span>▧</span> Pengajuan Saya
            </a>
            <a class="nav-link" href="#riwayat">
                <span>◷</span> Riwayat Pengajuan
            </a>
            <a class="nav-link" href="#perkembangan">
                <span>♧</span> Perkembangan Kegiatan
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="sdg-box">
                <span>🌱</span>
                <div>
                    <strong>Mendukung SDG 15</strong>
                    <small>Ekosistem Daratan</small>
                </div>
            </div>
            <a href="welcome" class="logout-link">⇥ &nbsp; Keluar</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-area" id="dashboard">

        <header class="topbar">
            <div class="page-heading">
                <small>Portal / Dashboard</small>
                <strong>Dashboard</strong>
            </div>

            <div class="topbar-right">
                <div class="search-box">
                    <span>⌕</span>
                    <input type="search" placeholder="Cari data, lahan...">
                </div>

                <button class="notification-button" type="button" aria-label="Notifikasi">
                    ♧
                    <span></span>
                </button>

                <div class="profile">
                    <div class="profile-avatar">SR</div>
                    <div class="profile-info">
                        <strong>Siti Rahma</strong>
                        <small>Masyarakat / Komunitas</small>
                    </div>
                    <span class="profile-arrow">⌄</span>
                </div>
            </div>
        </header>

        <main class="dashboard-content">

            <!-- WELCOME -->
            <section class="welcome-row">
                <div>
                    <p class="date-label">RABU, 21 MEI 2025</p>
                    <h1>Selamat datang kembali, Jingga!</h1>
                    <p class="subtitle">
                        Pantau pengajuan dan temukan peluang baru untuk komunitas Anda.
                    </p>
                </div>
                
                <a class="primary-button" href="{{ route('data-lahan') }}">
                    <span>＋</span> Buat Pengajuan
                </a>

            </section>

            <!-- STATISTICS -->
            <section class="stats-grid">
                <article class="stat-card">
                    <div class="stat-icon green">▤</div>
                    <div class="stat-info">
                        <p>Pengajuan Aktif</p>
                        <h2>04</h2>
                        <small>+1 bulan ini</small>
                    </div>
                    <span class="more">···</span>
                </article>

                <article class="stat-card">
                    <div class="stat-icon yellow">◷</div>
                    <div class="stat-info">
                        <p>Menunggu</p>
                        <h2>02</h2>
                        <small>Sedang ditinjau</small>
                    </div>
                    <span class="more">···</span>
                </article>

                <article class="stat-card">
                    <div class="stat-icon blue">✓</div>
                    <div class="stat-info">
                        <p>Disetujui</p>
                        <h2>01</h2>
                        <small>Siap dilaksanakan</small>
                    </div>
                    <span class="more">···</span>
                </article>

                <article class="stat-card">
                    <div class="stat-icon orange">!</div>
                    <div class="stat-info">
                        <p>Perlu Perbaikan</p>
                        <h2>01</h2>
                        <small>Perlu ditindaklanjuti</small>
                    </div>
                    <span class="more">···</span>
                </article>
            </section>

            <!-- ALERT -->
            <section class="alert-box">
                <div class="alert-icon">!</div>
                <div class="alert-text">
                    <strong>Satu pengajuan perlu diperbaiki</strong>
                    <p>Admin memberikan catatan pada pengajuan "Kebun Komunitas Rungkut".</p>
                </div>
                <a href="#riwayat" class="outline-button">Lihat Catatan</a>
            </section>

            <!-- LAND RECOMMENDATIONS -->
            <section class="section-block" id="rekomendasi">
                <div class="section-heading">
                    <div>
                        <h2>Rekomendasi lahan untuk Anda</h2>
                        <p>Berdasarkan lokasi dan minat komunitas</p>
                    </div>
                    <a href="#data-lahan" class="text-link">Lihat semua ›</a>
                </div>

                <div class="land-grid">

                    <article class="land-card" id="data-lahan">
                        <div class="land-image land-one">
                            <span class="status-badge available">Tersedia</span>
                            <span class="area-badge">925 cocok</span>
                        </div>
                        <div class="land-body">
                            <h3>Lahan Kosong Rungkut</h3>
                            <p class="location">⌖ Jl. Rungkut Madya, Rungkut</p>
                            <div class="land-details">
                                <div>
                                    <strong>1.250 m²</strong>
                                    <small>Luas lahan</small>
                                </div>
                                <div>
                                    <strong>Tanah datar</strong>
                                    <small>Kondisi</small>
                                </div>
                            </div>
                            <a href="#detail-rungkut" class="detail-button">Lihat Detail <span>›</span></a>
                        </div>
                    </article>

                    <article class="land-card">
                        <div class="land-image land-two">
                            <span class="status-badge verified">Diverifikasi</span>
                        </div>
                        <div class="land-body">
                            <h3>Lahan Kelurahan Gubeng</h3>
                            <p class="location">⌖ Jl. Gubeng Kertajaya, Gubeng</p>
                            <div class="land-details">
                                <div>
                                    <strong>860 m²</strong>
                                    <small>Luas lahan</small>
                                </div>
                                <div>
                                    <strong>Perlu pembersihan ringan</strong>
                                    <small>Kondisi</small>
                                </div>
                            </div>
                            <a href="#detail-gubeng" class="detail-button">Lihat Detail <span>›</span></a>
                        </div>
                    </article>

                    <article class="land-card">
                        <div class="land-image land-three">
                            <span class="status-badge utilized">Dalam pemanfaatan</span>
                        </div>
                        <div class="land-body">
                            <h3>Lahan Pinggir Sungai Wonokromo</h3>
                            <p class="location">⌖ Jl. Jagir Wonokromo, Wonokromo</p>
                            <div class="land-details">
                                <div>
                                    <strong>2.100 m²</strong>
                                    <small>Luas lahan</small>
                                </div>
                                <div>
                                    <strong>Lahan memanjang</strong>
                                    <small>Kondisi</small>
                                </div>
                            </div>
                            <a href="#detail-wonokromo" class="detail-button">Lihat Detail <span>›</span></a>
                        </div>
                    </article>

                </div>
            </section>

            <!-- LATEST SUBMISSIONS -->
            <section class="section-block" id="pengajuan">
                <div class="section-heading">
                    <div>
                        <h2>Pengajuan terbaru</h2>
                        <p>Aktivitas pengajuan komunitas Anda</p>
                    </div>
                    <a href="#riwayat" class="text-link">Lihat riwayat ›</a>
                </div>

                <div class="table-container" id="riwayat">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>LAHAN</th>
                                <th>KEGIATAN</th>
                                <th>TANGGAL</th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#PL001</td>
                                <td>Kebun Komunitas Rungkut</td>
                                <td>Urban Farming</td>
                                <td>20 Mei 2025</td>
                                <td><span class="table-status pending">Menunggu</span></td>
                                <td><a href="#detail-pengajuan">Detail</a></td>
                            </tr>
                            <tr>
                                <td>#PL002</td>
                                <td>Lahan Kelurahan Gubeng</td>
                                <td>Penghijauan</td>
                                <td>18 Mei 2025</td>
                                <td><span class="table-status approved">Disetujui</span></td>
                                <td><a href="#detail-pengajuan">Detail</a></td>
                            </tr>
                            <tr>
                                <td>#PL003</td>
                                <td>Lahan Wonokromo</td>
                                <td>Kebun Bersama</td>
                                <td>15 Mei 2025</td>
                                <td><span class="table-status revision">Perbaikan</span></td>
                                <td><a href="#detail-pengajuan">Detail</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <footer class="footer">
                © {{ date('Y') }} PERLAHAN — Pelayanan Lahan untuk Masyarakat
            </footer>

        </main>
    </div>
</div>

</body>
</html>
