<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lahan extends Model
{
    use HasFactory;

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
    ];

    protected function casts(): array
    {
        return [
            'luas' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
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
