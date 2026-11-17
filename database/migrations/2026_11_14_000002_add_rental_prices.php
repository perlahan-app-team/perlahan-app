<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lahans', function (Blueprint $table) {
            $table->unsignedBigInteger('tarif_sewa_harian')->default(1500000);
            $table->string('bank_pembayaran')->nullable();
            $table->string('rekening_pembayaran')->nullable();
            $table->string('penerima_pembayaran')->nullable();
            $table->string('qr_pembayaran_path')->nullable();
        });
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->unsignedBigInteger('tarif_sewa_harian')->nullable();
            $table->unsignedBigInteger('total_sewa')->nullable();
            $table->string('tujuan_pembayaran')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuans', fn (Blueprint $table) => $table->dropColumn([
            'tarif_sewa_harian', 'total_sewa', 'tujuan_pembayaran',
        ]));
        Schema::table('lahans', fn (Blueprint $table) => $table->dropColumn([
            'tarif_sewa_harian', 'bank_pembayaran', 'rekening_pembayaran',
            'penerima_pembayaran', 'qr_pembayaran_path',
        ]));
    }
};
