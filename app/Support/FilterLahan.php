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
                    ->orWhere('kota', 'like', "%{$cari}%");
            });
        }

        if (! empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (isset($filter['luas_min']) && $filter['luas_min'] !== '') {
            $query->where('luas', '>=', $filter['luas_min']);
        }
        if (isset($filter['luas_max']) && $filter['luas_max'] !== '') {
            $query->where('luas', '<=', $filter['luas_max']);
        }

        return $query;
    }
}
