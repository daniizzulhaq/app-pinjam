<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
     * Jika belum ada, buat dengan nilai default.
     */
    public static function profil(): static
    {
        return static::firstOrCreate([], [
            'nama_lembaga' => 'Pinjamin',
        ]);
    }

    /**
     * URL logo atau placeholder jika belum ada.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && file_exists(public_path('storage/' . $this->logo))) {
            return asset('storage/' . $this->logo);
        }

        // Placeholder: inisial nama lembaga via UI Avatars
        $nama = urlencode($this->nama_lembaga ?? 'P');
        return "https://ui-avatars.com/api/?name={$nama}&background=1e40af&color=fff&size=200";
    }
}