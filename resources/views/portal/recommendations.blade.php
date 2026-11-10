@extends('layouts.portal')
@section('title','Rekomendasi Pemanfaatan')
@section('content')
<div class="page-intro"><div><span class="eyebrow">POTENSI RUANG HIJAU</span><h1>Rekomendasi Pemanfaatan</h1><p>Alternatif awal berdasarkan luas dan informasi kondisi lahan.</p></div></div>
<div class="alert-banner">Rekomendasi merupakan informasi awal. Kondisi tanah, akses air, status aset, dan tata ruang tetap perlu diverifikasi pihak berwenang.</div>
<div class="recommend-grid">@forelse($lahans as $item)<article class="panel">@if($item->foto_path)<img class="land-photo" src="{{ $item->foto_url }}" alt="Kondisi {{ $item->nama_lahan }}">@endif<span class="status-pill">{{ number_format($item->luas,0,',','.') }} m²</span><h2>{{ $item->nama_lahan }}</h2><p>{{ $item->alamat }}</p>@foreach(\App\Services\RekomendasiLahan::for($item) as $recommendation)<h3>{{ $recommendation['title'] }}</h3><p>{{ $recommendation['reason'] }}</p>@endforeach<a class="btn btn-secondary" href="{{ route('masyarakat.lahan.show',$item) }}">Lihat Detail Lahan</a></article>@empty<section class="panel empty-state">Belum ada lahan tersedia untuk direkomendasikan.</section>@endforelse</div>{{ $lahans->links() }}
@endsection
