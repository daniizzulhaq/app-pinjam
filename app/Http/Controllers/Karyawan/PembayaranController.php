<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\{Pinjaman, Pembayaran};
use Carbon\Carbon;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function create(Pinjaman $pinjaman)
    {
        abort_if($pinjaman->user_id !== auth()->id(), 403);
        abort_if($pinjaman->status !== 'aktif', 403, 'Pinjaman tidak aktif.');

        $angsuranKe = $pinjaman->pembayaran->count() + 1;

        return view('karyawan.pembayaran.create', compact('pinjaman', 'angsuranKe'));
    }

    public function store(Request $request, Pinjaman $pinjaman)
    {
        abort_if($pinjaman->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'tanggal_bayar'    => ['required', 'date'],
            'jenis_pembayaran' => ['required', 'in:bayar_lunas,bayar_bunga_saja,tidak_bayar,cicilan_normal'],
            'jumlah_dibayar'   => ['required', 'numeric', 'min:0'],
            'keterangan'       => ['nullable', 'string', 'max:255'],
        ]);

        $angsuranKe = $pinjaman->pembayaran->count() + 1;
        $tanggalBayar = Carbon::parse($validated['tanggal_bayar']);

        $jatuhTempoCicilan = Carbon::parse($pinjaman->tanggal_mulai)
                                   ->addMonths($angsuranKe);

        $hariTerlambat = 0;
        $denda = 0;
        if ($tanggalBayar->greaterThan($jatuhTempoCicilan)) {
            $hariTerlambat = $tanggalBayar->diffInDays($jatuhTempoCicilan);
            $denda = $pinjaman->cicilan_per_bulan * 0.001 * $hariTerlambat;
        }

        $bungaPerBulan = $pinjaman->total_bunga / $pinjaman->tenor_bulan;
        $pokokPerBulan = $pinjaman->jumlah_pinjaman / $pinjaman->tenor_bulan;

        $statusBayar = match(true) {
            $validated['jenis_pembayaran'] === 'bayar_lunas'                    => 'lunas',
            $validated['jenis_pembayaran'] === 'tidak_bayar'                    => 'tidak_bayar',
            $validated['jumlah_dibayar'] >= floor($pinjaman->cicilan_per_bulan) => 'lunas',
            default                                                              => 'sebagian',
        };

        $pembayaran = Pembayaran::create([
            'no_pembayaran'               => Pembayaran::generateNoPembayaran(),
            'pinjaman_id'                 => $pinjaman->id,
            'user_id'                     => auth()->id(),
            'angsuran_ke'                 => $angsuranKe,
            'tanggal_jatuh_tempo_cicilan' => $jatuhTempoCicilan,
            'tanggal_bayar'               => $tanggalBayar,
            'jumlah_cicilan'              => $pinjaman->cicilan_per_bulan,
            'jumlah_dibayar'              => $validated['jumlah_dibayar'],
            'pokok_dibayar'               => $pokokPerBulan,
            'bunga_dibayar'               => $bungaPerBulan,
            'denda'                       => $denda,
            'hari_terlambat'              => $hariTerlambat,
            'jenis_pembayaran'            => $validated['jenis_pembayaran'],
            'status'                      => $statusBayar,
            'keterangan'                  => $validated['keterangan'],
        ]);

        if ($angsuranKe >= $pinjaman->tenor_bulan || $validated['jenis_pembayaran'] === 'bayar_lunas') {
            $pinjaman->update(['status' => 'lunas']);
        }

        return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                         ->with('success', 'Pembayaran angsuran ke-'.$angsuranKe.' berhasil dicatat.')
                         ->with('invoice_id', $pembayaran->id); // opsional: auto-redirect ke invoice
    }

    /**
     * Invoice / kwitansi pembayaran.
     */
    public function invoice(Pinjaman $pinjaman, Pembayaran $pembayaran)
    {
        abort_if($pinjaman->user_id !== auth()->id(), 403);
        abort_if($pembayaran->pinjaman_id !== $pinjaman->id, 404);

        $pinjaman->load(['nasabah', 'karyawan']);

        $totalBayarSebelumnya = Pembayaran::where('pinjaman_id', $pinjaman->id)
            ->where('id', '<', $pembayaran->id)
            ->sum('jumlah_dibayar');

        $totalBayarSampaiIni = $totalBayarSebelumnya + $pembayaran->jumlah_dibayar;
        $sisaSetelahBayar    = max(0, $pinjaman->total_pinjaman - $totalBayarSampaiIni);

        return view('karyawan.pembayaran.invoice', compact(
            'pinjaman',
            'pembayaran',
            'totalBayarSebelumnya',
            'totalBayarSampaiIni',
            'sisaSetelahBayar',
        ));
    }
}