@extends('layouts.portal')
@section('title','Pengajuan')
@section('content')
<div class="page-intro"><div><span class="eyebrow">PEMANFAATAN LAHAN</span><h1>Pengajuan Masyarakat</h1><p>Tinjau kelengkapan, tugaskan verifikasi, dan catat keputusan.</p></div></div>
<form class="portal-filters" method="GET"><input name="cari" value="{{ request('cari') }}" placeholder="Cari nama kegiatan" aria-label="Cari kegiatan"><select name="status" aria-label="Status pengajuan"><option value="">Semua status</option>@foreach(['diajukan','perlu_perbaikan','ditugaskan','diverifikasi','disetujui','ditolak'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>@endforeach</select><button class="btn btn-primary">Terapkan</button><a class="btn btn-secondary" href="{{ route('admin.pengajuan.index') }}">Reset</a></form>
@include('portal.submission-table')
{{ $pengajuans->links() }}
@endsection
