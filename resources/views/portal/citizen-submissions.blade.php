@extends('layouts.portal')
@section('title','Pengajuan Saya')
@section('content')
<div class="page-intro"><div><span class="eyebrow">RENCANA KEGIATAN</span><h1>Pengajuan Saya</h1><p>Pantau peninjauan, catatan, dan keputusan pengajuan Anda.</p></div><a class="btn btn-primary" href="{{ route('masyarakat.pengajuan.create') }}">+ Pengajuan Baru</a></div>
@forelse($pengajuans as $pengajuan)
<article class="panel citizen-submission">
    <div class="submission-summary"><div><span class="eyebrow">PGJ-{{ str_pad($pengajuan->id,4,'0',STR_PAD_LEFT) }}</span><h2><a href="{{ route('pengajuan.show',$pengajuan) }}">{{ $pengajuan->nama_kegiatan }}</a></h2><p>{{ $pengajuan->lahan?->nama_lahan }} · {{ $pengajuan->tanggal_pengajuan->format('d M Y') }}</p></div>@include('portal.status',['status'=>$pengajuan->status_pengajuan])</div>
    @php($last=$pengajuan->riwayatStatuses->sortByDesc('id')->first())
    @if($last)<div class="submission-note"><strong>Catatan terbaru</strong><p class="note-copy">{{ $last->catatan }}</p><small>{{ $last->tanggal_status->format('d M Y H:i') }}</small></div>@endif
    <div class="form-actions"><a class="btn btn-secondary" href="{{ route('pengajuan.show',$pengajuan) }}">Lihat Detail & Riwayat</a>@if($pengajuan->status_pengajuan==='perlu_perbaikan')<a class="btn btn-primary" href="{{ route('masyarakat.pengajuan.edit',$pengajuan) }}">Perbaiki Pengajuan</a>@endif</div>
</article>
@empty<section class="panel empty-state"><h2>Belum ada pengajuan</h2><p>Anda belum memiliki pengajuan pemanfaatan lahan.</p><a class="btn btn-primary" href="{{ route('masyarakat.pengajuan.create') }}">Buat Pengajuan Pertama</a></section>@endforelse
@if(method_exists($pengajuans,'links')){{ $pengajuans->links() }}@endif
@endsection
