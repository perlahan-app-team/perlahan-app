@php
    $state = $pengajuan->status_pengajuan;
    $payment = $pengajuan->status_pembayaran;
    $next = match (true) {
        $state === 'ditolak' => 'Permintaan sewa ditolak. Baca alasan admin di riwayat. Anda dapat memilih lahan atau tanggal lain.',
        $state === 'perlu_perbaikan' => 'Ada data yang perlu dilengkapi. Baca catatan admin, perbaiki data sewa, lalu kirim kembali.',
        $state === 'disetujui' => 'Penyewaan sudah dikonfirmasi admin. Gunakan lahan sesuai jadwal dan ketentuan yang disepakati.',
        $state === 'diajukan' => 'Permintaan sewa sudah diterima. Tunggu admin memeriksa data dan menugaskan petugas. Anda belum perlu membayar.',
        $state === 'ditugaskan' => 'Petugas sedang memeriksa kondisi lahan. Anda belum perlu membayar.',
        !$pengajuan->total_sewa => 'Pemeriksaan lapangan selesai. Tunggu admin menerbitkan tagihan sewa.',
        $payment === 'menunggu_verifikasi' => 'Bukti pembayaran sudah diterima. Tunggu admin memeriksanya; tidak perlu mengunggah ulang.',
        $payment === 'terverifikasi' => 'Pembayaran sudah diverifikasi. Tunggu konfirmasi akhir dari admin.',
        $payment === 'ditolak' => 'Bukti pembayaran perlu diperbaiki. Baca catatan admin dan unggah bukti pengganti pada bagian pembayaran.',
        default => 'Tagihan sudah tersedia. Periksa total dan tujuan pembayaran, lalu bayar dan unggah bukti pada bagian pembayaran.',
    };
@endphp
@if($role === 'masyarakat')
<section class="panel"><h2>Langkah Selanjutnya</h2><p>{{ $next }}</p>
@if($state === 'diverifikasi' && $pengajuan->total_sewa && in_array($payment, ['belum_dibayar', 'ditolak'], true))<a class="btn btn-primary" href="#pembayaran-sewa">{{ $payment === 'ditolak' ? 'Perbaiki Bukti Pembayaran' : 'Lihat Tagihan & Bayar' }}</a>@endif
</section>
@endif
