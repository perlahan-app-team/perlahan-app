
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Lahan | PERLAHAN</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/data-lahan.css') }}">
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
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span>▦</span> Dashboard
            </a>
            <a class="nav-link active" href="{{ route('data-lahan') }}">
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
                    <small>Life on Land</small>
                </div>
            </div>
            <a href="#keluar" class="logout-link">⇥ &nbsp; Keluar</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-area">

        <header class="topbar">
            <div class="page-heading">
                <small>Portal / Data Lahan</small>
                <strong>Data Lahan</strong>
            </div>

            <div class="topbar-right">
                <div class="search-box">
                    <span>⌕</span>
                    <input type="search" placeholder="Cari data, lahan...">
                </div>

                <button class="notification-button" type="button" aria-label="Notifikasi">
                    ♧<span></span>
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

        <main class="data-content">

            <section class="data-heading">
                <p class="date-label">INVENTARIS RUANG KOTA</p>
                <h1>Data Lahan</h1>
                <p>Jelajahi lahan pemerintah yang berpotensi dimanfaatkan bersama.</p>
            </section>

            <!-- SEARCH AND FILTER -->
            <section class="filter-panel">
                <div class="land-search">
                    <span>⌕</span>
                    <input
                        type="search"
                        id="searchLahan"
                        placeholder="Cari nama atau alamat lahan..."
                    >
                </div>

                <select id="filterLokasi" aria-label="Filter lokasi">
                    <option value="">⌖ Semua Lokasi</option>
                    <option value="rungkut">Rungkut</option>
                    <option value="gubeng">Gubeng</option>
                    <option value="wonokromo">Wonokromo</option>
                </select>

                <select id="filterStatus" aria-label="Filter status">
                    <option value="">⚑ Status</option>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Diverifikasi">Diverifikasi</option>
                    <option value="Dalam pemanfaatan">Dalam pemanfaatan</option>
                </select>

                <select id="filterLuas" aria-label="Filter luas lahan">
                    <option value="">Luas Lahan</option>
                    <option value="kecil">Di bawah 1.000 m²</option>
                    <option value="besar">1.000 m² ke atas</option>
                </select>
            </section>

            <div class="results-row">
                <p id="jumlahLahan">Menampilkan 3 lahan potensial</p>
                <label>
                    Urutkan:
                    <select id="sortLahan">
                        <option value="terbaru">Terbaru</option>
                        <option value="luas-terbesar">Luas terbesar</option>
                        <option value="luas-terkecil">Luas terkecil</option>
                    </select>
                </label>
            </div>

            <!-- LAND CARDS -->
            <section class="land-grid data-land-grid" id="daftarLahan">

                <article class="land-card"
                    data-nama="Lahan Kosong Rungkut"
                    data-alamat="Jl. Rungkut Madya, Rungkut"
                    data-lokasi="rungkut"
                    data-status="Tersedia"
                    data-luas="1250"
                    data-urutan="1">

                    <div class="land-image land-one">
                        <span class="status-badge available">Tersedia</span>
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

                        <button class="detail-button"
                            type="button"
                            onclick="lihatDetail('Lahan Kosong Rungkut', 'Jl. Rungkut Madya, Rungkut', '1.250 m²', 'Tersedia', 'Tanah datar')">
                            Lihat Detail <span>›</span>
                        </button>
                    </div>
                </article>

                <article class="land-card"
                    data-nama="Lahan Kelurahan Gubeng"
                    data-alamat="Jl. Gubeng Kertajaya, Gubeng"
                    data-lokasi="gubeng"
                    data-status="Diverifikasi"
                    data-luas="860"
                    data-urutan="2">

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

                        <button class="detail-button"
                            type="button"
                            onclick="lihatDetail('Lahan Kelurahan Gubeng', 'Jl. Gubeng Kertajaya, Gubeng', '860 m²', 'Diverifikasi', 'Perlu pembersihan ringan')">
                            Lihat Detail <span>›</span>
                        </button>
                    </div>
                </article>

                <article class="land-card"
                    data-nama="Lahan Pinggir Sungai Wonokromo"
                    data-alamat="Jl. Jagir Wonokromo, Wonokromo"
                    data-lokasi="wonokromo"
                    data-status="Dalam pemanfaatan"
                    data-luas="2100"
                    data-urutan="3">

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

                        <button class="detail-button"
                            type="button"
                            onclick="lihatDetail('Lahan Pinggir Sungai Wonokromo', 'Jl. Jagir Wonokromo, Wonokromo', '2.100 m²', 'Dalam pemanfaatan', 'Lahan memanjang')">
                            Lihat Detail <span>›</span>
                        </button>
                    </div>
                </article>

            </section>

            <div id="tidakAdaLahan" class="empty-state" hidden>
                <span>⌕</span>
                <h3>Lahan tidak ditemukan</h3>
                <p>Coba ubah kata pencarian atau filter yang kamu pilih.</p>
            </div>

            <footer class="footer">
                © {{ date('Y') }} PERLAHAN — Pelayanan Lahan untuk Masyarakat
            </footer>

        </main>
    </div>
</div>

<!-- DETAIL MODAL -->
<div class="modal-overlay" id="detailModal" hidden>
    <div class="detail-modal">
        <button class="modal-close" type="button" onclick="tutupDetail()">×</button>
        <p class="date-label">INFORMASI LAHAN</p>
        <h2 id="detailNama">Nama Lahan</h2>

        <div class="modal-info">
            <p><strong>Alamat</strong><span id="detailAlamat"></span></p>
            <p><strong>Luas lahan</strong><span id="detailLuas"></span></p>
            <p><strong>Status</strong><span id="detailStatus"></span></p>
            <p><strong>Kondisi</strong><span id="detailKondisi"></span></p>
        </div>

        <p class="modal-note">
            Data ini merupakan contoh tampilan awal. Informasi lahan perlu
            diverifikasi sebelum pengajuan dilakukan.
        </p>

        <button class="primary-button modal-action" type="button" onclick="buatPengajuan()">
            Ajukan Pemanfaatan Lahan
        </button>
    </div>
</div>

<!-- APPLICATION MODAL -->
<div class="modal-overlay" id="pengajuanModal" hidden>
    <div class="detail-modal">
        <button class="modal-close" type="button" onclick="tutupPengajuan()">×</button>
        <p class="date-label">FORMULIR PENGAJUAN</p>
        <h2>Pengajuan Pemanfaatan Lahan</h2>
        <p class="form-intro">Lengkapi informasi awal pengajuan komunitas Anda.</p>

        <form id="formPengajuan">
            <label for="namaLahanPilihan">Lahan yang dipilih</label>
            <input type="text" id="namaLahanPilihan" readonly>

            <label for="namaKomunitas">Nama masyarakat / komunitas</label>
            <input type="text" id="namaKomunitas" placeholder="Masukkan nama komunitas" required>

            <label for="jenisKegiatan">Rencana kegiatan</label>
            <select id="jenisKegiatan" required>
                <option value="">Pilih kegiatan</option>
                <option>Urban Farming</option>
                <option>Penghijauan</option>
                <option>Kebun Komunitas</option>
                <option>Kegiatan Sosial</option>
                <option>Lainnya</option>
            </select>

            <label for="deskripsiKegiatan">Deskripsi kegiatan</label>
            <textarea id="deskripsiKegiatan" rows="4"
                placeholder="Jelaskan rencana pemanfaatan lahan..." required></textarea>

            <button class="primary-button submit-button" type="submit">
                Kirim Pengajuan
            </button>
            <p class="form-note">
                Demo frontend: data belum dikirim atau disimpan ke server.
            </p>
        </form>

        <div id="pesanDemo" class="demo-message" hidden></div>
    </div>
</div>

<script>
    const kartuLahan = Array.from(document.querySelectorAll('#daftarLahan .land-card'));

    const searchLahan = document.getElementById('searchLahan');
    const filterLokasi = document.getElementById('filterLokasi');
    const filterStatus = document.getElementById('filterStatus');
    const filterLuas = document.getElementById('filterLuas');
    const sortLahan = document.getElementById('sortLahan');

    function perbaruiLahan() {
        const kata = searchLahan.value.toLowerCase().trim();
        const lokasi = filterLokasi.value;
        const status = filterStatus.value;
        const luas = filterLuas.value;

        let hasil = kartuLahan.filter(kartu => {
            const teks = (kartu.dataset.nama + ' ' + kartu.dataset.alamat).toLowerCase();

            const cocokKata = teks.includes(kata);
            const cocokLokasi = !lokasi || kartu.dataset.lokasi === lokasi;
            const cocokStatus = !status || kartu.dataset.status === status;
            const nilaiLuas = Number(kartu.dataset.luas);

            const cocokLuas =
                !luas ||
                (luas === 'kecil' && nilaiLuas < 1000) ||
                (luas === 'besar' && nilaiLuas >= 1000);

            return cocokKata && cocokLokasi && cocokStatus && cocokLuas;
        });

        if (sortLahan.value === 'luas-terbesar') {
            hasil.sort((a, b) => Number(b.dataset.luas) - Number(a.dataset.luas));
        } else if (sortLahan.value === 'luas-terkecil') {
            hasil.sort((a, b) => Number(a.dataset.luas) - Number(b.dataset.luas));
        } else {
            hasil.sort((a, b) => Number(a.dataset.urutan) - Number(b.dataset.urutan));
        }

        const daftar = document.getElementById('daftarLahan');

        hasil.forEach(kartu => daftar.appendChild(kartu));

        kartuLahan.forEach(kartu => {
            kartu.hidden = !hasil.includes(kartu);
        });

        document.getElementById('jumlahLahan').textContent =
            `Menampilkan ${hasil.length} lahan potensial`;

        document.getElementById('tidakAdaLahan').hidden = hasil.length > 0;
    }

    [searchLahan, filterLokasi, filterStatus, filterLuas, sortLahan]
        .forEach(elemen => {
            elemen.addEventListener(
                elemen.tagName === 'INPUT' ? 'input' : 'change',
                perbaruiLahan
            );
        });

    let lahanTerpilih = null;

    function lihatDetail(nama, alamat, luas, status, kondisi) {
        lahanTerpilih = { nama, alamat, luas, status, kondisi };

        document.getElementById('detailNama').textContent = nama;
        document.getElementById('detailAlamat').textContent = alamat;
        document.getElementById('detailLuas').textContent = luas;
        document.getElementById('detailStatus').textContent = status;
        document.getElementById('detailKondisi').textContent = kondisi;

        document.getElementById('detailModal').hidden = false;
    }

    function tutupDetail() {
        document.getElementById('detailModal').hidden = true;
    }

    function buatPengajuan() {
        if (!lahanTerpilih) return;

        document.getElementById('namaLahanPilihan').value = lahanTerpilih.nama;
        document.getElementById('detailModal').hidden = true;
        document.getElementById('pesanDemo').hidden = true;
        document.getElementById('pengajuanModal').hidden = false;
    }

    function tutupPengajuan() {
        document.getElementById('pengajuanModal').hidden = true;
    }

    document.getElementById('formPengajuan').addEventListener('submit', function(event) {
        event.preventDefault();

        const pesan = document.getElementById('pesanDemo');
        pesan.textContent =
            'Formulir berhasil divalidasi untuk demo. Pengajuan belum tersimpan karena backend belum terhubung.';
        pesan.hidden = false;
    });

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.hidden = true;
            }
        });
    });
</script>

</body>
</html>
