<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Nasabah extends Model
{
    use SoftDeletes;
 
    protected $table = 'nasabah';
 
    protected $fillable = [
        'no_ktp',
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'no_telepon',
        'email',
        'pekerjaan',
        'nama_perusahaan',
        'penghasilan_bulanan',
        'nama_penjamin',
        'no_telepon_penjamin',
        'hubungan_penjamin',
        'foto_ktp',
        'foto_nasabah',
        'user_id',
    ];
 
    protected $casts = [
        'tanggal_lahir'       => 'date',
        'penghasilan_bulanan' => 'decimal:2',
    ];
 
    // ---- ACCESSOR ----
    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
 
    // ---- RELATIONS ----
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
 
    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class);
    }
 
    public function pinjamanAktif()
    {
        return $this->hasMany(Pinjaman::class)->where('status', 'aktif');
    }
}