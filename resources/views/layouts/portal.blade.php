<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal') | PERLAHAN</title>
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal-integration.css') }}">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
    <link rel="stylesheet" href="{{ asset('css/figma-alignment.css') }}">
    <x-site-icons />
    @stack('styles')
</head>
<body>
@php
    $role = auth()->user()->role;
    $roleLabel = match($role) { 'admin' => 'Pemerintah / Admin', 'petugas_lapangan' => 'Petugas Lapangan', default => 'Masyarakat / Komunitas' };
    $menus = match($role) {
        'admin' => [['Dashboard','admin.index'],['Data Lahan','admin.lahan.index'],['Peta Lahan','peta'],['Pengajuan','admin.pengajuan.index'],['Verifikasi Lapangan','admin.verifikasi'],['Monitoring','monitoring'],['Pengguna','admin.users.index'],['Riwayat Data','admin.riwayat']],
        'petugas_lapangan' => [['Dashboard','petugas.index'],['Tugas Verifikasi','petugas.tugas'],['Monitoring & Laporan','monitoring']],
        default => [['Dashboard','masyarakat.index'],['Data Lahan','data-lahan'],['Peta Lahan','peta'],['Rekomendasi','rekomendasi'],['Pengajuan Saya','masyarakat.pengajuan.index'],['Riwayat Pengajuan','masyarakat.riwayat'],['Perkembangan Kegiatan','monitoring'],['Profil Saya','masyarakat.profil.show']]
    };
    $menuIcons = [
        'admin.index' => 'layout-dashboard', 'petugas.index' => 'layout-dashboard', 'masyarakat.index' => 'layout-dashboard',
        'admin.lahan.index' => 'trees', 'data-lahan' => 'trees', 'peta' => 'map', 'rekomendasi' => 'sprout',
        'admin.pengajuan.index' => 'file-text', 'masyarakat.pengajuan.index' => 'file-text', 'admin.verifikasi' => 'clipboard-check',
        'petugas.tugas' => 'clipboard-check', 'monitoring' => 'activity', 'admin.users.index' => 'users',
        'admin.riwayat' => 'file-clock', 'masyarakat.riwayat' => 'history', 'masyarakat.profil.show' => 'user-round'
    ];
    $searchRoute = match($role) { 'admin' => 'admin.lahan.index', 'petugas_lapangan' => 'peta', default => 'data-lahan' };
    $noticeRoute = match($role) { 'admin' => 'admin.pengajuan.index', 'petugas_lapangan' => 'petugas.tugas', default => 'masyarakat.pengajuan.index' };
    $profileRoute = route('profil.show');
@endphp
<div class="app-shell">
    <aside class="sidebar" id="portal-sidebar">
        <div class="side-head"><a href="{{ route('dashboard') }}" aria-label="Beranda PERLAHAN"><x-brand-logo /></a><button type="button" id="menu-close" aria-label="Tutup menu"><x-ui-icon name="x" size="20" /></button></div>
        <div class="role-label"><span>{{ match($role) { 'admin' => 'PA', 'petugas_lapangan' => 'PL', default => 'MK' } }}</span><div><strong>{{ $roleLabel }}</strong><small>Portal PERLAHAN</small></div></div>
        <nav aria-label="Menu utama"><p>MENU UTAMA</p>
            @foreach($menus as [$label,$route])
                @php($isActive = request()->routeIs($route) || ($route==='masyarakat.profil.show' && request()->routeIs('profil.*')) || (in_array($route,['admin.lahan.index','admin.users.index','admin.pengajuan.index','masyarakat.pengajuan.index']) && request()->routeIs(str_replace('.index','.*',$route))))
                <a href="{{ route($route) }}" class="portal-nav {{ $isActive ? 'active' : '' }}" @if($isActive) aria-current="page" @endif><x-ui-icon :name="$menuIcons[$route]" /><span>{{ $label }}</span>@if($isActive)<i></i>@endif</a>
            @endforeach
        </nav>
        <div class="side-foot"><div class="sdg-mini"><x-ui-icon name="leaf" size="18" /><div><strong>Mendukung SDG 15</strong><span>Life on Land</span></div></div><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><x-ui-icon name="log-out" size="18" />Keluar</button></form></div>
    </aside>
    <div class="app-main">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" id="menu-toggle" class="menu-btn" aria-label="Buka menu" aria-controls="portal-sidebar" aria-expanded="false"><x-ui-icon name="menu" size="21" /></button>
                @if($role !== 'masyarakat')<x-brand-logo variant="header" />@endif
                <div><span class="breadcrumb">Portal / @yield('title', 'Dashboard')</span><strong>@yield('title', 'Dashboard')</strong></div>
            </div>
            <div class="topbar-actions">
                <form class="top-search portal-header-search" action="{{ route($searchRoute) }}" method="GET" role="search"><x-ui-icon name="search" size="18" /><input type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari data, lahan..." aria-label="Cari lahan"><button type="submit" class="sr-only">Cari</button></form>
                <a class="icon-btn portal-notice" href="{{ route($noticeRoute) }}" aria-label="{{ $role==='petugas_lapangan' ? 'Buka tugas verifikasi' : 'Buka pengajuan' }}"><x-ui-icon name="bell" size="20" /></a>
                <a class="profile portal-profile-link" href="{{ $profileRoute }}"><span><x-user-initials :name="auth()->user()->name" /></span><div><strong>{{ auth()->user()->name }}</strong><small>{{ $roleLabel }}</small></div><x-ui-icon name="chevron-down" size="16" /></a>
            </div>
        </header>
        <main class="portal-content {{ request()->routeIs('profil.*','masyarakat.profil.*') ? 'profile-shell' : '' }}">
            @if(session('success'))<div class="form-success" role="status">{{ session('success') }}</div>@endif
            @if(session('info'))<div class="alert-banner" role="status">{{ session('info') }}</div>@endif
            @if($errors->any())<div class="validation-errors" role="alert"><strong>Periksa kembali data berikut.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
            <footer class="portal-footer">Prototype akademik PERLAHAN · Bukan sistem resmi Pemerintah Kota Surabaya.</footer>
        </main>
    </div>
</div>
<script>const menuToggle=document.getElementById('menu-toggle');const sidebar=document.getElementById('portal-sidebar');menuToggle.addEventListener('click',function(){const open=sidebar.classList.toggle('open');this.setAttribute('aria-expanded',String(open));});document.getElementById('menu-close').addEventListener('click',()=>{sidebar.classList.remove('open');menuToggle.setAttribute('aria-expanded','false');menuToggle.focus();});document.addEventListener('keydown',event=>{if(event.key==='Escape'){sidebar.classList.remove('open');menuToggle.setAttribute('aria-expanded','false');}});</script>
@stack('scripts')
</body></html>
