@extends('layouts.portal')
@section('title','Monitoring Kegiatan')
@section('content')
@php($role = auth()->user()->role)
<div class="page-intro"><div><span class="eyebrow">DAMPAK KEGIATAN</span><h1>Monitoring Kegiatan</h1><p>Pantau perkembangan dan dokumentasi kegiatan yang sudah disetujui.</p></div></div>
<div class="recommend-grid">@forelse($pengajuans as $item)@php($last=$item->monitorings->first())<article class="panel"><span class="status-pill">{{ $last ? ucfirst(str_replace('_',' ',$last->kondisi)) : 'Belum ada laporan' }}</span><h2>{{ $item->nama_kegiatan }}</h2><p>{{ $item->lahan->nama_lahan }} · {{ $item->lahan->alamat }}</p><div class="progress-head"><span>Progres kegiatan</span><strong>{{ $last?->progres ?? 0 }}%</strong></div><progress class="monitoring-progress" value="{{ $last?->progres ?? 0 }}" max="100">{{ $last?->progres ?? 0 }}%</progress><p class="subtle">Laporan terakhir: {{ $last?->tanggal->format('d M Y') ?? 'Belum tersedia' }}</p><a class="btn btn-secondary" href="{{ route('pengajuan.show',$item) }}">{{ $role==='petugas_lapangan' ? 'Lihat / Buat Laporan' : 'Lihat Perkembangan' }}</a></article>@empty<section class="panel empty-state">Belum ada kegiatan yang disetujui.</section>@endforelse</div>{{ $pengajuans->links() }}
@endsection
