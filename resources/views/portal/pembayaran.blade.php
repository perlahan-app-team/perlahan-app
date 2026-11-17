<section class="panel" id="pembayaran-sewa" style="scroll-margin-top:100px"><h2>Jadwal Sewa dan Pembayaran</h2>
@if($pengajuan->sewa_mulai && $pengajuan->sewa_selesai)
<p>{{ $pengajuan->sewa_mulai->format('d M Y') }} sampai {{ $pengajuan->sewa_selesai->format('d M Y') }} ({{ (int) $pengajuan->sewa_mulai->diffInDays($pengajuan->sewa_selesai) + 1 }} hari)</p>
@else<p class="subtle">Jadwal sewa belum tercatat pada penyewaan lama ini.</p>@endif
@if($pengajuan->total_sewa)<p>Harga: Rp {{ number_format($pengajuan->tarif_sewa_harian,0,',','.') }} per hari. <strong>Total: Rp {{ number_format($pengajuan->total_sewa,0,',','.') }}</strong></p><p>{{ $pengajuan->tujuan_pembayaran }}</p>@endif
@if($pengajuan->status_pengajuan === 'ditolak' && $pengajuan->bukti_bayar_path)<p class="subtle">Penyewaan ditolak. Hubungi pengelola untuk pemeriksaan pembayaran dan pengembalian dana. Pengembalian dana tidak dilakukan otomatis oleh aplikasi.</p>@endif
@if(!$pengajuan->total_sewa)<p class="subtle">Harga sewa Rp {{ number_format($pengajuan->tarif_sewa_harian ?? $pengajuan->lahan->tarif_sewa_harian,0,',','.') }} per hari. Tagihan belum diterbitkan admin; Anda belum perlu membayar.</p>@endif
@if($role === 'admin' && in_array($pengajuan->status_pengajuan, ['diverifikasi','disetujui']) && !$pengajuan->total_sewa && !$pengajuan->bukti_bayar_path)
@if(!$pengajuan->lahan->qr_pembayaran_path && !($pengajuan->lahan->bank_pembayaran && $pengajuan->lahan->rekening_pembayaran && $pengajuan->lahan->penerima_pembayaran))
<p class="subtle">Tujuan pembayaran belum diisi. Buka pengaturan berikut, unggah QR atau isi rekening, lalu klik Simpan Lahan. Setelah itu kembali ke penyewaan ini untuk menerbitkan tagihan.</p>
@endif
<div class="form-actions"><a class="btn btn-secondary" href="{{ route('admin.lahan.edit',$pengajuan->lahan) }}#pembayaran">Atur QR / Rekening Pembayaran</a></div>
<form method="POST" action="{{ route('pengajuan.tagihan',$pengajuan) }}">@csrf
<label class="field"><span>Total tagihan sewa (Rp)</span><input type="number" name="total_sewa" min="1" max="1000000000000" value="{{ old('total_sewa', $pengajuan->sewa_mulai && $pengajuan->sewa_selesai ? ((int)$pengajuan->sewa_mulai->diffInDays($pengajuan->sewa_selesai)+1)*($pengajuan->tarif_sewa_harian ?? $pengajuan->lahan->tarif_sewa_harian) : '') }}" required><small>Perhitungan awal mengikuti tarif harian yang tercatat untuk penyewaan ini. Periksa total sebelum menerbitkan; tagihan yang terbit tidak dapat ditimpa.</small></label><button class="btn btn-primary">Terbitkan Tagihan</button>
</form>
@endif
@if($role === 'masyarakat' && $pengajuan->total_sewa && in_array($pengajuan->status_pengajuan, ['diverifikasi','disetujui']) && $pengajuan->lahan->qr_pembayaran_path)
<img src="{{ route('lahan.qr',$pengajuan->lahan) }}" alt="QR pembayaran PERLAHAN" width="240" style="max-width:100%;height:auto">
@endif
@if(in_array($role, ['admin', 'masyarakat']))
<p>Status pembayaran: <strong>{{ $pengajuan->status_pembayaran ? ucfirst(str_replace('_', ' ', $pengajuan->status_pembayaran)) : 'Belum ada bukti pembayaran' }}</strong></p>
@if($pengajuan->catatan_pembayaran)<p class="note-copy">{{ $pengajuan->catatan_pembayaran }}</p>@endif
@if($pengajuan->bukti_bayar_path)<a class="btn btn-secondary" href="{{ route('pengajuan.bukti-bayar', $pengajuan) }}">Unduh Bukti Pembayaran</a>@endif
@if($role === 'masyarakat' && (in_array($pengajuan->status_pengajuan, ['diverifikasi','disetujui']) && $pengajuan->total_sewa) && !in_array($pengajuan->status_pembayaran, ['menunggu_verifikasi', 'terverifikasi']))
<p class="subtle">Pastikan nominal dan tujuan pembayaran telah dikonfirmasi kepada pengelola. Bukti yang diunggah akan diperiksa admin.</p>
<form method="POST" enctype="multipart/form-data" action="{{ route('pengajuan.bayar', $pengajuan) }}">@csrf
<label class="field"><span>Bukti pembayaran sewa</span><input type="file" name="bukti_bayar" accept=".pdf,.jpg,.jpeg,.png" required><small>PDF/JPG/PNG, maksimal 5 MB.</small></label>
<button class="btn btn-primary">Kirim Bukti Pembayaran</button></form>
@endif
@if($role === 'admin' && (in_array($pengajuan->status_pengajuan, ['diverifikasi','disetujui']) && $pengajuan->total_sewa) && $pengajuan->status_pembayaran === 'menunggu_verifikasi')
<form method="POST" action="{{ route('pengajuan.pembayaran.review', $pengajuan) }}">@csrf
<label class="field"><span>Hasil pemeriksaan pembayaran</span><select name="status_pembayaran" required><option value="terverifikasi">Bukti diterima</option><option value="ditolak">Tolak dan minta unggah ulang</option></select></label>
<label class="field"><span>Catatan pemeriksaan</span><textarea name="catatan_pembayaran" maxlength="2000" required>{{ old('catatan_pembayaran') }}</textarea></label>
<button class="btn btn-primary">Simpan Pemeriksaan</button></form>
@endif
@endif
</section>
