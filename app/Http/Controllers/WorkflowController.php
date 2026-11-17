<?php

namespace App\Http\Controllers;

use App\Models\{Lahan, Monitoring, Pengajuan, RiwayatStatus, User, Verifikasi};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Validation\{Rule, ValidationException};

class WorkflowController extends Controller
{
    public function dashboard(Request $request)
    {
        $admin = $request->user()->role === 'admin';
        $query = Pengajuan::query()->with(['lahan', 'user']);
        if (!$admin) {
            $query->whereHas('verifikasis', fn ($q) => $q->where('petugas_id', $request->user()->id));
        }
        return view('portal.dashboard', [
            'totalLahan' => Lahan::count(),
            'tersedia' => Lahan::where('status', 'tersedia')->count(),
            'menunggu' => (clone $query)->whereIn('status_pengajuan', ['diajukan', 'ditugaskan', 'diverifikasi'])->count(),
            'berjalan' => (clone $query)->where('status_pengajuan', 'disetujui')->count(),
            'pengajuans' => $query->latest()->limit(8)->get(),
        ]);
    }

    public function submissions(Request $request)
    {
        $query = Pengajuan::with(['user', 'lahan']);
        if ($request->filled('status')) $query->where('status_pengajuan', $request->string('status')->toString());
        if ($request->filled('cari')) $query->where('nama_kegiatan', 'like', '%'.$request->string('cari').'%');
        return view('portal.submissions', ['pengajuans' => $query->latest()->paginate(12)->withQueryString()]);
    }

    private function authorizeSubmission(Request $request, Pengajuan $pengajuan): void
    {
        $user = $request->user();
        abort_unless($user->role === 'admin'
            || ($user->role === 'masyarakat' && $pengajuan->user_id === $user->id)
            || ($user->role === 'petugas_lapangan' && $pengajuan->verifikasis()->where('petugas_id', $user->id)->exists()), 403);
    }

    public function show(Request $request, Pengajuan $pengajuan)
    {
        $this->authorizeSubmission($request, $pengajuan);
        $pengajuan->load(['user', 'lahan', 'riwayatStatuses.user', 'verifikasis.petugas', 'monitorings.user']);
        return view('portal.submission', [
            'pengajuan' => $pengajuan,
            'petugas' => $request->user()->role === 'admin' ? User::where('role', 'petugas_lapangan')->orderBy('name')->get() : collect(),
        ]);
    }

    public function document(Request $request, Pengajuan $pengajuan)
    {
        $this->authorizeSubmission($request, $pengajuan);
        abort_unless($pengajuan->dokumen_path && Storage::disk('local')->exists($pengajuan->dokumen_path), 404);
        return Storage::disk('local')->download($pengajuan->dokumen_path);
    }

    private function history(Pengajuan $pengajuan, Request $request, string $status, string $catatan): void
    {
        $pengajuan->riwayatStatuses()->create([
            'user_id' => $request->user()->id, 'status' => $status,
            'catatan' => $catatan, 'tanggal_status' => now(),
        ]);
    }

    public function decision(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['perlu_perbaikan', 'ditolak', 'disetujui'])],
            'catatan' => ['required', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($request, $pengajuan, $data) {
            // Lock land first for all decisions concerning competing applications.
            $lahan = Lahan::whereKey($pengajuan->lahan_id)->lockForUpdate()->firstOrFail();
            $pengajuan = Pengajuan::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
            if (!in_array($pengajuan->status_pengajuan, ['diajukan', 'diverifikasi'], true)) {
                throw ValidationException::withMessages(['status' => 'Permintaan sewa ini belum dapat diputuskan atau sudah selesai ditinjau.']);
            }
            if ($data['status'] === 'disetujui') {
                if (!$pengajuan->total_sewa || !$pengajuan->bukti_bayar_path || $pengajuan->status_pembayaran !== 'terverifikasi') {
                    throw ValidationException::withMessages(['status' => 'Terbitkan tagihan dan verifikasi bukti pembayaran terlebih dahulu sebelum menyetujui permintaan sewa.']);
                }
                if ($pengajuan->status_pengajuan !== 'diverifikasi' || !$pengajuan->verifikasis()->where('status', 'selesai')->exists()) {
                    throw ValidationException::withMessages(['status' => 'Verifikasi lapangan harus selesai sebelum persetujuan.']);
                }
                if (!in_array($lahan->status, ['tersedia', 'diajukan'], true)
                    || Pengajuan::where('lahan_id', $lahan->id)->where('status_pengajuan', 'disetujui')
                        ->bertabrakan($pengajuan->sewa_mulai?->toDateString(), $pengajuan->sewa_selesai?->toDateString())->exists()) {
                    throw ValidationException::withMessages(['status' => 'Lahan sudah dimanfaatkan atau tidak tersedia.']);
                }
                // Pengajuan lama tanpa jadwal tetap memakai alokasi lahan sebelumnya.
                // Sewa bertanggal mengunci periode, sehingga tanggal lain tetap dapat diajukan.
                if (!$pengajuan->sewa_mulai) $lahan->update(['status' => 'dimanfaatkan']);
                $others = Pengajuan::where('lahan_id', $lahan->id)->whereKeyNot($pengajuan->id)
                    ->whereIn('status_pengajuan', ['diajukan', 'ditugaskan', 'diverifikasi', 'perlu_perbaikan'])
                    ->bertabrakan($pengajuan->sewa_mulai?->toDateString(), $pengajuan->sewa_selesai?->toDateString())
                    ->lockForUpdate()->get();
                foreach ($others as $other) {
                    $reason = 'Ditolak otomatis karena lahan pada periode yang sama telah disetujui untuk permintaan sewa SWA-'.$pengajuan->id.'.';
                    $other->update(['status_pengajuan' => 'ditolak', 'alasan_penolakan' => $reason]);
                    $this->history($other, $request, 'ditolak', $reason);
                }
            }
            $pengajuan->update(['status_pengajuan' => $data['status'], 'alasan_penolakan' => $data['status'] === 'ditolak' ? $data['catatan'] : null]);
            $this->history($pengajuan, $request, $data['status'], $data['catatan']);
        });
        return back()->with('success', 'Keputusan permintaan sewa berhasil disimpan.');
    }

    public function assign(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'petugas_id' => ['required', Rule::exists('users', 'id')->where('role', 'petugas_lapangan')],
            'tenggat' => ['required', 'date', 'after_or_equal:today'],
            'catatan_penugasan' => ['nullable', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($request, $pengajuan, $data) {
            $pengajuan = Pengajuan::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
            if ($pengajuan->status_pengajuan !== 'diajukan') {
                throw ValidationException::withMessages(['petugas_id' => 'Hanya permintaan sewa baru yang dapat ditugaskan.']);
            }
            $pengajuan->verifikasis()->create($data);
            $pengajuan->update(['status_pengajuan' => 'ditugaskan']);
            $this->history($pengajuan, $request, 'ditugaskan', 'Petugas lapangan ditugaskan. '.($data['catatan_penugasan'] ?? ''));
        });
        return back()->with('success', 'Tugas verifikasi berhasil dikirim.');
    }

    public function tasks(Request $request)
    {
        $query = Verifikasi::with(['pengajuan.lahan', 'pengajuan.user', 'petugas']);
        if ($request->user()->role !== 'admin') $query->where('petugas_id', $request->user()->id);
        return view('portal.tasks', ['tasks' => $query->orderByRaw("CASE WHEN status = 'ditugaskan' THEN 0 ELSE 1 END")->orderBy('tenggat')->paginate(12)]);
    }

    public function verify(Request $request, Verifikasi $verifikasi)
    {
        abort_unless($verifikasi->petugas_id === $request->user()->id, 403);
        $data = $request->validate([
            'hasil' => ['required', 'string', 'max:5000'],
            'fotos' => ['required', 'array', 'min:1', 'max:5'],
            'fotos.*' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
        $paths = [];
        try {
            foreach ($request->file('fotos', []) as $foto) $paths[] = $foto->store('verifikasi', 'local');
            DB::transaction(function () use ($request, $verifikasi, $data, $paths) {
                $pengajuan = Pengajuan::whereKey($verifikasi->pengajuan_id)->lockForUpdate()->firstOrFail();
                $verifikasi = Verifikasi::whereKey($verifikasi->id)->lockForUpdate()->firstOrFail();
                if ($verifikasi->status !== 'ditugaskan' || $pengajuan->status_pengajuan !== 'ditugaskan') {
                    throw ValidationException::withMessages(['hasil' => 'Tugas ini sudah diselesaikan atau tidak aktif.']);
                }
                $verifikasi->update(['hasil' => $data['hasil'], 'foto_paths' => $paths, 'status' => 'selesai', 'diperiksa_at' => now()]);
                $pengajuan->update(['status_pengajuan' => 'diverifikasi']);
                $this->history($pengajuan, $request, 'diverifikasi', $data['hasil']);
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($paths);
            throw $e;
        }
        return back()->with('success', 'Hasil verifikasi berhasil disimpan.');
    }

    public function monitoring(Request $request)
    {
        $query = Pengajuan::where('status_pengajuan', 'disetujui')->with(['lahan', 'monitorings' => fn ($q) => $q->orderByDesc('tanggal')->orderByDesc('id')]);
        if ($request->user()->role === 'masyarakat') $query->where('user_id', $request->user()->id);
        if ($request->user()->role === 'petugas_lapangan') $query->whereHas('verifikasis', fn ($q) => $q->where('petugas_id', $request->user()->id));
        return view('portal.monitoring', ['pengajuans' => $query->latest()->paginate(12)]);
    }

    public function report(Request $request, Pengajuan $pengajuan)
    {
        $this->authorizeSubmission($request, $pengajuan);
        $data = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'progres' => ['required', 'integer', 'between:0,100'],
            'kondisi' => ['required', Rule::in(['baik', 'dalam_proses', 'perlu_tinjauan'])],
            'catatan' => ['required', 'string', 'max:5000'],
            'fotos' => ['required', 'array', 'min:1', 'max:5'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
        $paths = [];
        try {
            foreach ($request->file('fotos', []) as $foto) $paths[] = $foto->store('monitoring', 'local');
            DB::transaction(function () use ($request, $pengajuan, $data, $paths) {
                $pengajuan = Pengajuan::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
                if ($pengajuan->status_pengajuan !== 'disetujui') {
                    throw ValidationException::withMessages(['catatan' => 'Monitoring hanya untuk permintaan sewa yang disetujui.']);
                }
                $pengajuan->monitorings()->create(collect($data)->except('fotos')->all() + ['user_id' => $request->user()->id, 'foto_paths' => $paths]);
                $this->history($pengajuan, $request, 'monitoring', 'Progres '.$data['progres'].'%: '.$data['catatan']);
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($paths);
            throw $e;
        }
        return back()->with('success', 'Laporan monitoring berhasil dikirim.');
    }

    public function photo(Request $request, string $type, int $id, int $index)
    {
        abort_unless(in_array($type, ['verifikasi', 'monitoring'], true), 404);
        $record = $type === 'verifikasi' ? Verifikasi::findOrFail($id) : Monitoring::findOrFail($id);
        $this->authorizeSubmission($request, $record->pengajuan);
        $path = $record->foto_paths[$index] ?? null;
        abort_unless($path && Storage::disk('local')->exists($path), 404);
        return Storage::disk('local')->response($path, null, ['X-Content-Type-Options' => 'nosniff']);
    }

    public function historyIndex(Request $request)
    {
        $query = RiwayatStatus::with(['pengajuan', 'user']);
        if ($request->user()->role === 'masyarakat') $query->whereHas('pengajuan', fn ($q) => $q->where('user_id', $request->user()->id));
        return view('portal.history', ['histories' => $query->latest('id')->paginate(20)]);
    }
}
