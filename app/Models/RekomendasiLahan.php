<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Alternatif informasional, bukan persetujuan resmi pemanfaatan lahan. */
class RekomendasiLahan extends Model
{
    protected $table = 'rekomendasi_lahans';

    protected $fillable = ['lahan_id', 'jenis_pemanfaatan', 'judul', 'alasan'];

    public function lahan(): BelongsTo
    {
        return $this->belongsTo(Lahan::class);
    }
}
