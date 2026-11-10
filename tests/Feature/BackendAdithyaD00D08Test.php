<?php

namespace Tests\Feature;

use App\Models\Lahan;
use App\Models\LahanFoto;
use App\Models\RekomendasiLahan;
use App\Models\Pengajuan;
use App\Models\User;
use App\Support\PeranPengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackendAdithyaD00D08Test extends TestCase
{
    use RefreshDatabase;

    private function lahan(array $data = []): Lahan
    {
        return Lahan::create(array_merge([
            'kode_lahan' => 'LH-' . fake()->unique()->numerify('#####'),
            'nama_lahan' => 'Kebun Komunitas',
            'alamat' => 'Jl. Rungkut Madya',
            'kota' => 'Surabaya',
            'luas' => 1200,
            'status' => 'tersedia',
            'latitude' => -7.33,
            'longitude' => 112.79,
            'kondisi' => 'Tanah datar',
        ], $data));
    }

    public function test_work_44_role_pengguna_tersimpan_dan_bisa_dicari(): void
    {
        $admin = User::factory()->create(['role' => PeranPengguna::ADMIN]);
        User::factory()->create(['role' => PeranPengguna::MASYARAKAT]);

        $this->assertSame(['masyarakat', 'admin', 'petugas_lapangan'], PeranPengguna::semua());
        $this->assertSame($admin->id, User::denganRole('admin')->sole()->id);
    }

    public function test_work_55_admin_bisa_crud_dan_masyarakat_dilarang(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $masyarakat = User::factory()->create(['role' => 'masyarakat']);
        $data = [
            'kode_lahan' => 'LH-CRUD-01',
            'nama_lahan' => 'Lahan Uji',
            'alamat' => 'Jl. Raya Uji',
            'kota' => 'Surabaya',
            'luas' => 1250,
            'status' => 'tersedia',
            'kondisi' => 'Datar',
            'latitude' => -7.3,
            'longitude' => 112.7,
        ];

        $this->actingAs($masyarakat)->postJson(route('admin.data.lahan.store'), $data)
            ->assertForbidden();

        $response = $this->actingAs($admin)->postJson(route('admin.data.lahan.store'), $data)
            ->assertCreated()->assertJsonPath('data.kode_lahan', 'LH-CRUD-01');
        $id = $response->json('data.id');

        $this->actingAs($admin)->putJson(route('admin.data.lahan.update', $id), [
            'nama_lahan' => 'Lahan Uji Diperbarui',
        ])->assertOk()->assertJsonPath('data.nama_lahan', 'Lahan Uji Diperbarui');

        $this->actingAs($admin)->deleteJson(route('admin.data.lahan.destroy', $id))
            ->assertNoContent();
        $this->assertDatabaseMissing('lahans', ['kode_lahan' => 'LH-CRUD-01']);
    }

    public function test_work_55_validasi_koordinat_dan_kode_lahan_unik(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->lahan(['kode_lahan' => 'LH-EXISTS']);

        $this->actingAs($admin)->postJson(route('admin.data.lahan.store'), [
            'kode_lahan' => 'LH-EXISTS',
            'nama_lahan' => 'Duplikat',
            'alamat' => 'Jl. A',
            'luas' => 100,
            'latitude' => -100,
        ])->assertUnprocessable()->assertJsonValidationErrors(['kode_lahan', 'latitude']);
    }

    public function test_work_57_lahan_berelasi_pengajuan_dan_tidak_bisa_dihapus_jika_digunakan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $masyarakat = User::factory()->create(['role' => 'masyarakat']);
        $lahan = $this->lahan();

        Pengajuan::create([
            'user_id' => $masyarakat->id,
            'lahan_id' => $lahan->id,
            'nama_kegiatan' => 'Penghijauan',
            'deskripsi_kegiatan' => 'Menanam pohon bersama',
            'tanggal_pengajuan' => now()->toDateString(),
            'status_pengajuan' => 'diajukan',
        ]);

        $this->assertCount(1, $lahan->pengajuans);
        $this->actingAs($admin)->deleteJson(route('admin.data.lahan.destroy', $lahan->id))
            ->assertStatus(409);
        $this->assertDatabaseHas('lahans', ['id' => $lahan->id]);
    }

    public function test_work_64_foto_terikat_ke_lahan_yang_benar(): void
    {
        $a = $this->lahan();
        $b = $this->lahan();
        $a->fotos()->create(['path' => 'lahan/foto-a.jpg', 'urutan' => 1]);
        $b->fotos()->create(['path' => 'lahan/foto-b.jpg', 'urutan' => 1]);

        $this->assertSame(['lahan/foto-a.jpg'], $a->fotos->pluck('path')->all());
        $this->assertSame(['lahan/foto-b.jpg'], $b->fotos->pluck('path')->all());
        $this->assertSame($a->id, LahanFoto::firstWhere('path', 'lahan/foto-a.jpg')->lahan->id);
    }

    public function test_work_69_daftar_detail_tidak_membocorkan_lahan_tidak_tersedia(): void
    {
        $masyarakat = User::factory()->create(['role' => 'masyarakat']);
        $tersedia = $this->lahan();
        $tersembunyi = $this->lahan(['status' => 'tidak_aktif']);
        $tersedia->fotos()->create(['path' => 'lahan/detail.jpg']);

        $this->actingAs($masyarakat)->getJson(route('data.lahan.index'))
            ->assertOk()->assertJsonCount(1, 'data');
        $this->actingAs($masyarakat)->getJson(route('data.lahan.show', $tersedia->id))
            ->assertOk()->assertJsonPath('data.fotos.0.path', 'lahan/detail.jpg');
        $this->actingAs($masyarakat)->getJson(route('data.lahan.show', $tersembunyi->id))
            ->assertNotFound();
    }

    public function test_work_77_filter_lokasi_status_dan_luas(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->lahan(['kota' => 'Surabaya', 'luas' => 1500, 'status' => 'tersedia']);
        $this->lahan(['kota' => 'Malang', 'luas' => 300, 'status' => 'diajukan']);

        $this->actingAs($admin)->getJson(route('data.lahan.index', [
            'cari' => 'Surabaya', 'status' => 'tersedia',
            'luas_min' => 1000, 'luas_max' => 2000,
        ]))->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_work_77_filter_versi_proyek_terbaru_tetap_terbatas_pada_backend(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kecil = $this->lahan(['kode_lahan' => 'LH-SBY-K', 'kota' => 'Surabaya', 'luas' => 700]);
        $besar = $this->lahan(['kode_lahan' => 'LH-SBY-B', 'kota' => 'Surabaya', 'luas' => 1900]);
        $sedang = $this->lahan(['kode_lahan' => 'LH-SBY-S', 'kota' => 'Surabaya', 'luas' => 1400]);
        $luar = $this->lahan(['kode_lahan' => 'LH-MLG-K', 'kota' => 'Malang', 'luas' => 300]);

        // Cari kode lahan persis dari formulir terbaru (pencarian seperti LIKE).
        $this->actingAs($admin)->getJson(route('data.lahan.index', ['cari' => 'LH-SBY-K']))
            ->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $kecil->id);

        // Lokasi, luas kategoris, dan pengurutan dapat digabungkan.
        $this->actingAs($admin)->getJson(route('data.lahan.index', [
            'lokasi' => 'Surabaya', 'luas' => 'besar', 'urut' => 'luas-terkecil',
        ]))->assertOk()->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $sedang->id)
            ->assertJsonPath('data.1.id', $besar->id);

        // Nilai filter dari pengguna tidak boleh menjadi nama kolom/sort SQL.
        $this->actingAs($admin)->getJson(route('data.lahan.index', ['luas' => 'bebas']))
            ->assertUnprocessable()->assertJsonValidationErrors('luas');
        $this->actingAs($admin)->getJson(route('data.lahan.index', ['urut' => 'raw-sql']))
            ->assertUnprocessable()->assertJsonValidationErrors('urut');
    }

    public function test_work_84_koordinat_untuk_peta_berupa_angka_dan_tidak_null(): void
    {
        $masyarakat = User::factory()->create(['role' => 'masyarakat']);
        $ada = $this->lahan();
        $this->lahan(['latitude' => null, 'longitude' => null]);
        $this->lahan(['status' => 'tidak_aktif']);

        $response = $this->actingAs($masyarakat)->getJson(route('data.lahan.koordinat'))
            ->assertOk()->assertJsonCount(1, 'data');
        $this->assertSame($ada->id, $response->json('data.0.id'));
        $this->assertIsFloat($response->json('data.0.latitude'));
    }

    public function test_work_93_rekomendasi_punya_relasi_lahan_dan_tidak_mengubah_status(): void
    {
        $lahan = $this->lahan();
        $lahan->rekomendasiLahans()->create([
            'jenis_pemanfaatan' => 'urban_farming',
            'judul' => 'Urban Farming Komunitas',
            'alasan' => 'Alternatif untuk dipertimbangkan.',
        ]);

        $this->assertSame($lahan->id, RekomendasiLahan::first()->lahan->id);
        $this->assertCount(1, $lahan->rekomendasiLahans);
        $viewer = User::factory()->create(['role' => 'masyarakat']);
        $this->actingAs($viewer)->getJson(route('data.lahan.show', $lahan->id))
            ->assertOk()->assertJsonPath('data.rekomendasi_lahans.0.jenis_pemanfaatan', 'urban_farming');
        $this->assertSame('tersedia', $lahan->refresh()->status);
    }
}
