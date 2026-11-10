# Kontrak backend Adithya hingga D-08

Hanya subtask berikut yang termasuk dalam cakupan perubahan ini: WORK-44, WORK-55,
WORK-57, WORK-64, WORK-69, WORK-77, WORK-84, dan WORK-93. WORK-34 sudah Done.

## Struktur data

- `users.role`: `masyarakat`, `admin`, `petugas_lapangan` (dengan indeks pencarian).
- `lahans`: identitas, alamat, kota, luas (m²), status, kondisi, deskripsi,
  latitude dan longitude (desimal); `foto_path` dipertahankan agar ekspor SQL lama cocok.
- `lahan_fotos`: satu lahan mempunyai banyak foto. File disimpan tim backend logic
  dan hanya `path`/metadata yang dicatat di tabel ini. Kolom `foto_path` lama tetap ada.
- `rekomendasi_lahans`: banyak alternatif per lahan (`lahan_id`, `jenis_pemanfaatan`,
  `judul`, `alasan`). Tidak ada algoritma rekomendasi atau keputusan persetujuan
  dalam migrasi ini; keduanya di luar tanggung jawab Adithya.

## Endpoint backend

Semua endpoint berikut menggunakan middleware `auth` (Laravel session):

- `GET /data/lahan` daftar JSON terpaginasikan, dengan parameter opsional
  `cari`, `status`, `luas_min`, `luas_max`.
- `GET /data/lahan/{id}` detail JSON berikut relasi `fotos`.
- `GET /data/lahan/koordinat` daftar `{id,nama_lahan,alamat,kota,latitude,longitude}`
  dengan koordinat tidak-null, untuk Leaflet. Posisi adalah angka, bukan string.
- `GET /admin/data/lahan` daftar admin terpaginasikan, wajib role `admin`.
- `POST /admin/data/lahan` buat lahan, role `admin`.
- `PUT /admin/data/lahan/{id}` ubah lahan, role `admin`.
- `DELETE /admin/data/lahan/{id}` hapus lahan, role `admin`; hasil `409` bila
  ada pengajuan yang merujuk ke lahan tersebut.

Pada endpoint baca umum, role `masyarakat` hanya dapat mengakses lahan `tersedia`.
Endpoint admin belum membangun form HTML; itu jatah frontend.

## Instalasi

Jalankan `php artisan migrate` dengan konfigurasi `.env` pribadi. Perubahan
skema mendukung tabel `lahans` dari migrasi Laravel maupun dari SQL lama.
Jangan commit file `.env` atau dump pengguna yang berisi hash password/sesi.

## Integrasi antartim

- **Fadhil (WORK-62):** setelah upload file, simpan `path` dengan
  `$lahan->fotos()->create(['path' => $path]);` (tanpa membuat ulang upload handler).
- **Fadhil (WORK-91):** aturan rekomendasi dapat menyimpan/memperbarui melalui
  `$lahan->rekomendasiLahans()->updateOrCreate(['jenis_pemanfaatan' => $jenis],
  ['judul' => $judul, 'alasan' => $alasan]);`.
- **Jingga/Brilliant:** konsumsi endpoint baca serta relasi foto; frontend
  tidak dibuat atau diubah dalam pengerjaan Adithya.
