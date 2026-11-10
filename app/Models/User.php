<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Support\PeranPengguna;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_MASYARAKAT = PeranPengguna::MASYARAKAT;
    public const ROLE_ADMIN = PeranPengguna::ADMIN;
    public const ROLE_PETUGAS = PeranPengguna::PETUGAS_LAPANGAN;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeDenganRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }
}
