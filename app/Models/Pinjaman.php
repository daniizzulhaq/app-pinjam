<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Pinjaman extends Model
{
    use SoftDeletes;

    protected $table = 'pinjaman';

    protected $fillable = [
        'no_pinjaman',
        'nasabah_id',
        'user_id',
        'bunga_id',
        'tenor_id',
        'jumlah_pinjaman',
        'bunga_persen',
        'tenor_bulan',
        'cicilan_per_bulan',
        'total_pinjaman',
        'total_bunga',
        'tanggal_pengajuan',
        'tanggal_approval',
        'tanggal_mulai',
        'tanggal_jatuh_tempo',
        'status',
        'catatan_pengajuan',
        'catatan_approval',
        'approved_by',
    ];

    protected $casts = [
        'jumlah_pinjaman'    => 'decimal:2',
        'bunga_persen'       => 'decimal:2',
        'cicilan_per_bulan'  => 'decimal:2',
        'total_pinjaman'     => 'decimal:2',
        'total_bunga'        => 'decimal:2',
        'tenor_bulan'        => 'integer',   // ← FIX
        'tanggal_pengajuan'  => 'date',
        'tanggal_approval'   => 'date',
        'tanggal_mulai'      => 'date',
        'tanggal_jatuh_tempo'=> 'date',
    ];

    // ---- AUTO GENERATE NO PINJAMAN ----
    public static function generateNoPinjaman(): string
    {
        $tahun  = date('Y');
        $bulan  = date('m');
        $last   = static::whereYear('created_at', $tahun)
                        ->whereMonth('created_at', $bulan)
                        ->count();
        return 'PIN-' . $tahun . $bulan . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    // ---- SCOPE ----
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu_approval');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeJatuhTempo($query)
    {
        return $query->where('status', 'aktif')
                     ->whereDate('tanggal_jatuh_tempo', '<=', now());
    }

    // ---- ACCESSOR ----
    public function getSisaPinjamanAttribute(): float
    {
        $totalDibayar = $this->pembayaran->sum('pokok_dibayar');
        return $this->jumlah_pinjaman - $totalDibayar;
    }

    public function getAngsuranKeBerapaAttribute(): int
    {
        return $this->pembayaran->count() + 1;
    }

    // ---- RELATIONS ----
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bunga()
    {
        return $this->belongsTo(BungaPinjaman::class, 'bunga_id');
    }

    public function tenor()
    {
        return $this->belongsTo(Tenor::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}