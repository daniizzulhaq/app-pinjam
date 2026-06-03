<?php

namespace App\Http\Controllers\Karyawan;
 
use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\Pinjaman;
 
class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
 
        $data = [
            'total_nasabah'     => Nasabah::where('user_id', $userId)->count(),
            'pinjaman_aktif'    => Pinjaman::where('user_id', $userId)->aktif()->count(),
            'pinjaman_menunggu' => Pinjaman::where('user_id', $userId)->menunggu()->count(),
            'pinjaman_jatuh_tempo' => Pinjaman::where('user_id', $userId)->jatuhTempo()->count(),
        ];
 
        return view('karyawan.dashboard', compact('data'));
    }
}