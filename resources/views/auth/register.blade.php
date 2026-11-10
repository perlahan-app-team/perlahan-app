<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Daftar — PERLAHAN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <style>
        .register-box {
            max-width: 600px;
        }

        .register-box .subtitle {
            margin-bottom: 24px;
        }

        .register-form .form-group {
            margin-bottom: 18px;
        }

        .register-form label {
            display: block;
            margin-bottom: 10px;
            font-weight: 700;
            font-size: 15px;
        }

        .register-form input[type="text"],
        .register-form input[type="email"],
        .register-form input[type="password"] {
            display: block;
            width: 100%;
            margin: 0;
            padding: 18px 20px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            color: var(--ink);
            font: 500 16px 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: .2s;
        }

        .register-form input::placeholder {
            color: #a7b0ab;
        }

        .register-form input:focus {
            border-color: var(--green-600);
            box-shadow: 0 0 0 4px rgba(22, 163, 74, .12);
        }

        .register-form .is-invalid {
            border-color: #b91c1c;
        }

        .register-form .field-error {
            display: block;
            margin-top: 7px;
            color: #b91c1c;
            font-size: 13px;
        }

        .register-form .btn-submit {
            margin-top: 8px;
        }

        .register-box .register {
            margin: 14px 0 24px;
        }

        .register-errors {
            margin-bottom: 20px;
        }

        .register-errors ul {
            padding-left: 20px;
            margin-top: 6px;
        }

        .register-note {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 18px 22px;
            border-radius: 12px;
            background: var(--green-50);
            color: #3f5a4a;
            font-size: 14px;
            line-height: 1.7;
        }

        @media (max-width: 960px) {
            .register-visual {
                min-height: 320px;
            }

            .register-visual .login__logo img {
                height: 90px;
            }

            .register-visual .login__caption {
                padding: 0 8px 12px;
            }

            .register-visual .login__caption p {
                font-size: 16px;
            }

            .register-box h1 {
                font-size: 34px;
            }

            .register-box .subtitle {
                margin-bottom: 24px;
            }

            .register-note {
                padding: 16px;
            }
        }

        @media (max-width: 480px) {
            .register-form input[type="text"],
            .register-form input[type="email"],
            .register-form input[type="password"] {
                padding: 15px 16px;
                font-size: 15px;
            }

            .register-box h1 {
                font-size: 28px;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth-alignment.css') }}">
    <x-site-icons />
</head>

<body>
    <div class="login">

        <!-- PANEL KIRI: desain Jingga -->
        <aside
            class="login__visual register-visual"
            style="background-image: url('{{ asset('images/figma-auth.jpg') }}')"
        >
            <a href="{{ route('landing') }}" class="login__logo">
                <x-brand-logo />
            </a>

            <div class="login__caption">
                <span class="pill">Surabaya berkelanjutan</span>
                <h2>Setiap jengkal lahan punya potensi untuk tumbuh.</h2>
                <p>Mari ubah ruang yang terlupakan menjadi manfaat yang dirasakan bersama.</p>
            </div>
        </aside>

        <!-- PANEL KANAN: form registrasi Laravel -->
        <main class="login__panel">
            <div class="login__box register-box">

                <span class="eyebrow">Bergabung bersama kami</span>
                <h1>Buat Akun PERLAHAN</h1>
                <p class="subtitle">
                    Daftar untuk mulai mengakses layanan pengajuan lahan.
                </p>

                @if ($errors->any())
                    <div class="alert-error register-errors" role="alert">
                        <strong>Pendaftaran belum berhasil.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    class="register-form"
                    method="POST"
                    action="{{ route('register.store') }}"
                >
                    @csrf

                    <div class="form-group">
                        <label for="name">Nama lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap"
                            autocomplete="name"
                            maxlength="255"
                            required
                            autofocus
                            class="@error('name') is-invalid @enderror"
                        >
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            maxlength="255"
                            required
                            class="@error('email') is-invalid @enderror"
                        >
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            minlength="8"
                            autocomplete="new-password"
                            required
                            class="@error('password') is-invalid @enderror"
                        >
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Masukkan ulang password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn-submit">
                        Daftar Sekarang
                        <span aria-hidden="true">›</span>
                    </button>

                    <p class="register">
                        Sudah punya akun?
                        <a href="{{ route('login') }}">Masuk di sini</a>
                    </p>
                </form>

                <div class="register-note">
                    <span aria-hidden="true">ⓘ</span>
                    <div>
                        Dengan mendaftar, Anda dapat menggunakan layanan PERLAHAN
                        sesuai ketentuan yang berlaku.
                    </div>
                </div>

            </div>
        </main>

    </div>
</body>
</html>
