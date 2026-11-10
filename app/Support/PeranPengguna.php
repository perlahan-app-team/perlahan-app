<?php

namespace App\Support;

/** Nilai role yang dipakai bersama model, data, dan middleware. */
final class PeranPengguna
{
    public const MASYARAKAT = 'masyarakat';
    public const ADMIN = 'admin';
    public const PETUGAS_LAPANGAN = 'petugas_lapangan';

    public static function semua(): array
    {
        return [self::MASYARAKAT, self::ADMIN, self::PETUGAS_LAPANGAN];
    }
}
