# Handoff Backend Adithya — D-09 sampai D-24

Sumber: Jira (3).csv dan ZIP perlahan-app(4).zip, 17 November 2026.
Branch: backend/adithya; kelanjutan dari commit D-08, tidak mengubah kode milik tim lain.

**D-19 dan D-20 sengaja dilewati.** WORK-157, WORK-158, dan WORK-164 tidak dimasukkan ke cakupan commit.

| Hari | Tiket Jira Adithya | Implementasi atau pemeriksaan data |
|---|---|---|
| D-09 | WORK-99 | Tabel pengajuans, kolom pemohon, lahan dan kegiatan yang sudah tersedia |
| D-10 | WORK-108 | Revisi memperbarui data pengajuans; logika pengiriman ulang ada pada controller tim gabungan |
| D-11 | WORK-113 | Status permintaan dipisahkan dari status pembayaran |
| D-12 | WORK-117 | Riwayat perubahan status berelasi ke pengajuan dan pengguna |
| D-13 | WORK-122 | Kolom status dan alasan penolakan pada data pengajuan |
| D-14 | WORK-128 | Tabel dan model verifikasi lapangan beserta relasi petugas/pengajuan |
| D-15 | WORK-135 | Tabel dan model monitoring beserta relasi pengajuan/petugas |
| D-16 | WORK-142 | Pemeriksaan struktur migration dan relasi antarfitur |
| D-17 | WORK-146 | Fillable, casts, dan relasi data siap dipakai antarmuka |
| D-18 | WORK-152 | Pengujian data fitur database di tests/Feature/AdithyaDatabaseD09D24Test.php |
| D-21 | WORK-178 | Kolom tanggal sewa, harga harian, estimasi/tagihan |
| D-22 | WORK-184 | Scope bertabrakan sebagai dukungan validasi jadwal |
| D-23 | WORK-190 | Status pembayaran, bukti, rekening/QR, total biaya |
| D-24 | WORK-196 | Pemeriksaan relasi monitoring dan penyewaan |

## Catatan integrasi

1. Migration lama D-08 sudah membuat kolom kondisi dan foto_path lahan. Migration terbaru hanya membuat tabel verifikasis dan monitorings agar tidak duplikat.
2. Model Lahan mempertahankan relasi fotos() dan rekomendasiLahans() dari D-08; field tarif/pembayaran ditambahkan tanpa menimpa relasi lama.
3. Model Pengajuan mempertahankan riwayatStatuses() dan menambah verifikasis(), monitorings(), tanggal sewa, status pembayaran, dan dukungan pencarian jadwal beririsan.
4. Migration harga demo hanya mengubah tarif untuk kode lahan DEMO-GBG, DEMO-RKT, dan DEMO-WNK yang terdapat pada data simulasi ZIP terbaru.
5. Tidak ada penggantian controller bisnis, auth, UI, atau route pekerjaan anggota lain. Validasi akhir jadwal, penerbitan tagihan, pembayaran dan persetujuan wajib diuji pada branch gabungan.
6. File perlahan.sql, .env, folder .git, akun dan sesi tidak boleh di-commit.
7. Setelah menyatukan branch pada lingkungan pengujian, jalankan php artisan migrate dan php artisan test --filter=AdithyaDatabaseD09D24Test.

## Status pengujian

File pengujian disiapkan, tetapi belum dinyatakan lulus hingga tes benar-benar dijalankan pada checkout branch yang kompatibel. Status Jira tidak otomatis menjadi Done karena commit.
