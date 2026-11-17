<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuans';

    protected $fillable = [
        'user_id',
        'lahan_id',
        'nama_kegiatan',
        'deskripsi_kegiatan',
        'tanggal_pengajuan',
        'status_pengajuan',
        'alasan_penolakan',
        'dokumen_path',
        'sewa_mulai',
        'sewa_selesai',
        'bukti_bayar_path',
        'status_pembayaran',
        'catatan_pembayaran',
        'tarif_sewa_harian',
        'total_sewa',
        'tujuan_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'sewa_mulai' => 'date',
            'sewa_selesai' => 'date',
            'tarif_sewa_harian' => 'integer',
            'total_sewa' => 'integer',
        ];
    }

    /**
     * D-22: rentang tanggal inklusif; pengajuan lama tanpa tanggal
     * dianggap bertabrakan. Pemanggil memilih status relevan.
     */
    public function scopeBertabrakan($query, ?string $mulai, ?string $selesai)
    {
        if (! $mulai || ! $selesai) {
            return $query;
        }

        return $query->where(function ($q) use ($mulai, $selesai) {
            $q->whereNull('sewa_mulai')
                ->orWhereNull('sewa_selesai')
                ->orWhere(function ($dates) use ($mulai, $selesai) {
                    $dates->whereDate('sewa_mulai', '<=', $selesai)
                        ->whereDate('sewa_selesai', '>=', $mulai);
                });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lahan(): BelongsTo
    {
        return $this->belongsTo(Lahan::class);
    }

    public function verifikasis(): HasMany
    {
        return $this->hasMany(Verifikasi::class);
    }

    public function monitorings(): HasMany
    {
        return $this->hasMany(Monitoring::class);
    }

    public function riwayatStatuses(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class);
    }
}
