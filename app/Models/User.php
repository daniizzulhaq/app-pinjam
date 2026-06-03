<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'nama_bank',
        'no_rekening',
        'nama_pemilik_rekening',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ---- SCOPES ----
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeKaryawan($query)
    {
        return $query->where('role', 'karyawan');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // ---- HELPERS ----
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    // ---- RELATIONS ----
    public function nasabah()
    {
        return $this->hasMany(Nasabah::class);
    }

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function approvedPinjaman()
    {
        return $this->hasMany(Pinjaman::class, 'approved_by');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}