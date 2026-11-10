
# LAPORAN PENGUJIAN QA PERLAHAN
## D-00 sampai D-08

### 1. Identitas
- Nama: Citra Fadillah Agung
- Peran: Product Owner / QA
- Repository: perlahan-app
- Branch: product/citra

### 2. Tujuan Pengujian
Melakukan pengujian terhadap fitur PERLAHAN untuk memastikan setiap fitur berjalan sesuai dengan kebutuhan dan fungsi yang telah ditentukan.

### 3. Daftar Pengujian

| No. | Issue Jira | Pengujian | Hasil |
|---|---|---|---|
| 1 | WORK-45 | Registrasi, Login, dan Hak Akses | PASS |
| 2 | WORK-51 | Pengelolaan Akun dan Role | PASS |
| 3 | WORK-58 | CRUD dan Validasi Lahan | PASS |
| 4 | WORK-65 | Upload dan Foto Lahan | PASS |
| 5 | WORK-72 | Daftar dan Detail Lahan | PASS |
| 6 | WORK-86 | Ketepatan Marker dan Peta | PASS |
| 7 | WORK-94 | Pengujian Rekomendasi | PASS |

### 4. Dokumentasi Hasil Pengujian
Hasil pengujian akan dicatat berdasarkan pelaksanaan pengujian secara langsung pada aplikasi PERLAHAN.

# WORK-45
| No. | Skenario Pengujian | Hasil Aktual | Status |
|---|---|---|---|
| 1 | Registrasi akun baru | Registrasi berhasil | PASS |
| 2 | Login menggunakan akun valid | Login berhasil | PASS |
| 3 | Login menggunakan password salah | Sistem menolak login | PASS |
| 4 | Login sebagai Admin | Diarahkan ke dashboard Admin | PASS |
| 5 | Login sebagai Petugas | Diarahkan ke dashboard Petugas | PASS |
| 6 | Login sebagai Masyarakat | Diarahkan ke dashboard Masyarakat | PASS |
| 7 | Pembatasan akses berdasarkan role | Akses halaman role lain dibatasi | PASS |

# WORK 51
| No. | Skenario Pengujian | Hasil Aktual | Status |
|---|---|---|---|
| 1 | Melihat daftar akun | Daftar akun berhasil ditampilkan | PASS |
| 2 | Menambahkan akun | Akun baru berhasil ditambahkan | PASS |
| 3 | Mengubah informasi akun | Informasi akun berhasil diperbarui | PASS |
| 4 | Mengubah role akun | Role berhasil diperbarui | PASS |
| 5 | Petugas mengakses fitur pengelolaan akun | Akses halaman pengelolaan akun dibatasi | PASS |
| 6 | Masyarakat mengakses fitur pengelolaan akun | Akses halaman pengelolaan akun dibatasi | PASS |

# WORK 58
| No. | Skenario Pengujian | Hasil Aktual | Status |
|---|---|---|---|
| 1 | Menambahkan data lahan | Data lahan berhasil ditambahkan | PASS |
| 2 | Melihat data lahan | Data lahan berhasil ditampilkan | PASS |
| 3 | Mengubah data lahan | Data lahan berhasil diperbarui | PASS |
| 4 | Menghapus data lahan | Data lahan berhasil dihapus | PASS |
| 5 | Menguji validasi data lahan | Sistem menolak informasi yang tidak sesuai | PASS |

# WORK 65
| No. | Skenario Pengujian | Hasil Aktual | Status |
|---|---|---|---|
| 1 | Mengunggah foto lahan | Foto berhasil diunggah melalui halaman edit data lahan | PASS |
| 2 | Memastikan foto tersimpan | Foto berhasil disimpan setelah proses upload | PASS |
| 3 | Memastikan foto ditampilkan | Foto berhasil ditampilkan pada detail lahan | PASS |
| 4 | Memastikan foto tidak tertukar | Foto pada setiap data lahan tampil sesuai dengan lahannya | PASS |

# WORK 72
| No. | Skenario Pengujian | Hasil Aktual | Status |
|---|---|---|---|
| 1 | Memeriksa informasi pada halaman daftar lahan | Informasi pada daftar lahan tampil sesuai | PASS |
| 2 | Memeriksa informasi pada halaman detail lahan | Informasi pada detail lahan tampil sesuai | PASS |
| 3 | Memastikan data sesuai dengan lahan yang dipilih | Data yang ditampilkan sesuai dengan lahan yang dipilih | PASS |

# WORK 86
| No. | Skenario Pengujian | Hasil Aktual | Status |
|---|---|---|---|
| 1 | Memeriksa posisi marker pada peta | Marker muncul pada lokasi yang sesuai | PASS |
| 2 | Memilih marker pada peta | Marker dapat dipilih oleh pengguna | PASS |
| 3 | Memeriksa informasi lahan pada marker | Informasi lahan berhasil ditampilkan | PASS |

# WORK 94
| No. | Skenario Pengujian | Hasil Aktual | Status |
|---|---|---|---|
| 1 | Memeriksa rekomendasi yang ditampilkan | Rekomendasi pemanfaatan lahan berhasil ditampilkan berdasarkan informasi luas dan kondisi lahan | PASS |
| 2 | Memeriksa kesesuaian rekomendasi dengan informasi lahan | Rekomendasi sesuai dengan informasi pada halaman detail lahan | PASS |
| 3 | Memastikan rekomendasi bukan keputusan resmi | Sistem menampilkan pemberitahuan bahwa rekomendasi merupakan informasi awal dan perlu diverifikasi pihak berwenang | PASS |

### 5. Catatan
Pengujian dilakukan secara bertahap berdasarkan kesiapan fitur dan tiket Jira yang menjadi tanggung jawab QA.
