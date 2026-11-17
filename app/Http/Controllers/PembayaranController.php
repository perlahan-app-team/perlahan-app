<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Validation\{Rule, ValidationException};

class PembayaranController extends Controller
{
    public function invoice(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate(['total_sewa' => ['required', 'integer', 'min:1', 'max:1000000000000']]);
        DB::transaction(function () use ($request, $pengajuan, $data) {
            $land = \App\Models\Lahan::whereKey($pengajuan->lahan_id)->lockForUpdate()->firstOrFail();
            $record = Pengajuan::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
            if (!in_array($record->status_pengajuan, ['diverifikasi', 'disetujui'], true) || !$record->verifikasis()->where('status', 'selesai')->exists() || $record->total_sewa || $record->bukti_bayar_path) {
                throw ValidationException::withMessages(['total_sewa' => 'Tagihan hanya dapat diterbitkan sekali setelah verifikasi lapangan selesai.']);
            }
            if (!$land->qr_pembayaran_path && (!$land->bank_pembayaran || !$land->rekening_pembayaran || !$land->penerima_pembayaran)) {
                throw ValidationException::withMessages(['total_sewa' => 'Lengkapi QR atau rekening pembayaran di Edit Lahan sebelum menerbitkan tagihan.']);
            }
            $record->update($data + [
                'tarif_sewa_harian' => $record->tarif_sewa_harian ?? $land->tarif_sewa_harian,
                'tujuan_pembayaran' => $land->qr_pembayaran_path ? 'QR pembayaran PERLAHAN' : $land->bank_pembayaran.' / '.$land->rekening_pembayaran.' / a.n. '.$land->penerima_pembayaran,
                'status_pembayaran' => 'belum_dibayar',
            ]);
            $this->history($record, $request, 'Admin menerbitkan tagihan sewa Rp '.number_format($data['total_sewa'], 0, ',', '.').'.');
        });
        return back()->with('success', 'Tagihan diterbitkan. Pemohon dapat membayar melalui detail permintaan sewa.');
    }

    public function qr(\App\Models\Lahan $lahan)
    {
        abort_unless($lahan->qr_pembayaran_path && Storage::disk('public')->exists($lahan->qr_pembayaran_path), 404);
        return Storage::disk('public')->response($lahan->qr_pembayaran_path);
    }

    public function store(Request $request, Pengajuan $pengajuan)
    {
        abort_unless($pengajuan->user_id === $request->user()->id, 403);
        abort_unless($this->canPay($pengajuan), 403);
        $request->validate(['bukti_bayar' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']]);
        $path = $request->file('bukti_bayar')->store('bukti-pembayaran', 'local');
        try {
            $old = DB::transaction(function () use ($request, $pengajuan, $path) {
                $record = Pengajuan::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
                if (!$this->canPay($record) || in_array($record->status_pembayaran, ['menunggu_verifikasi', 'terverifikasi'], true)) {
                    throw ValidationException::withMessages(['bukti_bayar' => 'Bukti sedang diperiksa atau sudah diterima admin.']);
                }
                $old = $record->bukti_bayar_path;
                $record->update(['bukti_bayar_path' => $path, 'status_pembayaran' => 'menunggu_verifikasi', 'catatan_pembayaran' => null]);
                $this->history($record, $request, 'Bukti pembayaran diunggah dan menunggu pemeriksaan admin.');
                return $old;
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }
        if ($old) Storage::disk('local')->delete($old);
        return back()->with('success', 'Bukti pembayaran berhasil dikirim.');
    }

    public function document(Request $request, Pengajuan $pengajuan)
    {
        abort_unless($request->user()->role === 'admin' || $pengajuan->user_id === $request->user()->id, 403);
        abort_unless($pengajuan->bukti_bayar_path && Storage::disk('local')->exists($pengajuan->bukti_bayar_path), 404);
        return Storage::disk('local')->download($pengajuan->bukti_bayar_path);
    }

    public function review(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'status_pembayaran' => ['required', Rule::in(['terverifikasi', 'ditolak'])],
            'catatan_pembayaran' => ['required', 'string', 'max:2000'],
        ]);
        DB::transaction(function () use ($request, $pengajuan, $data) {
            $record = Pengajuan::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
            if (!$this->canPay($record) || $record->status_pembayaran !== 'menunggu_verifikasi' || !$record->bukti_bayar_path) {
                throw ValidationException::withMessages(['status_pembayaran' => 'Tidak ada bukti pembayaran yang menunggu pemeriksaan.']);
            }
            $record->update($data);
            $this->history($record, $request, 'Pembayaran '.str_replace('_', ' ', $data['status_pembayaran']).': '.$data['catatan_pembayaran']);
        });
        return back()->with('success', 'Pemeriksaan pembayaran berhasil disimpan.');
    }

    private function history(Pengajuan $record, Request $request, string $note): void
    {
        $record->riwayatStatuses()->create(['user_id' => $request->user()->id, 'status' => $record->status_pengajuan, 'catatan' => $note, 'tanggal_status' => now()]);
    }

    private function canPay(Pengajuan $record): bool
    {
        return in_array($record->status_pengajuan, ['diverifikasi', 'disetujui'], true) && (bool) $record->total_sewa;
    }
}
