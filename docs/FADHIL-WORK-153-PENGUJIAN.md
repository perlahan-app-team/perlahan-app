# Pengujian Backend PERLAHAN - WORK-153

Tanggal: 17 November 2026
PIC: Fadhil
Hari: D-18

## Lingkungan Pengujian
- Framework: Laravel
- PHP: 8.3.31
- PHPUnit: 11.5.57
- Database: MySQL/MariaDB XAMPP
- Database pengujian: perlahan_test
- Source pengujian: ZIP PERLAHAN terbaru

## Hasil Pengujian

| Test Suite | Tests Passed | Assertions |
|---|---:|---:|
| MasyarakatPengajuanTest | 6 | 36 |
| WorkflowTest | 9 | 101 |
| SewaPembayaranTest | 6 | 81 |
| KalenderSewaTest | 3 | 22 |
| TOTAL | 24 | 240 |

## Kesimpulan
Seluruh 24 pengujian terpilih berhasil dijalankan
menggunakan konfigurasi MySQL khusus pengujian.

Pengujian mencakup pengajuan, validasi, workflow,
pembayaran, otorisasi, serta kalender sewa.

Pengujian dilakukan pada source ZIP terbaru,
bukan pada branch backend/fadhil secara terpisah.

Database utama perlahan tidak digunakan sebagai
database pengujian otomatis.
