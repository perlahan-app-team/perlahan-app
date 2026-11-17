<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Harga simulasi yang sudah ada di ZIP dan dump SQL terbaru.
        foreach ([
            'DEMO-GBG' => 350000,
            'DEMO-RKT' => 450000,
            'DEMO-WNK' => 900000,
        ] as $kode => $tarif) {
            DB::table('lahans')->where('kode_lahan', $kode)->update([
                'tarif_sewa_harian' => $tarif,
            ]);
        }
    }

    public function down(): void
    {
        // Harga terbaru milik pengelola tidak ditimpa pada rollback.
    }
};
