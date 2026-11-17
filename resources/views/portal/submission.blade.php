@extends('layouts.portal')
@section('title','Detail Penyewaan')
@section('content')
@php($role = auth()->user()->role)
<div class="page-intro"><div><span class="eyebrow">SWA-{{ str_pad($pengajuan->id,4,'0',STR_PAD_LEFT) }}</span><h1>{{ $pengajuan->nama_kegiatan }}</h1><p>{{ $pengajuan->lahan?->nama_lahan }} · {{ $pengajuan->lahan?->alamat }}</p></div>@include('portal.status',['status'=>$pengajuan->status_pengajuan])</div>
@include('portal.rental-next-step')
<div class="detail-grid"><div>
<section class="panel"><h2>Rencana kegiatan</h2><dl class="info-list"><dt>Calon Penyewa</dt><dd>{{ $pengajuan->user->name }}</dd><dt>Tanggal</dt><dd>{{ $pengajuan->tanggal_pengajuan->format('d M Y') }}</dd><dt>Luas lahan</dt><dd>{{ number_format($pengajuan->lahan->luas,0,',','.') }} m²</dd><dt>Kondisi</dt><dd>{{ $pengajuan->lahan->kondisi ?? 'Belum diisi' }}</dd></dl><p class="note-copy">{{ $pengajuan->deskripsi_kegiatan }}</p>@if($pengajuan->dokumen_path)<a class="btn btn-secondary" href="{{ route('pengajuan.dokumen',$pengajuan) }}">Unduh dokumen pendukung</a>@endif
@if($role === 'masyarakat' && $pengajuan->status_pengajuan === 'perlu_perbaikan')<a class="btn btn-primary" href="{{ route('masyarakat.pengajuan.edit',$pengajuan) }}">Lengkapi Data Sewa</a>@endif</section>
@include('portal.pembayaran')
<section class="panel"><h2>Verifikasi Lapangan</h2>
@forelse($pengajuan->verifikasis->sortByDesc('id') as $task)
<article class="report-entry"><h3>{{ $task->petugas->name }} · Tenggat {{ $task->tenggat->format('d M Y') }}</h3>@include('portal.status',['status'=>$task->status])<p class="note-copy">{{ $task->catatan_penugasan }}</p>
@if($task->hasil)<p class="note-copy">{{ $task->hasil }}</p>@endif
<div class="photo-row">@foreach($task->foto_paths ?? [] as $index=>$path)<a href="{{ route('dokumentasi',['type'=>'verifikasi','id'=>$task->id,'index'=>$index]) }}"><img src="{{ route('dokumentasi',['type'=>'verifikasi','id'=>$task->id,'index'=>$index]) }}" alt="Dokumentasi pemeriksaan {{ $index+1 }}" loading="lazy"></a>@endforeach</div>
@if($role === 'petugas_lapangan' && $task->petugas_id === auth()->id() && $task->status === 'ditugaskan')
<form method="POST" action="{{ route('petugas.verifikasi.store',$task) }}" enctype="multipart/form-data">@csrf<label class="field"><span>Hasil pemeriksaan lapangan</span><textarea name="hasil" required maxlength="5000">{{ old('hasil') }}</textarea></label><label class="field"><span>Foto lapangan (1–5 JPG/PNG, maksimal 5 MB per foto)</span><input type="file" name="fotos[]" accept="image/jpeg,image/png" multiple required></label><button class="btn btn-primary">Kirim Hasil Verifikasi</button></form>
@endif</article>
@empty<p class="subtle">Belum ada penugasan verifikasi.</p>@endforelse
</section>
<section class="panel"><h2>Perkembangan Kegiatan</h2>
@forelse($pengajuan->monitorings->sortByDesc(fn($m)=>$m->tanggal->format('Y-m-d').str_pad($m->id,12,'0',STR_PAD_LEFT)) as $report)
<article class="report-entry"><h3>{{ $report->tanggal->format('d M Y') }} · {{ $report->progres }}%</h3><progress class="monitoring-progress" value="{{ $report->progres }}" max="100">{{ $report->progres }}%</progress><p>{{ ucfirst(str_replace('_',' ',$report->kondisi)) }} · {{ $report->user->name }}</p><p class="note-copy">{{ $report->catatan }}</p><div class="photo-row">@foreach($report->foto_paths ?? [] as $index=>$path)<a href="{{ route('dokumentasi',['type'=>'monitoring','id'=>$report->id,'index'=>$index]) }}"><img src="{{ route('dokumentasi',['type'=>'monitoring','id'=>$report->id,'index'=>$index]) }}" alt="Dokumentasi monitoring {{ $index+1 }}" loading="lazy"></a>@endforeach</div></article>
@empty<p class="subtle">Belum ada laporan perkembangan.</p>@endforelse
@if($role === 'petugas_lapangan' && $pengajuan->status_pengajuan === 'disetujui')
<h3>Buat Laporan Monitoring</h3><form method="POST" action="{{ route('petugas.monitoring.store',$pengajuan) }}" enctype="multipart/form-data">@csrf<div class="form-grid"><label class="field"><span>Tanggal pengamatan</span><input type="date" name="tanggal" value="{{ old('tanggal',now()->toDateString()) }}" max="{{ now()->toDateString() }}" required></label><label class="field"><span>Progres (%)</span><input type="number" name="progres" min="0" max="100" value="{{ old('progres') }}" required></label></div><label class="field"><span>Kondisi kegiatan</span><select name="kondisi" required>@foreach(['baik','dalam_proses','perlu_tinjauan'] as $condition)<option value="{{ $condition }}" @selected(old('kondisi')===$condition)>{{ ucfirst(str_replace('_',' ',$condition)) }}</option>@endforeach</select></label><label class="field"><span>Catatan perkembangan</span><textarea name="catatan" maxlength="5000" required>{{ old('catatan') }}</textarea></label><label class="field"><span>Foto kegiatan (1–5 JPG/PNG, maksimal 5 MB per foto)</span><input type="file" name="fotos[]" accept="image/jpeg,image/png" multiple required></label><button class="btn btn-primary">Kirim Laporan</button></form>
@endif</section>
</div><aside>
@if($role === 'admin' && $pengajuan->status_pengajuan === 'diajukan')
<section class="panel"><h2>Tugaskan Petugas</h2><form method="POST" action="{{ route('admin.pengajuan.assign',$pengajuan) }}">@csrf<label class="field"><span>Petugas lapangan</span><select name="petugas_id" required><option value="">Pilih petugas</option>@foreach($petugas as $person)<option value="{{ $person->id }}" @selected(old('petugas_id')==$person->id)>{{ $person->name }}</option>@endforeach</select></label><label class="field"><span>Tenggat kunjungan</span><input type="date" name="tenggat" value="{{ old('tenggat') }}" min="{{ now()->toDateString() }}" required></label><label class="field"><span>Catatan penugasan</span><textarea name="catatan_penugasan" maxlength="5000">{{ old('catatan_penugasan') }}</textarea></label><button class="btn btn-primary">Kirim Penugasan</button></form></section>
@endif
@if($role === 'admin' && in_array($pengajuan->status_pengajuan,['diajukan','diverifikasi']))
<section class="panel"><h2>Keputusan Admin</h2><form method="POST" action="{{ route('admin.pengajuan.decision',$pengajuan) }}">@csrf<label class="field"><span>Tindak lanjut</span><select name="status" required><option value="perlu_perbaikan">Kembalikan untuk perbaikan</option><option value="ditolak">Tolak Permintaan Sewa</option>@if($pengajuan->status_pengajuan==='diverifikasi' && $pengajuan->total_sewa && $pengajuan->bukti_bayar_path && $pengajuan->status_pembayaran==='terverifikasi')<option value="disetujui">Setujui Penyewaan</option>@endif</select></label><label class="field"><span>Catatan / alasan keputusan</span><textarea name="catatan" maxlength="5000" required>{{ old('catatan') }}</textarea></label><button class="btn btn-primary">Simpan Keputusan</button></form></section>
@endif
<section class="panel"><h2>Riwayat Penyewaan</h2>@foreach($pengajuan->riwayatStatuses->sortByDesc('id') as $history)<article class="report-entry">@include('portal.status',['status'=>$history->status])<p class="subtle">{{ $history->tanggal_status->format('d M Y H:i') }} · {{ $history->user?->name }}</p><p class="note-copy">{{ $history->catatan }}</p></article>@endforeach</section>
</aside></div>
@endsection
