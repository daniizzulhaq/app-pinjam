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
        $now = now()->setTime(17, 0, 0);

        $query = Pinjaman::with(['nasabah', 'user'])
            ->where('status', 'aktif')
            ->where('tanggal_jatuh_tempo', '<', $now);

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nasabah', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter karyawan (jika bukan admin, hanya lihat pinjaman miliknya)
        // Karyawan hanya bisa lihat pinjaman yang dia ajukan
        $query->where('user_id', auth()->id());

        $pinjamanDenda = $query->orderBy('tanggal_jatuh_tempo', 'asc')->get();

        // Hitung denda per pinjaman
        $pinjamanDenda = $pinjamanDenda->map(function ($pinjaman) use ($now) {
            $jatuhTempo = Carbon::parse($pinjaman->tanggal_jatuh_tempo);
            $hariTerlambat = max(0, $jatuhTempo->diffInDays($now, false) * -1);
            // diffInDays: jika jatuh_tempo < now, selisihnya negatif jika pakai false
            // Cara aman:
            $hariTerlambat = (int) ceil($now->diffInDays($jatuhTempo));
            $dendaPerHari = 50000;
            $totalDenda = $hariTerlambat * $dendaPerHari;

            $pinjaman->hari_terlambat = $hariTerlambat;
            $pinjaman->total_denda = $totalDenda;
            return $pinjaman;
        });

        $totalDendaKeseluruhan = $pinjamanDenda->sum('total_denda');
        $totalPinjamanDenda = $pinjamanDenda->count();

        return view('karyawan.denda.index', compact(
            'pinjamanDenda',
            'totalDendaKeseluruhan',
            'totalPinjamanDenda'
        ));
    }
}