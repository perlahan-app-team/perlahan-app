<?php

namespace App\Services;

use App\Models\Lahan;

class RekomendasiLahan
{
    public static function for(Lahan $lahan): array
    {
        if ($lahan->status !== 'tersedia') return [];
        $kondisi = mb_strtolower($lahan->kondisi ?? '');
        if ($kondisi === '' || str_contains($kondisi, 'tercemar') || str_contains($kondisi, 'sengketa')) {
            return [['title' => 'Pemeriksaan kondisi lahan', 'reason' => 'Data kondisi belum cukup atau terdapat indikasi kendala. Petugas perlu meninjau sebelum pemanfaatan.']];
        }
        $items = [['title' => 'Penghijauan lingkungan', 'reason' => 'Alternatif penanaman vegetasi dapat ditinjau sesuai kondisi tanah dan akses air.']];
        if ($lahan->luas >= 500) $items[] = ['title' => 'Urban farming komunitas', 'reason' => 'Luas minimal 500 m² memberi ruang awal untuk rancangan kebun bersama. Kualitas tanah dan air perlu diperiksa.'];
        if ($lahan->luas >= 1000) $items[] = ['title' => 'Taman lingkungan', 'reason' => 'Luas minimal 1.000 m² memberi ruang awal untuk perencanaan taman. Akses publik dan tata ruang perlu diperiksa.'];
        return $items;
    }
}
