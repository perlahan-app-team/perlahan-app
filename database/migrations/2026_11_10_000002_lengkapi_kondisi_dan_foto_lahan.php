<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dump SQL lama sudah memiliki dua kolom ini; jangan tambahkan ulang.
        if (! Schema::hasColumn('lahans', 'kondisi')) {
            Schema::table('lahans', function (Blueprint $table) {
                $table->text('kondisi')->nullable();
            });
        }

        if (! Schema::hasColumn('lahans', 'foto_path')) {
            Schema::table('lahans', function (Blueprint $table) {
                $table->string('foto_path')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Tidak menghapus kolom karena kolom yang sama bisa berasal dari dump SQL lama.
        // Data lama tidak boleh hilang pada rollback.
    }
};
