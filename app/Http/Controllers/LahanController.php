<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Support\FilterLahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LahanController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('lahans')) {
            return view('masyarakat.lahan.index', [
                'lahan' => collect(),
                'tableBelumTersedia' => true,
            ]);
        }

        $query = Lahan::query()
            ->where('status', 'tersedia');

        // Pertahankan parameter `cari` lama sekaligus dukung filter D-06.
        $filter = $request->validate([
            'cari' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'nullable', \Illuminate\Validation\Rule::in([
                'tersedia', 'diajukan', 'dimanfaatkan', 'tidak_aktif',
            ])],
            'luas_min' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'luas_max' => ['sometimes', 'nullable', 'numeric', 'min:0', 'gte:luas_min'],
        ]);
        FilterLahan::terapkan($query, $filter);

        $lahan = $query->latest()->paginate(10)->withQueryString();

        return view('masyarakat.lahan.index', [
            'lahan' => $lahan,
            'tableBelumTersedia' => false,
        ]);
    }

    public function show($id)
    {
        abort_unless(Schema::hasTable('lahans'), 404);

        $lahan = Lahan::query()
            ->with('fotos', 'rekomendasiLahans')
            ->where('status', 'tersedia')
            ->findOrFail($id);

        return view('masyarakat.lahan.show', compact('lahan'));
    }
}
