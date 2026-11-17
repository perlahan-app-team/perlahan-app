<?php

namespace Tests\Feature;

use App\Models\{Lahan, Pengajuan, User, Verifikasi};
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SewaPembayaranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        Storage::fake('local');
    }

    private function submission(?Lahan $land = null, int $start = 10, int $end = 12): Pengajuan
    {
        $land ??= Lahan::create(['kode_lahan' => fake()->uuid(), 'nama_lahan' => 'Lahan sewa', 'alamat' => 'Surabaya', 'kota' => 'Surabaya', 'luas' => 100, 'status' => 'tersedia', 'bank_pembayaran' => 'Bank Uji', 'rekening_pembayaran' => '00000000', 'penerima_pembayaran' => 'Penerima Uji']);
        return Pengajuan::create([
            'user_id' => User::factory()->create(['role' => 'masyarakat'])->id, 'lahan_id' => $land->id,
            'nama_kegiatan' => 'Sewa kegiatan', 'deskripsi_kegiatan' => 'Kegiatan warga', 'tanggal_pengajuan' => today(),
            'status_pengajuan' => 'diajukan', 'sewa_mulai' => today()->addDays($start), 'sewa_selesai' => today()->addDays($end),
        ]);
    }

    private function ready(Pengajuan $record): void
    {
        $record->update(['status_pengajuan' => 'diverifikasi']);
        Verifikasi::create(['pengajuan_id' => $record->id, 'petugas_id' => User::factory()->create(['role' => 'petugas_lapangan'])->id, 'tenggat' => today(), 'status' => 'selesai']);
    }

    public function test_approval_rejects_only_overlapping_applications_and_records_reason(): void
    {
        $winner = $this->submission();
        $overlap = $this->submission($winner->lahan, 12, 13);
        $other = $this->submission($winner->lahan, 13, 14);
        $this->ready($winner);
        $winner->update(['total_sewa' => 4500000, 'status_pembayaran' => 'terverifikasi', 'bukti_bayar_path' => 'bukti-pembayaran/winner.pdf']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.pengajuan.decision', $winner), ['status' => 'disetujui', 'catatan' => 'Sesuai pemeriksaan.'])->assertSessionHasNoErrors();
        $this->assertSame('ditolak', $overlap->fresh()->status_pengajuan);
        $this->assertNotNull($overlap->fresh()->alasan_penolakan);
        $this->assertDatabaseHas('riwayat_statuses', ['pengajuan_id' => $overlap->id, 'status' => 'ditolak']);
        $this->assertSame('diajukan', $other->fresh()->status_pengajuan);
        $this->ready($other);
        $other->update(['total_sewa' => 3000000, 'status_pembayaran' => 'terverifikasi', 'bukti_bayar_path' => 'bukti-pembayaran/other.pdf']);
        $this->post(route('admin.pengajuan.decision', $other), ['status' => 'disetujui', 'catatan' => 'Jadwal berbeda.'])->assertSessionHasNoErrors();
        $this->assertSame('disetujui', $other->fresh()->status_pengajuan);
    }

    public function test_approved_overlap_cannot_be_approved_twice(): void
    {
        $first = $this->submission();
        $first->update(['status_pengajuan' => 'disetujui']);
        $second = $this->submission($first->lahan);
        $this->ready($second);
        $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('admin.pengajuan.decision', $second), ['status' => 'disetujui', 'catatan' => 'Bertabrakan.'])->assertSessionHasErrors('status');
        $this->assertSame('diverifikasi', $second->fresh()->status_pengajuan);
    }

    public function test_new_rental_requires_valid_dates_and_accepts_one_day(): void
    {
        $record = $this->submission();
        $this->actingAs($record->user);
        $payload = ['lahan_id' => $record->lahan_id, 'nama_kegiatan' => 'Sewa sehari', 'tarif_konfirmasi' => 1500000, 'bukti_bayar' => UploadedFile::fake()->create('bukti.pdf', 10, 'application/pdf'), 'deskripsi_kegiatan' => 'Kegiatan satu hari.', 'dokumen' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf')];
        $this->post(route('masyarakat.pengajuan.store'), $payload)->assertSessionHasErrors(['sewa_mulai', 'sewa_selesai']);
        $payload['sewa_mulai'] = today()->addDays(20)->toDateString();
        $payload['sewa_selesai'] = today()->addDays(19)->toDateString();
        $this->post(route('masyarakat.pengajuan.store'), $payload)->assertSessionHasErrors('sewa_selesai');
        $payload['sewa_selesai'] = $payload['sewa_mulai'];
        $this->post(route('masyarakat.pengajuan.store'), $payload)->assertSessionHasNoErrors();
        $new = Pengajuan::latest('id')->first();
        $this->assertTrue($new->sewa_mulai->equalTo($new->sewa_selesai));
        $this->assertNull($new->total_sewa);
        $this->assertNull($new->status_pembayaran);
        $this->assertNull($new->bukti_bayar_path);
        $record->update(['status_pengajuan' => 'disetujui']);
        $payload['sewa_mulai'] = $record->sewa_mulai->toDateString();
        $payload['sewa_selesai'] = $record->sewa_selesai->toDateString();
        $this->post(route('masyarakat.pengajuan.store'), $payload)->assertSessionHasErrors('sewa_mulai');
    }

    public function test_selected_land_rate_is_saved_without_creating_an_upfront_bill(): void
    {
        $record = $this->submission();
        $record->lahan->update(['tarif_sewa_harian' => 350000]);
        $this->actingAs($record->user)->get(route('masyarakat.pengajuan.create', ['lahan_id' => $record->lahan_id]))
            ->assertOk()->assertSee('data-tarif="350000"', false);
        $this->post(route('masyarakat.pengajuan.store'), [
            'lahan_id' => $record->lahan_id, 'nama_kegiatan' => 'Sewa Gubeng', 'deskripsi_kegiatan' => 'Kegiatan warga.',
            'sewa_mulai' => today()->addDays(25)->toDateString(), 'sewa_selesai' => today()->addDays(26)->toDateString(),
            'tarif_sewa_harian' => 1,
            'dokumen' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
        ])->assertSessionHasNoErrors();
        $rental = Pengajuan::latest('id')->first();
        $this->assertSame(350000, (int) $rental->tarif_sewa_harian);
        $this->assertNull($rental->total_sewa);
        $record->lahan->update(['tarif_sewa_harian' => 450000]);
        $this->get(route('pengajuan.show', $rental))->assertOk()->assertSee('350.000');
    }

    public function test_invoice_payment_review_then_final_approval_with_no_upfront_payment(): void
    {
        $record = $this->submission();
        $record->lahan->update(['bank_pembayaran'=>null, 'rekening_pembayaran'=>null, 'penerima_pembayaran'=>null]);
        $this->actingAs($record->user)->get(route('masyarakat.pengajuan.create'))->assertOk()->assertSee('Informasi Biaya Sewa')->assertDontSee('name="bukti_bayar"', false);
        $payload = ['lahan_id'=>$record->lahan_id, 'nama_kegiatan'=>'Tanpa pembayaran awal', 'deskripsi_kegiatan'=>'Kegiatan warga.', 'sewa_mulai'=>today()->addDays(20)->toDateString(), 'sewa_selesai'=>today()->addDays(22)->toDateString(), 'total_sewa'=>1, 'status_pembayaran'=>'terverifikasi', 'dokumen'=>UploadedFile::fake()->create('dokumen.pdf',10,'application/pdf')];
        $this->post(route('masyarakat.pengajuan.store'),$payload)->assertSessionHasNoErrors();
        $new=Pengajuan::latest('id')->first();
        $this->assertNull($new->total_sewa);
        $this->assertNull($new->bukti_bayar_path);
        $this->post(route('pengajuan.bayar',$new), ['bukti_bayar'=>UploadedFile::fake()->create('bukti.pdf',10,'application/pdf')])->assertForbidden();
        $this->post(route('pengajuan.tagihan',$new),['total_sewa'=>4500000])->assertForbidden();
        $admin=User::factory()->create(['role'=>'admin']);
        $this->actingAs($admin)->post(route('pengajuan.tagihan',$new),['total_sewa'=>4500000])->assertSessionHasErrors('total_sewa');
        $this->ready($new);
        $this->post(route('admin.pengajuan.decision',$new),['status'=>'disetujui','catatan'=>'Belum bayar.'])->assertSessionHasErrors('status');
        $this->post(route('pengajuan.tagihan',$new),['total_sewa'=>4500000])->assertSessionHasErrors('total_sewa');
        $new->lahan->update(['bank_pembayaran'=>'Bank Uji','rekening_pembayaran'=>'000000','penerima_pembayaran'=>'Penerima Uji']);
        $this->post(route('pengajuan.tagihan',$new),['total_sewa'=>4500000])->assertSessionHasNoErrors();
        $this->assertSame(4500000,(int)$new->fresh()->total_sewa);
        $this->assertSame('belum_dibayar',$new->fresh()->status_pembayaran);
        $this->post(route('pengajuan.tagihan',$new),['total_sewa'=>1])->assertSessionHasErrors('total_sewa');
        $this->actingAs($new->user)->get(route('pengajuan.show',$new))->assertOk()->assertSee('4.500.000')->assertSee('Kirim Bukti Pembayaran');
        $this->actingAs($admin)->post(route('admin.pengajuan.decision',$new),['status'=>'disetujui','catatan'=>'Tagihan belum dibayar.'])->assertSessionHasErrors('status');
        $this->actingAs($new->user)->post(route('pengajuan.bayar',$new),['bukti_bayar'=>UploadedFile::fake()->create('bukti.pdf',10,'application/pdf')])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('admin.pengajuan.decision',$new),['status'=>'disetujui','catatan'=>'Bukti belum diperiksa.'])->assertSessionHasErrors('status');
        $this->post(route('pengajuan.pembayaran.review',$new),['status_pembayaran'=>'terverifikasi','catatan_pembayaran'=>'Bukti sesuai.'])->assertSessionHasNoErrors();
        $this->post(route('admin.pengajuan.decision',$new),['status'=>'disetujui','catatan'=>'Pembayaran dan lapangan sesuai.'])->assertSessionHasNoErrors();
        $this->assertSame('disetujui',$new->fresh()->status_pengajuan);
    }
    public function test_payment_upload_is_private_reviewed_and_can_be_resubmitted_only_after_rejection(): void
    {
        $record = $this->submission();
        $upload = fn () => ['bukti_bayar' => UploadedFile::fake()->create('bukti.pdf', 10, 'application/pdf')];
        $this->actingAs($record->user)->post(route('pengajuan.bayar', $record), $upload())->assertForbidden();
        $record->update(['status_pengajuan' => 'disetujui', 'total_sewa' => 4500000]);
        $this->post(route('pengajuan.bayar', $record), ['bukti_bayar' => UploadedFile::fake()->create('script.php', 10)])->assertSessionHasErrors('bukti_bayar');
        $this->post(route('pengajuan.bayar', $record), $upload())->assertSessionHasNoErrors();
        $old = $record->fresh()->bukti_bayar_path;
        Storage::disk('local')->assertExists($old);
        $this->assertSame('menunggu_verifikasi', $record->fresh()->status_pembayaran);
        $this->get(route('pengajuan.bukti-bayar', $record))->assertOk();
        $this->post(route('pengajuan.bayar', $record), $upload())->assertSessionHasErrors('bukti_bayar');
        $other = User::factory()->create(['role' => 'masyarakat']);
        $this->actingAs($other)->get(route('pengajuan.bukti-bayar', $record))->assertForbidden();
        $this->post(route('pengajuan.bayar', $record), $upload())->assertForbidden();
        $this->post(route('pengajuan.pembayaran.review', $record), ['status_pembayaran' => 'terverifikasi', 'catatan_pembayaran' => 'Tidak sah'])->assertForbidden();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('pengajuan.pembayaran.review', $record), ['status_pembayaran' => 'ditolak', 'catatan_pembayaran' => 'Gambar kurang jelas.'])->assertSessionHasNoErrors();
        $this->actingAs($record->user)->get(route('pengajuan.show', $record))->assertOk()->assertSee('Gambar kurang jelas.');
        $this->post(route('pengajuan.bayar', $record), $upload())->assertSessionHasNoErrors();
        Storage::disk('local')->assertMissing($old);
        $this->actingAs($admin)->post(route('pengajuan.pembayaran.review', $record), ['status_pembayaran' => 'terverifikasi', 'catatan_pembayaran' => 'Bukti sesuai.'])->assertSessionHasNoErrors();
        $this->assertSame('terverifikasi', $record->fresh()->status_pembayaran);
        $this->actingAs($record->user)->post(route('pengajuan.bayar', $record), $upload())->assertSessionHasErrors('bukti_bayar');
        $this->assertSame('disetujui', $record->fresh()->status_pengajuan);
    }
}
