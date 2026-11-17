<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verifikasi extends Model
{
    protected $table = 'verifikasis';

    protected $fillable = [
        'pengajuan_id', 'petugas_id', 'tenggat', 'status',
        'catatan_penugasan', 'hasil', 'foto_paths', 'diperiksa_at',
    ];

    protected function casts(): array
    {
        return ['tenggat' => 'date', 'foto_paths' => 'array', 'diperiksa_at' => 'datetime'];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
