<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Pembayaran extends Model
{
    protected $table = 'pembayaran';
 
    protected $fillable = [
        'no_pembayaran',
        'pinjaman_id',
        'user_id',
        'angsuran_ke',
        'tanggal_jatuh_tempo_cicilan',
        'tanggal_bayar',
        'jumlah_cicilan',
        'jumlah_dibayar',
        'pokok_dibayar',
        'bunga_dibayar',
        'denda',
        'hari_terlambat',
        'jenis_pembayaran',
        'status',
        'keterangan',
    ];
 
    protected $casts = [
        'tanggal_jatuh_tempo_cicilan' => 'date',
        'tanggal_bayar'               => 'date',
        'jumlah_cicilan'              => 'decimal:2',
        'jumlah_dibayar'              => 'decimal:2',
        'pokok_dibayar'               => 'decimal:2',
        'bunga_dibayar'               => 'decimal:2',
        'denda'                       => 'decimal:2',
    ];
 
    // ---- AUTO GENERATE NO PEMBAYARAN ----
    public static function generateNoPembayaran(): string
    {
        $tahun = date('Y');
        $bulan = date('m');
        $last  = static::whereYear('created_at', $tahun)
                       ->whereMonth('created_at', $bulan)
                       ->count();
        return 'PAY-' . $tahun . $bulan . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
 
    // ---- RELATIONS ----
    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }
 
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}