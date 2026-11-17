<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('pengajuans')) {
            return view('portal.citizen-submissions', [
                'pengajuans' => collect(),
                'tableBelumTersedia' => true,
            ]);
        }

        $pengajuans = $request->user()
            ->pengajuan()
            ->with(['lahan', 'riwayatStatuses'])
            ->latest()
            ->paginate(10);

        return view('portal.citizen-submissions', [
            'pengajuans' => $pengajuans,
            'tableBelumTersedia' => false,
        ]);
    }

    public function create(Request $request)
    {
        if (!Schema::hasTable('lahans')) {
            return redirect()
                ->route('data-lahan')
                ->with('info', 'Data lahan belum tersedia.');
        }

        $lahans = Lahan::query()
            ->where('status', 'tersedia')
            ->orderBy('nama_lahan')
            ->get();

        $selectedLahanId = $request->query('lahan_id');

        if (
            !$request->filled('lahan_id') ||
            !$lahans->contains(
                fn($item) => (string) $item->id === (string) $selectedLahanId
            )
        ) {
            $selectedLahanId = null;
        }

        return view('portal.citizen-submission-form', [
            'lahan' => $lahans,
            'selectedLahanId' => $selectedLahanId,
        ]);
    }

    public function edit(Request $request, Pengajuan $pengajuan)
    {
        abort_unless($pengajuan->user_id === $request->user()->id, 403);
        abort_unless($pengajuan->status_pengajuan === 'perlu_perbaikan', 403);
        return view('portal.submission-edit', compact('pengajuan'));
    }

    public function update(Request $request, Pengajuan $pengajuan)
    {
        abort_unless($pengajuan->user_id === $request->user()->id, 403);
        abort_unless($pengajuan->status_pengajuan === 'perlu_perbaikan', 403);
        $data = $request->validate([
            'sewa_mulai' => [Rule::requiredIf((bool) $pengajuan->sewa_mulai), 'nullable', 'date_format:Y-m-d', 'after_or_equal:today', 'required_with:sewa_selesai'],
            'sewa_selesai' => ['required_with:sewa_mulai', 'nullable', 'date_format:Y-m-d', 'after_or_equal:sewa_mulai'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi_kegiatan' => ['required', 'string', 'max:5000'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);
        unset($data['dokumen']);
        if ($pengajuan->total_sewa && (
            ($data['sewa_mulai'] ?? null) !== $pengajuan->sewa_mulai?->toDateString()
            || ($data['sewa_selesai'] ?? null) !== $pengajuan->sewa_selesai?->toDateString()
        )) {
            throw \Illuminate\Validation\ValidationException::withMessages(['sewa_mulai' => 'Jadwal dengan tagihan tersimpan tidak dapat diganti melalui revisi. Hubungi pengelola untuk perubahan jadwal dan pembayaran.']);
        }
        $old = $pengajuan->dokumen_path;
        $path = $request->hasFile('dokumen') ? $request->file('dokumen')->store('dokumen-pengajuan', 'local') : null;
        if ($path) $data['dokumen_path'] = $path;
        try {
            DB::transaction(function () use ($request, $pengajuan, $data) {
                $lahan = Lahan::whereKey($pengajuan->lahan_id)->lockForUpdate()->firstOrFail();
                $pengajuan = Pengajuan::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
                if ($pengajuan->status_pengajuan !== 'perlu_perbaikan' || $lahan->status !== 'tersedia') {
                    throw \Illuminate\Validation\ValidationException::withMessages(['nama_kegiatan' => 'Permintaan sewa tidak dapat dikirim ulang atau lahan tidak tersedia.']);
                }
                if (Pengajuan::where('lahan_id', $lahan->id)->where('status_pengajuan', 'disetujui')
                    ->bertabrakan($data['sewa_mulai'] ?? null, $data['sewa_selesai'] ?? null)->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['sewa_mulai' => 'Periode sewa bertabrakan dengan permintaan sewa yang disetujui.']);
                }
                $pengajuan->update($data + ['status_pengajuan' => 'diajukan']);
                $pengajuan->riwayatStatuses()->create([
                    'user_id' => $request->user()->id, 'status' => 'diajukan',
                    'catatan' => 'Permintaan sewa diperbaiki dan dikirim ulang.', 'tanggal_status' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            if ($path) Storage::disk('local')->delete($path);
            throw $e;
        }
        if ($path && $old) Storage::disk('local')->delete($old);
        return redirect()->route('pengajuan.show', $pengajuan)->with('success', 'Perbaikan permintaan sewa berhasil dikirim.');
    }

    public function store(Request $request)
    {
        if (
            !Schema::hasTable('lahans') ||
            !Schema::hasTable('pengajuans') ||
            !Schema::hasTable('riwayat_statuses')
        ) {
            return back()
                ->withInput()
                ->with('info', 'Database permintaan sewa belum siap digunakan.');
        }

        $validated = $request->validate([
            'sewa_mulai' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'sewa_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:sewa_mulai'],


            'lahan_id' => [
                'required',
                'integer',
                Rule::exists('lahans', 'id')
                    ->where('status', 'tersedia'),
            ],
            'nama_kegiatan' => [
                'required',
                'string',
                'max:255',
            ],
            'deskripsi_kegiatan' => [
                'required',
                'string',
                'max:5000',
            ],
            'dokumen' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $path = $request->file('dokumen')->store(
            'dokumen-pengajuan',
            'local'
        );


        try {

            DB::transaction(function () use ($request, $validated, $path) {
                $lahan = Lahan::whereKey($validated['lahan_id'])->lockForUpdate()->firstOrFail();
                if ($lahan->status !== 'tersedia') {
                    throw \Illuminate\Validation\ValidationException::withMessages(['lahan_id' => 'Lahan tidak lagi tersedia.']);
                }
                if (Pengajuan::where('lahan_id', $lahan->id)->where('status_pengajuan', 'disetujui')
                    ->bertabrakan($validated['sewa_mulai'], $validated['sewa_selesai'])->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['sewa_mulai' => 'Lahan sudah dipesan pada periode tersebut. Pilih tanggal lain.']);
                }
                $pengajuan = Pengajuan::create([
                    'user_id' => $request->user()->id,
                    'lahan_id' => $validated['lahan_id'],
                    'nama_kegiatan' => $validated['nama_kegiatan'],
                    'deskripsi_kegiatan' => $validated['deskripsi_kegiatan'],
                    'tanggal_pengajuan' => now()->toDateString(),
                    'status_pengajuan' => 'diajukan',
                    'dokumen_path' => $path,
                    'sewa_mulai' => $validated['sewa_mulai'],
                    'sewa_selesai' => $validated['sewa_selesai'],
                    'tarif_sewa_harian' => $lahan->tarif_sewa_harian,
                ]);

                RiwayatStatus::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $request->user()->id,
                    'status' => 'diajukan',
                    'catatan' => 'Permintaan sewa dibuat oleh masyarakat.',
                    'tanggal_status' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);


            if ($exception instanceof \Illuminate\Validation\ValidationException) throw $exception;

            report($exception);

            return back()
                ->withInput()
                ->with('info', 'Permintaan sewa gagal disimpan. Silakan coba kembali.');
        }

        return redirect()
            ->route('masyarakat.pengajuan.index')
            ->with('success', 'Permintaan sewa berhasil dikirim.');
    }
}
