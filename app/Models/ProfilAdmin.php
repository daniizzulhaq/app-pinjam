<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProfilAdmin extends Model
{
    protected $table = 'profil_admin';

    protected $fillable = [
        'nama_lembaga',
        'tagline',
        'logo',
        'no_telepon',
        'email',
        'alamat',
        'rekening',
    ];

    protected $casts = [
        'rekening' => 'array',
    ];

    /**
     * Selalu ambil satu baris (singleton pattern).
     */
    public static function profil(): static
    {
        return static::firstOrCreate([], [
            'nama_lembaga' => 'Pinjamin',
        ]);
    }

    /**
     * URL logo — pakai Storage::disk('public') sama seperti nasabah & bukti pembayaran.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::disk('public')->url($this->logo);
        }

        // Placeholder jika logo belum ada
        $nama = urlencode($this->nama_lembaga ?? 'P');
        return "https://ui-avatars.com/api/?name={$nama}&background=1e40af&color=fff&size=200";
    }
}