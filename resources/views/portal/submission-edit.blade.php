@extends('layouts.portal')
@section('title','Lengkapi Data Sewa')
@section('content')
<div class="page-intro"><div><span class="eyebrow">LENGKAPI DATA SEWA</span><h1>Lengkapi Rencana Kegiatan</h1><p>{{ $pengajuan->lahan->nama_lahan }}</p></div></div>
<section class="panel"><h2>Catatan Admin</h2><p class="note-copy">{{ $pengajuan->riwayatStatuses()->where('status','perlu_perbaikan')->latest('id')->value('catatan') }}</p></section>
<section class="panel"><form method="POST" enctype="multipart/form-data" action="{{ route('masyarakat.pengajuan.update',$pengajuan) }}">@csrf @method('PUT')@include('portal.sewa-fields')
<label class="field"><span>Nama kegiatan</span><input name="nama_kegiatan" value="{{ old('nama_kegiatan',$pengajuan->nama_kegiatan) }}" maxlength="255" required></label><label class="field"><span>Deskripsi rencana kegiatan</span><textarea name="deskripsi_kegiatan" maxlength="5000" required>{{ old('deskripsi_kegiatan',$pengajuan->deskripsi_kegiatan) }}</textarea></label><label class="field"><span>Ganti dokumen pendukung (opsional)</span><input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png"><small>PDF/JPG/PNG, maksimal 5 MB. Kosongkan untuk mempertahankan dokumen sebelumnya.</small></label><div class="form-actions"><button class="btn btn-primary">Kirim Ulang Permintaan Sewa</button><a class="btn btn-secondary" href="{{ route('pengajuan.show',$pengajuan) }}">Batal</a></div></form></section>
@endsection
