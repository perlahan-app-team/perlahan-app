<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lahan_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lahan_id')->constrained('lahans')->cascadeOnDelete();
            $table->string('path');
            $table->string('keterangan')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
            $table->unique(['lahan_id', 'path']);
        });

        // Pertahankan foto yang dahulu disimpan sebagai lahans.foto_path.
        if (Schema::hasColumn('lahans', 'foto_path')) {
            DB::table('lahans')->whereNotNull('foto_path')
                ->where('foto_path', '<>', '')
                ->select(['id', 'foto_path'])
                ->orderBy('id')
                ->chunkById(100, function ($lahans) {
                    foreach ($lahans as $lahan) {
                        DB::table('lahan_fotos')->insertOrIgnore([
                            'lahan_id' => $lahan->id,
                            'path' => $lahan->foto_path,
                            'urutan' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lahan_fotos');
    }
};
