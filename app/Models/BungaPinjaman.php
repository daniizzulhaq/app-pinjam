<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class BungaPinjaman extends Model
{
    use SoftDeletes;
 
    protected $table = 'bunga_pinjaman';
 
    protected $fillable = [
        'nama_bunga',
        'persentase',
        'jenis',
        'is_active',
    ];
 
    protected $casts = [
        'persentase' => 'decimal:2',
        'is_active'  => 'boolean',
    ];
 
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
 
    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class, 'bunga_id');
    }
}