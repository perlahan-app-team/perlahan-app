<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PERLAHAN — Lahan tidur, harapan bertumbuh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>

<body>

    {{-- NAVBAR --}}
    <header class="navbar">
        <a href="{{ route('landing') }}" class="navbar__brand">
            <img src="{{ asset('images/logo.jpeg') }}" alt="PERLAHAN">
        </a>
        <nav class="navbar__menu">
            <a href="#fitur">Fitur</a>
            <a href="#tentang">Tentang</a>
            <a href="#dampak">Dampak</a>
        </nav>
        <div class="navbar__auth">
            <a href="{{ route('login') }}" class="link-masuk">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn--primary">Daftar</a>
        </div>
    </header>

    <main>
        {{-- HERO --}}
        <section class="hero container">
            <div class="hero__text">
                <span class="badge"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7 20h10" />
                        <path d="M10 20c5.5-2.5.8-6.4 3-10" />
                        <path
                            d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z" />
                        <path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z" />
                    </svg> Bersama menjaga ruang hidup Surabaya</span>
                <h1>Lahan tidur,<br><span class="accent">harapan bertumbuh.</span></h1>
                <p>Platform kolaborasi untuk mendata, memetakan, dan menghidupkan lahan pemerintah yang belum
                    termanfaatkan menjadi ruang hijau produktif.</p>

                <div class="hero__actions">
                    <a href="{{ route('login') }}" class="btn btn--primary btn--lg">Mulai Jelajahi
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="btn btn--outline btn--lg">Lihat Peta Lahan</a>
                </div>

                <div class="hero__communities">
                    <div class="avatars">
                        <span class="avatar avatar--mint">RW</span>
                        <span class="avatar avatar--blue">KT</span>
                        <span class="avatar avatar--dark">+8</span>
                    </div>
                    <div>
                        <strong>10+ komunitas</strong>
                        <small>siap berkolaborasi</small>
                    </div>
                </div>
            </div>

            <div class="hero__visual">
                <div class="hero__image">
                    <img src="{{ asset('images/landing.jpg') }}" alt="Warga menyiram tanaman di lahan urban farming">
                </div>
                <div class="float-card float-card--stat">
                    <span class="float-card__icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10z" />
                            <path d="M2 21c0-3 1.9-5.5 6-7" />
                        </svg>
                    </span>
                    <div><strong>+32%</strong><small>ruang hijau produktif</small></div>
                </div>
                <div class="float-card float-card--loc">
                    <span class="float-card__pin">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </span>
                    <div><strong>Rungkut, Surabaya</strong><small>Potensi urban farming</small></div>
                </div>
            </div>
        </section>

        {{-- STATISTIK --}}
        <section class="container" id="dampak">
            <div class="stats">
                <div class="stats__item"><strong>48</strong><span>Lahan terdata</span></div>
                <div class="stats__item"><strong>12,4 Ha</strong><span>Total area potensial</span></div>
                <div class="stats__item"><strong>17</strong><span>Kegiatan berjalan</span></div>
                <div class="stats__item"><strong>31</strong><span>Komunitas terlibat</span></div>
            </div>
        </section>

        {{-- FITUR --}}
        <section class="features container" id="fitur">
            <span class="badge badge--center">Satu platform, dampak nyata</span>
            <h2>Dari lahan kosong menjadi ruang yang berarti</h2>
            <p class="features__sub" id="tentang">PERLAHAN mendukung kolaborasi warga dan pemerintah dari proses
                penemuan lahan hingga pemantauan kegiatan.</p>

            <div class="features__grid">
                @php
                    $fitur = [
                        ['01', 'Peta Lahan', 'Temukan lahan potensial melalui peta sebaran yang mudah dijelajahi.', '<path d="M3 6l6-3 6 3 6-3v15l-6 3-6-3-6 3z"/><path d="M9 3v15"/><path d="M15 6v15"/>'],
                        ['02', 'Rekomendasi', 'Lihat alternatif pemanfaatan awal sesuai kondisi setiap lahan.', '<path d="M7 20h10"/><path d="M10 20c5.5-2.5.8-6.4 3-10"/><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"/><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z"/>'],
                        ['03', 'Pengajuan', 'Ajukan rencana kegiatan komunitas secara transparan dan terarah.', '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h8"/><path d="M8 17h8"/><path d="M8 9h2"/>'],
                        ['04', 'Monitoring', 'Pantau perkembangan kegiatan serta dampaknya bagi lingkungan.', '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'],
                    ];
                @endphp

                @foreach ($fitur as [$no, $judul, $desk, $icon])
                    <article class="feature-card">
                        <span class="feature-card__no">{{ $no }}</span>
                        <span class="feature-card__icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                        </span>
                        <h3>{{ $judul }}</h3>
                        <p>{{ $desk }}</p>
                        <a href="{{ route('login') }}" class="feature-card__link">Pelajari lebih lanjut
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- SDG 15 --}}
        <section class="container">
            <div class="sdg">
                <div>
                    <span class="sdg__tag">SDG 15</span>
                    <h2>Menghidupkan ekosistem kota, perlahan namun pasti.</h2>
                </div>
                <p>Prototype akademik ini mendukung semangat <em>Life on Land</em> melalui urban farming, penghijauan,
                    dan taman lingkungan di Kota Surabaya.</p>
            </div>

            <div class="note">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 8v4" />
                    <path d="M12 16h.01" />
                </svg>
                <span><strong>Catatan:</strong> Rekomendasi hanya informasi awal, bukan keputusan akhir.</span>
            </div>
        </section>
    </main>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="footer__inner">
            <img src="{{ asset('images/logo.jpeg') }}" alt="PERLAHAN" class="footer__logo">
            <p>Prototype mata kuliah Proyek Perangkat Lunak Kelompok 7 — bukan sistem resmi Pemerintah Kota Surabaya.</p>
            <span>© {{ date('Y') }} PERLAHAN</span>
        </div>
    </footer>

</body>

</html>