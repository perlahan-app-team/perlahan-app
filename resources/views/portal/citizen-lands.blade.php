@extends('layouts.portal')
@section('title','Data Lahan')
@section('content')
<div class="page-intro"><div><span class="eyebrow">INVENTARIS RUANG KOTA</span><h1>Data Lahan</h1><p>Jelajahi lahan yang berpotensi dimanfaatkan bersama.</p></div><a class="btn btn-secondary" href="{{ route('peta') }}">Lihat Peta Lahan</a></div>
<form method="GET" action="{{ route('data-lahan') }}" class="portal-filters">
    <input type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari nama, kode, atau alamat lahan" aria-label="Cari lahan">
    <select name="lokasi" aria-label="Filter lokasi"><option value="">Semua lokasi</option>@foreach($lokasiOptions as $lokasi)<option value="{{ $lokasi }}" @selected(request('lokasi')===$lokasi)>{{ $lokasi }}</option>@endforeach</select>
    <select name="status" aria-label="Filter status"><option value="">Semua status</option>@foreach(['tersedia','diajukan','dimanfaatkan','tidak_aktif'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>@endforeach</select>
    <select name="luas" aria-label="Filter luas"><option value="">Semua luas</option><option value="kecil" @selected(request('luas')==='kecil')>Di bawah 1.000 m²</option><option value="besar" @selected(request('luas')==='besar')>1.000 m² ke atas</option></select>
    <select name="urut" aria-label="Urutkan lahan"><option value="terbaru" @selected(request('urut','terbaru')==='terbaru')>Terbaru</option><option value="luas-terbesar" @selected(request('urut')==='luas-terbesar')>Luas terbesar</option><option value="luas-terkecil" @selected(request('urut')==='luas-terkecil')>Luas terkecil</option></select>
    <button class="btn btn-primary">Terapkan Filter</button><a class="btn btn-secondary" href="{{ route('data-lahan') }}">Reset</a>
</form>
<p class="subtle">{{ method_exists($lahan,'total') ? $lahan->total() : $lahan->count() }} lahan ditemukan.</p>
<div class="citizen-land-grid">@forelse($lahan as $item)@include('portal.land-card')@empty<section class="panel empty-state"><h2>{{ $tableBelumTersedia ? 'Data lahan belum tersedia' : 'Lahan tidak ditemukan' }}</h2><p>{{ $tableBelumTersedia ? 'Siapkan database terlebih dahulu.' : 'Coba ubah kata kunci atau filter pencarian Anda.' }}</p></section>@endforelse</div>
@if(method_exists($lahan,'links')){{ $lahan->links() }}@endif
@endsection
