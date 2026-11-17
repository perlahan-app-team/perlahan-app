<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->date('sewa_mulai')->nullable();
            $table->date('sewa_selesai')->nullable();
            $table->string('bukti_bayar_path')->nullable();
            $table->string('status_pembayaran')->nullable();
            $table->text('catatan_pembayaran')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuans', fn (Blueprint $table) => $table->dropColumn([
            'sewa_mulai', 'sewa_selesai', 'bukti_bayar_path',
            'status_pembayaran', 'catatan_pembayaran',
        ]));
    }
};
