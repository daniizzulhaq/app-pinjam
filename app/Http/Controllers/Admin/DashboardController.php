<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\Nasabah;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
 
class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_nasabah'          => Nasabah::count(),
            'total_karyawan'         => User::karyawan()->count(),
            'pinjaman_menunggu'      => Pinjaman::menunggu()->count(),
            'pinjaman_aktif'         => Pinjaman::aktif()->count(),
            'pinjaman_jatuh_tempo'   => Pinjaman::jatuhTempo()->count(),
            'total_pinjaman_bulan_ini' => Pinjaman::whereMonth('tanggal_pengajuan', now()->month)
                                                   ->sum('jumlah_pinjaman'),
            'total_pembayaran_bulan_ini' => Pembayaran::whereMonth('tanggal_bayar', now()->month)
                                                       ->sum('jumlah_dibayar'),
        ];
 
        return view('admin.dashboard', compact('data'));
    }
}