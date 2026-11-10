<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Endpoint data saja; halaman/form Blade dikerjakan tim frontend. */
class LahanDataController extends Controller
{
    private const STATUS = ['tersedia', 'diajukan', 'dimanfaatkan', 'tidak_aktif'];

    public function index(): JsonResponse
    {
        return response()->json(['data' => Lahan::query()->orderBy('id')->paginate(15)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validasi($request);
        $lahan = Lahan::create($data);

        return response()->json(['data' => $lahan], 201);
    }

    public function update(Request $request, Lahan $lahan): JsonResponse
    {
        $data = $this->validasi($request, $lahan);
        $lahan->update($data);

        return response()->json(['data' => $lahan->refresh()]);
    }

    public function destroy(Lahan $lahan): JsonResponse
    {
        // FK pengajuan memakai RESTRICT, jangan sampai terjadi error 500.
        if ($lahan->pengajuans()->exists()) {
            return response()->json([
                'message' => 'Lahan memiliki pengajuan dan tidak dapat dihapus.',
            ], 409);
        }

        $lahan->delete();

        return response()->json(null, 204);
    }

    private function validasi(Request $request, ?Lahan $lahan = null): array
    {
        $partial = $lahan !== null;
        $required = $partial ? 'sometimes|required' : 'required';
        $rules = [
            'kode_lahan' => [$required, 'string', 'max:255', Rule::unique('lahans', 'kode_lahan')->ignore($lahan?->id)],
            'nama_lahan' => [$required, 'string', 'max:255'],
            'alamat' => [$required, 'string', 'max:5000'],
            'kota' => ['nullable', 'string', 'max:255'],
            'luas' => [$required, 'numeric', 'gt:0', 'max:9999999999.99'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['sometimes', Rule::in(self::STATUS)],
            'deskripsi' => ['nullable', 'string', 'max:20000'],
            'kondisi' => ['nullable', 'string', 'max:20000'],
        ];

        return $request->validate($rules);
    }
}
