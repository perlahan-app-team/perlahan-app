<?php

namespace App\Http\Controllers;

use App\Models\{Lahan, User, Verifikasi};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Validation\{Rule, ValidationException};

class ManagementController extends Controller
{
    public function lands(Request $request)
    {
        $query = Lahan::query();
        if ($request->filled('cari')) $query->where(fn ($q) => $q->where('nama_lahan', 'like', '%'.$request->string('cari').'%')->orWhere('alamat', 'like', '%'.$request->string('cari').'%'));
        return view('portal.lands', ['lahans' => $query->latest()->paginate(12)->withQueryString()]);
    }

    public function landForm(?Lahan $lahan = null)
    {
        return view('portal.land-form', ['lahan' => $lahan ?? new Lahan]);
    }

    public function saveLand(Request $request, ?Lahan $lahan = null)
    {
        $data = $request->validate([
            'kode_lahan' => ['required', 'string', 'max:100', Rule::unique('lahans')->ignore($lahan)],
            'nama_lahan' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:2000'],
            'kota' => ['required', 'string', 'max:100'],
            'luas' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'status' => ['required', Rule::in(['tersedia', 'tidak_aktif'])],
            'kondisi' => ['required', 'string', 'max:5000'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
        unset($data['foto']);
        $old = $lahan?->foto_path;
        $path = $request->hasFile('foto') ? $request->file('foto')->store('lahan', 'public') : null;
        if ($path) $data['foto_path'] = $path;
        try {
            DB::transaction(function () use ($lahan, $data) {
                if (!$lahan) {
                    Lahan::create($data);
                    return;
                }
                $locked = Lahan::whereKey($lahan->id)->lockForUpdate()->firstOrFail();
                if (in_array($locked->status, ['diajukan', 'dimanfaatkan'], true)) $data['status'] = $locked->status;
                $locked->update($data);
            });
        } catch (\Throwable $e) {
            if ($path) Storage::disk('public')->delete($path);
            throw $e;
        }
        if ($path && $old) Storage::disk('public')->delete($old);
        return redirect()->route('admin.lahan.index')->with('success', 'Data lahan berhasil disimpan.');
    }

    public function deleteLand(Lahan $lahan)
    {
        if ($lahan->pengajuans()->exists()) {
            throw ValidationException::withMessages(['lahan' => 'Lahan dengan riwayat pengajuan tidak dapat dihapus. Ubah menjadi tidak aktif bila tidak digunakan.']);
        }
        $path = $lahan->foto_path;
        $lahan->delete();
        if ($path) Storage::disk('public')->delete($path);
        return back()->with('success', 'Data lahan dihapus.');
    }

    public function users(Request $request)
    {
        $query = User::query();
        if ($request->filled('cari')) $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->string('cari').'%')->orWhere('email', 'like', '%'.$request->string('cari').'%'));
        return view('portal.users', ['users' => $query->latest()->paginate(12)->withQueryString()]);
    }

    public function userForm(?User $user = null)
    {
        return view('portal.user-form', ['account' => $user ?? new User]);
    }

    public function saveUser(Request $request, ?User $user = null)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'masyarakat', 'petugas_lapangan'])],
        ]);
        if ($user && $user->role !== $data['role']) {
            if ($user->id === $request->user()->id) {
                throw ValidationException::withMessages(['role' => 'Anda tidak dapat mengganti peran akun sendiri.']);
            }
            if ($user->pengajuan()->exists() || Verifikasi::where('petugas_id', $user->id)->exists()) {
                throw ValidationException::withMessages(['role' => 'Peran pengguna yang sudah memiliki pengajuan atau tugas tidak dapat diganti.']);
            }
        }
        if (empty($data['password'])) unset($data['password']);
        $user ? $user->update($data) : User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil disimpan.');
    }
}
