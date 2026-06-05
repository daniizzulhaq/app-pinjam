<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $pembayaran = Pembayaran::with(['pinjaman.nasabah', 'karyawan'])
            ->when($request->bulan, fn($q) =>
                $q->whereMonth('tanggal_bayar', $request->bulan)
            )
            ->when($request->tahun, fn($q) =>
                $q->whereYear('tanggal_bayar', $request->tahun)
            )
            ->latest()
            ->paginate(15);

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pinjaman.nasabah', 'karyawan', 'pinjaman.bunga', 'pinjaman.tenor']);

        $totalBayarSebelumnya = Pembayaran::where('pinjaman_id', $pembayaran->pinjaman_id)
            ->where('id', '<', $pembayaran->id)
            ->sum('jumlah_dibayar');

        $totalBayarSampaiIni = $totalBayarSebelumnya + $pembayaran->jumlah_dibayar;
        $sisaSetelahBayar    = max(0, $pembayaran->pinjaman->total_pinjaman - $totalBayarSampaiIni);

        return view('admin.pembayaran.show', compact(
            'pembayaran',
            'totalBayarSebelumnya',
            'totalBayarSampaiIni',
            'sisaSetelahBayar',
        ));
    }

    public function denda()
    {
        $pembayaran = Pembayaran::with(['pinjaman.nasabah'])
            ->where('denda', '>', 0)
            ->latest()
            ->paginate(15);

        return view('admin.pembayaran.denda', compact('pembayaran'));
    }
}