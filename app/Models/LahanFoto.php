<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LahanFoto extends Model
{
    protected $table = 'lahan_fotos';

    protected $fillable = ['lahan_id', 'path', 'keterangan', 'urutan'];

    public function lahan(): BelongsTo
    {
        return $this->belongsTo(Lahan::class);
    }
}
