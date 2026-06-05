<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Tenor extends Model
{
    protected $table = 'tenor';
 
    protected $fillable = [
        'bulan',
        'label',
        'tipe', 
        'is_active',
    ];
 
    protected $casts = [
        'is_active' => 'boolean',
    ];
 
    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('bulan');
    }
 
    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class);
    }
}