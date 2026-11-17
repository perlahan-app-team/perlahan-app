<?php

namespace Tests\Feature;

use App\Models\{Lahan, Monitoring, Pengajuan, User, Verifikasi};
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        Storage::fake('local');
        Storage::fake('public');
    }

    private function submission(): Pengajuan
    {
        $lahan = Lahan::create([
            'kode_lahan' => fake()->unique()->uuid(), 'nama_lahan' => 'Lahan Rungkut',
            'alamat' => 'Jl. Rungkut Madya', 'kota' => 'Surabaya', 'luas' => 1250,
            'latitude' => -7.32, 'longitude' => 112.79, 'status' => 'tersedia',
            'kondisi' => 'Tanah datar, akses mudah',
        ]);
        return Pengajuan::create([
            'user_id' => User::factory()->create(['role' => 'masyarakat'])->id,
            'lahan_id' => $lahan->id, 'nama_kegiatan' => 'Kebun Bersama',
            'deskripsi_kegiatan' => 'Kebun pangan warga.', 'tanggal_pengajuan' => today(),
            'status_pengajuan' => 'diajukan', 'dokumen_path' => 'dokumen/test.pdf',
        ]);
    }

    private function photo(): UploadedFile
    {
        // A real PNG without requiring the GD PHP extension.
        return UploadedFile::fake()->createWithContent('lapangan.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
    }

    public function test_complete_assignment_verification_approval_and_monitoring_flow(): void
    {
        $submission = $this->submission();
        $admin = User::factory()->create(['role' => 'admin']);
        $officer = User::factory()->create(['role' => 'petugas_lapangan']);
        $this->actingAs($admin)->post(route('admin.pengajuan.assign', $submission), [
            'petugas_id' => $officer->id, 'tenggat' => today()->addDays(3)->toDateString(),
            'catatan_penugasan' => 'Periksa akses dan kondisi tanah.',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $task = Verifikasi::firstOrFail();
        $this->assertSame('ditugaskan', $submission->fresh()->status_pengajuan);
        $this->actingAs($officer)->get(route('pengajuan.show', $submission))->assertOk()->assertSee('Kirim Hasil Verifikasi');
        $this->post(route('petugas.verifikasi.store', $task), ['hasil' => 'Kondisi sesuai dan akses tersedia.', 'fotos' => [$this->photo()]])->assertSessionHasNoErrors();
        $this->assertSame('diverifikasi', $submission->fresh()->status_pengajuan);
        Storage::disk('local')->assertExists($task->fresh()->foto_paths[0]);
        $submission->lahan->update(['bank_pembayaran' => 'Bank Uji', 'rekening_pembayaran' => '000000', 'penerima_pembayaran' => 'Penerima Uji']);
        $this->actingAs($admin)->post(route('pengajuan.tagihan', $submission), ['total_sewa' => 1500000])->assertSessionHasNoErrors();
        $this->actingAs($submission->user)->post(route('pengajuan.bayar', $submission), ['bukti_bayar' => $this->photo()])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('pengajuan.pembayaran.review', $submission), ['status_pembayaran' => 'terverifikasi', 'catatan_pembayaran' => 'Pembayaran sesuai.'])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('admin.pengajuan.decision', $submission), ['status' => 'disetujui', 'catatan' => 'Verifikasi lapangan sesuai.'])->assertSessionHasNoErrors();
        $this->assertSame('dimanfaatkan', $submission->lahan->fresh()->status);
        $this->actingAs($officer)->post(route('petugas.monitoring.store', $submission), [
            'tanggal' => today()->toDateString(), 'progres' => 40, 'kondisi' => 'baik',
            'catatan' => 'Bedeng tanam selesai.', 'fotos' => [$this->photo()],
        ])->assertSessionHasNoErrors();
        $report = Monitoring::firstOrFail();
        $this->actingAs($submission->user)->get(route('pengajuan.show', $submission))->assertOk()->assertSee('Bedeng tanam selesai.');
        $this->get(route('monitoring'))->assertOk()->assertSee('40%');
        $this->get(route('dokumentasi', ['type' => 'monitoring', 'id' => $report->id, 'index' => 0]))->assertOk();
        $this->assertDatabaseCount('riwayat_statuses', 7);
    }

    public function test_application_can_be_returned_revised_and_resubmitted_only_by_owner(): void
    {
        $submission = $this->submission();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.pengajuan.decision', $submission), ['status' => 'perlu_perbaikan', 'catatan' => 'Lengkapi jadwal kegiatan.'])->assertSessionHasNoErrors();
        $this->actingAs($submission->user)->get(route('masyarakat.pengajuan.index'))->assertOk()->assertSee('Lengkapi jadwal kegiatan.');
        $this->get(route('masyarakat.pengajuan.edit', $submission))->assertOk();
        $this->put(route('masyarakat.pengajuan.update', $submission), ['nama_kegiatan' => 'Kebun Revisi', 'deskripsi_kegiatan' => 'Jadwal kegiatan setiap minggu.'])->assertSessionHasNoErrors()->assertRedirect(route('pengajuan.show', $submission));
        $this->assertSame('diajukan', $submission->fresh()->status_pengajuan);
        $this->get(route('masyarakat.pengajuan.edit', $submission))->assertForbidden();
        $other = User::factory()->create(['role' => 'masyarakat']);
        $this->actingAs($other)->get(route('pengajuan.show', $submission))->assertForbidden();
        $this->put(route('masyarakat.pengajuan.update', $submission), [])->assertForbidden();
    }

    public function test_role_checks_private_documents_and_unassigned_officer_access(): void
    {
        $submission = $this->submission();
        Storage::disk('local')->put($submission->dokumen_path, 'private pdf');
        $officer = User::factory()->create(['role' => 'petugas_lapangan']);
        $this->actingAs($officer)->get(route('pengajuan.show', $submission))->assertForbidden();
        $this->get(route('pengajuan.dokumen', $submission))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($submission->user)->get(route('pengajuan.dokumen', $submission))->assertOk();
        $this->post(route('admin.pengajuan.decision', $submission), ['status' => 'disetujui', 'catatan' => 'Tidak sah'])->assertForbidden();
    }

    public function test_approval_requires_field_verification_and_only_one_active_allocation(): void
    {
        $submission = $this->submission();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.pengajuan.decision', $submission), ['status' => 'disetujui', 'catatan' => 'Belum diverifikasi'])->assertSessionHasErrors('status');
        $this->assertSame('diajukan', $submission->fresh()->status_pengajuan);
        $submission->update(['status_pengajuan' => 'diverifikasi']);
        Verifikasi::create(['pengajuan_id' => $submission->id, 'petugas_id' => User::factory()->create(['role' => 'petugas_lapangan'])->id, 'tenggat' => today(), 'status' => 'selesai']);
        $submission->lahan->update(['status' => 'dimanfaatkan']);
        $this->post(route('admin.pengajuan.decision', $submission), ['status' => 'disetujui', 'catatan' => 'Sudah digunakan'])->assertSessionHasErrors('status');
        $this->assertDatabaseCount('riwayat_statuses', 0);
    }

    public function test_admin_can_manage_land_and_users_and_land_with_history_is_preserved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.lahan.store'), [
            'kode_lahan' => 'LH-NEW', 'nama_lahan' => 'Taman Baru', 'alamat' => 'Jl. Baru',
            'kota' => 'Surabaya', 'luas' => 900, 'latitude' => -7.3, 'longitude' => 112.7,
            'status' => 'tersedia', 'kondisi' => 'Tanah datar', 'foto' => $this->photo(),
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.lahan.index'));
        $land = Lahan::where('kode_lahan', 'LH-NEW')->firstOrFail();
        Storage::disk('public')->assertExists($land->foto_path);
        $this->get(route('lahan.foto', $land))->assertOk();
        $this->get(route('admin.lahan.edit', $land))->assertOk();
        $this->post(route('admin.users.store'), ['name' => 'Petugas Baru', 'email' => 'petugas@example.test', 'role' => 'petugas_lapangan', 'password' => 'secret123', 'password_confirmation' => 'secret123'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'petugas@example.test', 'role' => 'petugas_lapangan']);
        $this->put(route('admin.users.update', $admin), ['name' => $admin->name, 'email' => $admin->email, 'role' => 'masyarakat'])->assertSessionHasErrors('role');
        $submission = $this->submission();
        $this->delete(route('admin.lahan.destroy', $submission->lahan))->assertSessionHasErrors('lahan');
        $this->assertModelExists($submission);
        $this->delete(route('admin.lahan.destroy', $land))->assertSessionHas('success');
        Storage::disk('public')->assertMissing($land->foto_path);
    }

    public function test_portal_pages_render_for_all_roles_with_real_data(): void
    {
        $submission = $this->submission();
        foreach (['admin', 'petugas_lapangan', 'masyarakat'] as $role) {
            $user = $role === 'masyarakat' ? $submission->user : User::factory()->create(compact('role'));
            $this->actingAs($user);
            $routes = ['peta', 'monitoring'];
            if ($role === 'admin') $routes = array_merge($routes, ['admin.index', 'admin.lahan.index', 'admin.lahan.create', 'admin.users.index', 'admin.users.create', 'admin.pengajuan.index', 'admin.verifikasi', 'admin.riwayat']);
            if ($role === 'petugas_lapangan') $routes = array_merge($routes, ['petugas.index', 'petugas.tugas']);
            if ($role === 'masyarakat') $routes = array_merge($routes, ['masyarakat.index', 'data-lahan', 'rekomendasi', 'masyarakat.riwayat', 'masyarakat.profil.show', 'masyarakat.pengajuan.create']);
            foreach ($routes as $route) $this->get(route($route))->assertOk();
        }
        $this->get(route('masyarakat.lahan.show', $submission->lahan))->assertOk()->assertSee('Urban farming komunitas');
        $this->get(route('peta', ['cari' => 'Tidak ada']))->assertOk()->assertDontSee('data-lat=');
    }

    public function test_officer_cannot_submit_another_officers_task_or_repeat_completed_verification(): void
    {
        $submission = $this->submission();
        $submission->update(['status_pengajuan' => 'ditugaskan']);
        $officer = User::factory()->create(['role' => 'petugas_lapangan']);
        $other = User::factory()->create(['role' => 'petugas_lapangan']);
        $task = Verifikasi::create(['pengajuan_id' => $submission->id, 'petugas_id' => $officer->id, 'tenggat' => today()]);
        $this->actingAs($other)->post(route('petugas.verifikasi.store', $task), ['hasil' => 'Tidak berhak', 'fotos' => [$this->photo()]])->assertForbidden();
        $this->actingAs($officer)->post(route('petugas.verifikasi.store', $task), ['hasil' => 'Sesuai kondisi', 'fotos' => [$this->photo()]])->assertSessionHasNoErrors();
        $this->post(route('petugas.verifikasi.store', $task), ['hasil' => 'Duplikat', 'fotos' => [$this->photo()]])->assertSessionHasErrors('hasil');
        $this->assertCount(1, Storage::disk('local')->allFiles('verifikasi'));
        $this->actingAs($other)->get(route('dokumentasi', ['type' => 'verifikasi', 'id' => $task->id, 'index' => 0]))->assertForbidden();
        $this->actingAs($submission->user)->get(route('dokumentasi', ['type' => 'verifikasi', 'id' => $task->id, 'index' => 0]))->assertOk();
    }

    public function test_monitoring_requires_approval_and_validation_cleans_up_files(): void
    {
        $submission = $this->submission();
        $officer = User::factory()->create(['role' => 'petugas_lapangan']);
        Verifikasi::create(['pengajuan_id' => $submission->id, 'petugas_id' => $officer->id, 'tenggat' => today()]);
        $data = ['tanggal' => today()->toDateString(), 'progres' => 30, 'kondisi' => 'baik', 'catatan' => 'Belum disetujui', 'fotos' => [$this->photo()]];
        $this->actingAs($officer)->post(route('petugas.monitoring.store', $submission), $data)->assertSessionHasErrors('catatan');
        $this->assertSame([], Storage::disk('local')->allFiles('monitoring'));
        $submission->update(['status_pengajuan' => 'disetujui']);
        $data['progres'] = 150;
        $data['fotos'] = [$this->photo()];
        $this->post(route('petugas.monitoring.store', $submission), $data)->assertSessionHasErrors('progres');
        $this->assertDatabaseCount('monitorings', 0);
    }

    public function test_pagination_and_recommendation_rules_handle_larger_and_incomplete_datasets(): void
    {
        $submission = $this->submission();
        $admin = User::factory()->create(['role' => 'admin']);
        for ($i = 0; $i < 13; $i++) User::factory()->create(['role' => 'masyarakat']);
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk()->assertSee('Navigasi halaman');
        $land = $submission->lahan;
        $land->kondisi = null;
        $this->assertSame('Pemeriksaan kondisi lahan', \App\Services\RekomendasiLahan::for($land)[0]['title']);
        $land->kondisi = 'Tanah tercemar limbah';
        $this->assertCount(1, \App\Services\RekomendasiLahan::for($land));
        $land->status = 'dimanfaatkan';
        $this->assertSame([], \App\Services\RekomendasiLahan::for($land));
    }
}
