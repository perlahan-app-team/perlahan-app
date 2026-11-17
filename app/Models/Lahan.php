<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lahan extends Model
{
    use HasFactory;

    public const TARIF_SEWA_HARIAN = 1500000;

    protected $attributes = ['tarif_sewa_harian' => self::TARIF_SEWA_HARIAN];

    protected $table = 'lahans';

    protected $fillable = [
        'kode_lahan',
        'nama_lahan',
        'alamat',
        'kota',
        'luas',
        'latitude',
        'longitude',
        'status',
        'deskripsi',
        'kondisi',
        'foto_path',
        'tarif_sewa_harian',
        'bank_pembayaran',
        'rekening_pembayaran',
        'penerima_pembayaran',
        'qr_pembayaran_path',
    ];

    protected function casts(): array
    {
        return [
            'luas' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'tarif_sewa_harian' => 'integer',
        ];
    }

    /** Foto tambahan, terkait langsung melalui lahan_id. */
    public function fotos(): HasMany
    {
        return $this->hasMany(LahanFoto::class)->orderBy('urutan')->orderBy('id');
    }

    /** Alternatif pemanfaatan; perumusan aturannya tanggung jawab backend logic. */
    public function rekomendasiLahans(): HasMany
    {
        return $this->hasMany(RekomendasiLahan::class);
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }
}
