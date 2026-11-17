<?php

namespace Tests\Feature;

use App\Models\Lahan;
use App\Models\Monitoring;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use App\Models\User;
use App\Models\Verifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdithyaDatabaseD09D24Test extends TestCase
{
    use RefreshDatabase;

    private function lahan(): Lahan
    {
        return Lahan::create([
            'kode_lahan' => 'D24-UJI-' . uniqid(),
            'nama_lahan' => 'Lahan Pengujian',
            'alamat' => 'Jalan Uji',
            'kota' => 'Surabaya',
            'luas' => 1000,
            'status' => 'tersedia',
            'tarif_sewa_harian' => 450000,
        ]);
    }

    private function pengajuan(Lahan $lahan, User $user): Pengajuan
    {
        return Pengajuan::create([
            'user_id' => $user->id,
            'lahan_id' => $lahan->id,
            'nama_kegiatan' => 'Kegiatan Uji',
            'deskripsi_kegiatan' => 'Uji relasi data.',
            'tanggal_pengajuan' => now()->toDateString(),
            'status_pengajuan' => 'disetujui',
            'sewa_mulai' => '2026-12-01',
            'sewa_selesai' => '2026-12-03',
            'tarif_sewa_harian' => 450000,
            'total_sewa' => 1350000,
            'status_pembayaran' => 'terverifikasi',
        ]);
    }

    public function test_kolom_periode_tarif_dan_pembayaran_tersedia(): void
    {
        foreach ([
            'sewa_mulai', 'sewa_selesai', 'bukti_bayar_path',
            'status_pembayaran', 'catatan_pembayaran',
            'tarif_sewa_harian', 'total_sewa', 'tujuan_pembayaran',
        ] as $kolom) {
            $this->assertTrue(Schema::hasColumn('pengajuans', $kolom), $kolom);
        }
        foreach ([
            'tarif_sewa_harian', 'bank_pembayaran', 'rekening_pembayaran',
            'penerima_pembayaran', 'qr_pembayaran_path',
        ] as $kolom) {
            $this->assertTrue(Schema::hasColumn('lahans', $kolom), $kolom);
        }
        $this->assertTrue(Schema::hasTable('verifikasis'));
        $this->assertTrue(Schema::hasTable('monitorings'));
    }

    public function test_relasi_database_pengajuan_ke_verifikasi_monitoring_dan_riwayat(): void
    {
        $pemohon = User::factory()->create(['role' => 'masyarakat']);
        $petugas = User::factory()->create(['role' => 'petugas_lapangan']);
        $pengajuan = $this->pengajuan($this->lahan(), $pemohon);

        $verifikasi = Verifikasi::create([
            'pengajuan_id' => $pengajuan->id,
            'petugas_id' => $petugas->id,
            'tenggat' => '2026-11-30',
            'status' => 'selesai',
        ]);
        $monitoring = Monitoring::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $petugas->id,
            'tanggal' => '2026-12-01',
            'progres' => 50,
            'kondisi' => 'baik',
            'catatan' => 'Uji data monitoring.',
            'foto_paths' => ['monitoring/uji.jpg'],
        ]);
        RiwayatStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $pemohon->id,
            'status' => 'diajukan',
            'tanggal_status' => now(),
        ]);

        $segar = $pengajuan->fresh();
        $this->assertTrue($segar->verifikasis->contains($verifikasi->id));
        $this->assertTrue($segar->monitorings->contains($monitoring->id));
        $this->assertCount(1, $segar->riwayatStatuses);
        $this->assertSame($segar->id, $monitoring->pengajuan->id);
        $this->assertSame($petugas->id, $verifikasi->petugas->id);
        $this->assertSame(['monitoring/uji.jpg'], $monitoring->fresh()->foto_paths);
    }

    public function test_jadwal_beririsan_dikenali_dan_jadwal_tidak_beririsan_diabaikan(): void
    {
        $lahan = $this->lahan();
        $this->pengajuan($lahan, User::factory()->create(['role' => 'masyarakat']));
        $dasar = Pengajuan::query()
            ->where('lahan_id', $lahan->id)
            ->where('status_pengajuan', 'disetujui');

        $this->assertTrue((clone $dasar)->bertabrakan('2026-12-03', '2026-12-04')->exists());
        $this->assertFalse((clone $dasar)->bertabrakan('2026-12-04', '2026-12-05')->exists());
    }
}
