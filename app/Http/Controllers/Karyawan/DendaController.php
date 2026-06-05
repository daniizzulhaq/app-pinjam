<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DendaController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan waktu sekarang apa adanya, bukan di-set jam 17:00
        $sekarang = Carbon::now();

        $query = Pinjaman::with(['nasabah', 'tenor'])
            ->where('status', 'aktif')
            ->where('user_id', auth()->id())
            // Jatuh tempo sudah lewat (tanggal_jatuh_tempo < hari ini)
            ->where('tanggal_jatuh_tempo', '<', $sekarang->toDateString());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nasabah', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $pinjamanDenda = $query->orderBy('tanggal_jatuh_tempo', 'asc')->get();

        // Hitung denda per pinjaman
        $pinjamanDenda = $pinjamanDenda->map(function ($pinjaman) use ($sekarang) {
            $jatuhTempo    = Carbon::parse($pinjaman->tanggal_jatuh_tempo)->startOfDay();
            $hariTerlambat = (int) $jatuhTempo->diffInDays($sekarang->copy()->startOfDay());
            $totalDenda    = $hariTerlambat * 50000;

            $pinjaman->hari_terlambat = $hariTerlambat;
            $pinjaman->total_denda    = $totalDenda;
            return $pinjaman;
        });

        $totalDendaKeseluruhan = $pinjamanDenda->sum('total_denda');
        $totalPinjamanDenda    = $pinjamanDenda->count();

        return view('karyawan.denda.index', compact(
            'pinjamanDenda',
            'totalDendaKeseluruhan',
            'totalPinjamanDenda'
        ));
    }
}