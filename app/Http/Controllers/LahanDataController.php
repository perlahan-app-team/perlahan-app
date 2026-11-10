<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Support\FilterLahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Data backend untuk frontend masyarakat/admin/petugas; bukan pemilik UI. */
class LahanDataController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filter = $this->validasiFilter($request);
        $query = Lahan::query()->with('fotos');
        $this->batasiMasyarakat($request, $query);
        FilterLahan::terapkan($query, $filter);
        FilterLahan::urutkan($query, $filter['urut'] ?? null, 'id-terlama');

        return response()->json($query->paginate(10)->withQueryString());
    }

    public function show(Request $request, Lahan $lahan): JsonResponse
    {
        if ($request->user()->role === 'masyarakat' && $lahan->status !== 'tersedia') {
            abort(404);
        }

        return response()->json(['data' => $lahan->load('fotos', 'rekomendasiLahans')]);
    }

    public function koordinat(Request $request): JsonResponse
    {
        $filter = $this->validasiFilter($request);
        $query = Lahan::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
        $this->batasiMasyarakat($request, $query);
        FilterLahan::terapkan($query, $filter);

        $titik = $query->orderBy('id')->get([
            'id', 'kode_lahan', 'nama_lahan', 'alamat', 'kota',
            'status', 'latitude', 'longitude',
        ])->map(static function (Lahan $lahan) {
            return [
                'id' => $lahan->id,
                'kode_lahan' => $lahan->kode_lahan,
                'nama_lahan' => $lahan->nama_lahan,
                'alamat' => $lahan->alamat,
                'kota' => $lahan->kota,
                'status' => $lahan->status,
                'latitude' => (float) $lahan->latitude,
                'longitude' => (float) $lahan->longitude,
            ];
        });

        return response()->json(['data' => $titik]);
    }

    private function batasiMasyarakat(Request $request, $query): void
    {
        if ($request->user()->role === 'masyarakat') {
            $query->where('status', 'tersedia');
        }
    }

    private function validasiFilter(Request $request): array
    {
        $filter = $request->validate([
            'cari' => ['sometimes', 'nullable', 'string', 'max:255'],
            'lokasi' => ['sometimes', 'nullable', 'string', 'max:255'],
            'luas' => ['sometimes', 'nullable', Rule::in(['kecil', 'besar'])],
            'urut' => ['sometimes', 'nullable', Rule::in(['terbaru', 'luas-terbesar', 'luas-terkecil'])],
            'status' => ['sometimes', 'nullable', Rule::in(['tersedia', 'diajukan', 'dimanfaatkan', 'tidak_aktif'])],
            'luas_min' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'luas_max' => ['sometimes', 'nullable', 'numeric', 'min:0', 'gte:luas_min'],
        ]);
        return $filter;
    }
}
