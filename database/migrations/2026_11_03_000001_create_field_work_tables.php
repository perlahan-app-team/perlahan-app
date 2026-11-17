<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kondisi dan foto_path lahan sudah ditangani migration D-08.
        Schema::create('verifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->restrictOnDelete();
            $table->date('tenggat');
            $table->string('status')->default('ditugaskan');
            $table->text('catatan_penugasan')->nullable();
            $table->text('hasil')->nullable();
            $table->json('foto_paths')->nullable();
            $table->timestamp('diperiksa_at')->nullable();
            $table->timestamps();
        });

        Schema::create('monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('tanggal');
            $table->unsignedTinyInteger('progres');
            $table->string('kondisi');
            $table->text('catatan');
            $table->json('foto_paths')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitorings');
        Schema::dropIfExists('verifikasis');
    }
};
