<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Monitoring extends Model
{
    protected $table = 'monitorings';

    protected $fillable = [
        'pengajuan_id', 'user_id', 'tanggal', 'progres', 'kondisi',
        'catatan', 'foto_paths',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'progres' => 'integer', 'foto_paths' => 'array'];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
