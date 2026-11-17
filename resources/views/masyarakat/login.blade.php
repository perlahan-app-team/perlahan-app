<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — PERLAHAN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="login">

        {{-- PANEL KIRI --}}
        <aside class="login__visual" style="background-image: url('{{ asset('images/login-bg.jpg') }}')">
            <a href="{{ route('landing') }}" class="login__logo">
                <img src="{{ asset('images/logo-perlahan.png') }}" alt="PERLAHAN">
            </a>
            <div class="login__caption">
                <span class="pill">Surabaya berkelanjutan</span>
                <h2>Setiap jengkal lahan punya potensi untuk tumbuh.</h2>
                <p>Mari ubah ruang yang terlupakan menjadi manfaat yang dirasakan bersama.</p>
            </div>
        </aside>

        {{-- PANEL KANAN --}}
        <main class="login__panel">
            <div class="login__box">
                <span class="eyebrow">Selamat datang kembali</span>
                <h1>Masuk ke PERLAHAN</h1>
                <p class="subtitle">Pilih peran untuk mencoba dashboard prototype.</p>

                <form id="loginForm" novalidate data-redirect-warga="{{ url('/masyarakat/dashboard') }}"
                    data-redirect-admin="{{ url('/admin/dashboard') }}"
                    data-redirect-petugas="{{ url('/petugas/dashboard') }}"> }}">

                    <div class="tabs" role="tablist">
                        <button type="button" class="tab is-active" data-role="warga">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="7" r="4" />
                                <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.1a4 4 0 0 1 0 7.8" />
                                <path d="M21 21v-2a4 4 0 0 0-3-3.9" />
                            </svg>
                            Warga
                        </button>
                        <button type="button" class="tab" data-role="admin">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" rx="1" />
                                <rect x="14" y="3" width="7" height="7" rx="1" />
                                <rect x="3" y="14" width="7" height="7" rx="1" />
                                <rect x="14" y="14" width="7" height="7" rx="1" />
                            </svg>
                            Admin
                        </button>
                        <button type="button" class="tab" data-role="petugas">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="8" y="2" width="8" height="4" rx="1" />
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                <path d="m9 14 2 2 4-4" />
                            </svg>
                            Petugas
                        </button>
                    </div>

                    <div class="alert-error" id="formError" hidden></div>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="nama@email.com" autocomplete="email">
    

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" autocomplete="current-password">

                    <button type="submit" class="btn-submit">
                        Masuk
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </button>

                    <p class="register">Belum memiliki akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
                </form>

                <div class="hint">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 8v4" />
                        <path d="M12 16h.01" />
                    </svg>
                    Gunakan peran di atas untuk menjelajahi seluruh alur prototype.
                </div>
            </div>
        </main>

    </div>
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>