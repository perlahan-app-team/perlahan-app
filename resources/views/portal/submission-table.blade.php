<div class="table-wrap"><table><thead><tr><th>Kegiatan / Lahan</th><th>Calon Penyewa</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@forelse($pengajuans as $item)
<tr><td><strong>{{ $item->nama_kegiatan }}</strong><small>{{ $item->lahan?->nama_lahan }}</small></td><td>{{ $item->user?->name }}</td><td>{{ $item->tanggal_pengajuan?->format('d M Y') }}</td><td>@include('portal.status',['status'=>$item->status_pengajuan])</td><td><a href="{{ route('pengajuan.show',$item) }}">Lihat detail →</a></td></tr>
@empty<tr><td colspan="5" class="empty-state">Belum ada penyewaan.</td></tr>@endforelse
</tbody></table></div>
