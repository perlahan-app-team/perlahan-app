<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/** Satu sumber filter data lahan untuk halaman Blade dan endpoint JSON. */
final class FilterLahan
{
    public static function terapkan(Builder $query, array $filter): Builder
    {
        $cari = trim((string) ($filter['cari'] ?? ''));
        if ($cari !== '') {
            $query->where(function (Builder $q) use ($cari) {
                $q->where('nama_lahan', 'like', "%{$cari}%")
                    ->orWhere('alamat', 'like', "%{$cari}%")
                    ->orWhere('kota', 'like', "%{$cari}%")
                    ->orWhere('kode_lahan', 'like', "%{$cari}%");
            });
        }

        $lokasi = trim((string) ($filter['lokasi'] ?? ''));
        if ($lokasi !== '') {
            $query->where(function (Builder $q) use ($lokasi) {
                $q->where('kota', $lokasi)
                    ->orWhere('alamat', 'like', "%{$lokasi}%");
            });
        }

        if (! empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        // Kompatibilitas filter luas pada form terbaru: <1000 atau >=1000 m².
        if (($filter['luas'] ?? null) === 'kecil') {
            $query->where('luas', '<', 1000);
        } elseif (($filter['luas'] ?? null) === 'besar') {
            $query->where('luas', '>=', 1000);
        }
        if (isset($filter['luas_min']) && $filter['luas_min'] !== '') {
            $query->where('luas', '>=', $filter['luas_min']);
        }
        if (isset($filter['luas_max']) && $filter['luas_max'] !== '') {
            $query->where('luas', '<=', $filter['luas_max']);
        }

        return $query;
    }

    /** Pilihan pengurutan dari form terbaru, tanpa menerima kolom SQL arbitrer. */
    public static function urutkan(Builder $query, ?string $urut = null, string $default = 'terbaru'): Builder
    {
        return match ($urut ?: $default) {
            'luas-terbesar' => $query->orderByDesc('luas')->orderBy('id'),
            'luas-terkecil' => $query->orderBy('luas')->orderBy('id'),
            'id-terlama' => $query->orderBy('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }
}
