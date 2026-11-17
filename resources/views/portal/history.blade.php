@extends('layouts.portal')
@section('title','Riwayat Data')
@section('content')
<div class="page-intro"><div><span class="eyebrow">AKTIVITAS PENYEWAAN</span><h1>Riwayat Data</h1><p>Telusuri perubahan status dan laporan perkembangan kegiatan.</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Waktu</th><th>Kegiatan</th><th>Status / Aktivitas</th><th>Oleh</th><th>Catatan</th></tr></thead><tbody>@forelse($histories as $history)<tr><td>{{ $history->tanggal_status->format('d M Y H:i') }}</td><td><a href="{{ route('pengajuan.show',$history->pengajuan) }}">{{ $history->pengajuan->nama_kegiatan }}</a></td><td>@include('portal.status',['status'=>$history->status])</td><td>{{ $history->user?->name }}</td><td class="note-copy">{{ $history->catatan }}</td></tr>@empty<tr><td colspan="5" class="empty-state">Belum ada riwayat.</td></tr>@endforelse</tbody></table></div>{{ $histories->links() }}
@endsection
