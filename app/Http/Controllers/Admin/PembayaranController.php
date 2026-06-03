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
 
    public function denda()
    {
        $pembayaran = Pembayaran::with(['pinjaman.nasabah'])
            ->where('denda', '>', 0)
            ->latest()
            ->paginate(15);
 
        return view('admin.pembayaran.denda', compact('pembayaran'));
    }
}