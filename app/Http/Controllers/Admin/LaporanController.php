<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PinjamanExport;
use App\Exports\PembayaranExport;

class LaporanController extends Controller
{
    // ── helper query builder ──────────────────────────────────────────────────

    private function queryPinjaman(Request $request)
    {
        return Pinjaman::with(['nasabah', 'karyawan'])
            ->when($request->bulan,  fn($q) => $q->whereMonth('tanggal_pengajuan', $request->bulan))
            ->when($request->tahun,  fn($q) => $q->whereYear('tanggal_pengajuan', $request->tahun))
            ->when($request->status, fn($q) => $q->where('status', $request->status));
    }

    private function queryPembayaran(Request $request)
    {
        return Pembayaran::with(['pinjaman.nasabah', 'karyawan'])
            ->when($request->bulan, fn($q) => $q->whereMonth('tanggal_bayar', $request->bulan))
            ->when($request->tahun, fn($q) => $q->whereYear('tanggal_bayar', $request->tahun));
    }

    // ── laporan index ─────────────────────────────────────────────────────────

    public function pinjaman(Request $request)
    {
        $query      = $this->queryPinjaman($request);
        $pinjaman   = $query->paginate(15)->withQueryString();
        $totalPokok = (clone $query)->sum('jumlah_pinjaman');
        $totalBunga = (clone $query)->sum('total_bunga');

        return view('admin.laporan.pinjaman', compact('pinjaman', 'totalPokok', 'totalBunga'));
    }

    public function pembayaran(Request $request)
    {
        $query         = $this->queryPembayaran($request);
        $pembayaran    = $query->paginate(15)->withQueryString();
        $totalDibayar  = (clone $query)->sum('jumlah_dibayar');
        $totalDenda    = (clone $query)->sum('denda');

        return view('admin.laporan.pembayaran', compact('pembayaran', 'totalDibayar', 'totalDenda'));
    }

    // ── PDF ───────────────────────────────────────────────────────────────────

    public function pdfPinjaman(Request $request)
    {
        $query      = $this->queryPinjaman($request);
        $pinjaman   = $query->get();
        $totalPokok = $pinjaman->sum('jumlah_pinjaman');
        $totalBunga = $pinjaman->sum('total_bunga');

        $pdf = Pdf::loadView('admin.laporan.pdf.pinjaman', compact('pinjaman', 'totalPokok', 'totalBunga', 'request'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pinjaman.pdf');
    }

    public function pdfPembayaran(Request $request)
    {
        $query        = $this->queryPembayaran($request);
        $pembayaran   = $query->get();
        $totalDibayar = $pembayaran->sum('jumlah_dibayar');
        $totalDenda   = $pembayaran->sum('denda');

        $pdf = Pdf::loadView('admin.laporan.pdf.pembayaran', compact('pembayaran', 'totalDibayar', 'totalDenda', 'request'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pembayaran.pdf');
    }

    // ── Excel ─────────────────────────────────────────────────────────────────

    public function excelPinjaman(Request $request)
    {
        return Excel::download(new PinjamanExport($request), 'laporan-pinjaman.xlsx');
    }

    public function excelPembayaran(Request $request)
    {
        return Excel::download(new PembayaranExport($request), 'laporan-pembayaran.xlsx');
    }
}