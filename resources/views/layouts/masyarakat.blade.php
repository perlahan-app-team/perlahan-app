
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'PERLAHAN')</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout-masyarakat.css') }}">

    @stack('styles')
</head>
<body>

    <div class="app-layout">
        @include('components.masyarakat.sidebar')

        <div class="main-area">
            <header class="topbar">
                <div class="page-heading">
                    <small>Portal / @yield('breadcrumb', 'Dashboard')</small>
                    <strong>@yield('page-title', 'Dashboard')</strong>
                </div>

                <div class="topbar-right">
                    <div class="search-box">
                        <span>⌕</span>
                        <input type="search" placeholder="Cari data, lahan...">
                    </div>

                    <button class="notification-button" type="button" aria-label="Notifikasi">
                        ♧
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

            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
