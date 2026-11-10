<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekomendasi_lahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lahan_id')->constrained('lahans')->cascadeOnDelete();
            $table->string('jenis_pemanfaatan');
            $table->string('judul');
            $table->text('alasan')->nullable();
            $table->timestamps();
            // Satu alternatif per jenis pada setiap lahan, bisa di-update logika Fadhil.
            $table->unique(['lahan_id', 'jenis_pemanfaatan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_lahans');
    }
};
