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
        // Hanya karyawan yang menginput pinjaman ini yang boleh akses
        if ((int) $pinjaman->user_id !== (int) auth()->id()) {
            return redirect()->route('karyawan.pinjaman.index')
                ->with('error', 'Anda tidak memiliki akses ke pinjaman ini.');
        }

        // Pinjaman harus berstatus aktif
        if ($pinjaman->status !== 'aktif') {
            return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                ->with('error', 'Pembayaran hanya bisa diinput untuk pinjaman berstatus aktif. Status saat ini: ' . $pinjaman->status);
        }

        // Cek angsuran sudah melebihi tenor
        $angsuranKe = $pinjaman->pembayaran->count() + 1;
        if ($angsuranKe > $pinjaman->tenor_bulan) {
            return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                ->with('error', 'Semua angsuran sudah lunas.');
        }

        return view('karyawan.pembayaran.create', compact('pinjaman', 'angsuranKe'));
    }

    public function store(Request $request, Pinjaman $pinjaman)
    {
        // Hanya karyawan yang menginput pinjaman ini yang boleh store
        if ((int) $pinjaman->user_id !== (int) auth()->id()) {
            return redirect()->route('karyawan.pinjaman.index')
                ->with('error', 'Anda tidak memiliki akses ke pinjaman ini.');
        }

        if ($pinjaman->status !== 'aktif') {
            return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                ->with('error', 'Pinjaman tidak aktif, pembayaran tidak dapat diproses.');
        }

        $validated = $request->validate([
            'tanggal_bayar'    => ['required', 'date'],
            'jenis_pembayaran' => ['required', 'in:bayar_lunas,bayar_bunga_saja,tidak_bayar,cicilan_normal'],
            'jumlah_dibayar'   => ['required', 'numeric', 'min:0'],
            'keterangan'       => ['nullable', 'string', 'max:255'],
            'bukti_pembayaran' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'bukti_pembayaran.image' => 'File bukti harus berupa gambar.',
            'bukti_pembayaran.max'   => 'Ukuran bukti maksimal 3 MB.',
        ]);

        $angsuranKe        = $pinjaman->pembayaran->count() + 1;
        $tanggalBayar      = Carbon::parse($validated['tanggal_bayar']);
        $jatuhTempoCicilan = Carbon::parse($pinjaman->tanggal_mulai)->addMonths($angsuranKe);

        $hariTerlambat = 0;
        $denda         = 0;
        if ($tanggalBayar->greaterThan($jatuhTempoCicilan)) {
            $hariTerlambat = $tanggalBayar->diffInDays($jatuhTempoCicilan);
            $denda         = $pinjaman->cicilan_per_bulan * 0.001 * $hariTerlambat;
        }

        $bungaPerBulan = $pinjaman->total_bunga / $pinjaman->tenor_bulan;
        $pokokPerBulan = $pinjaman->jumlah_pinjaman / $pinjaman->tenor_bulan;

        $statusBayar = match(true) {
            $validated['jenis_pembayaran'] === 'bayar_lunas'                    => 'lunas',
            $validated['jenis_pembayaran'] === 'tidak_bayar'                    => 'tidak_bayar',
            $validated['jumlah_dibayar'] >= floor($pinjaman->cicilan_per_bulan) => 'lunas',
            default                                                              => 'sebagian',
        };

        // -------------------------------------------------------
        // Upload bukti — simpan ke public_html/storage/bukti_pembayaran/
        // konsisten dengan foto nasabah di public_html/storage/nasabah/
        // -------------------------------------------------------
        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file     = $request->file('bukti_pembayaran');
            $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $tujuan   = public_path('storage/bukti_pembayaran');

            if (!file_exists($tujuan)) {
                mkdir($tujuan, 0755, true);
            }

            $file->move($tujuan, $namaFile);
            $buktiPath = 'bukti_pembayaran/' . $namaFile;
        }

        Pembayaran::create([
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
            'bukti_pembayaran'            => $buktiPath,
        ]);

        if ($angsuranKe >= $pinjaman->tenor_bulan || $validated['jenis_pembayaran'] === 'bayar_lunas') {
            $pinjaman->update(['status' => 'lunas']);
        }

        return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                         ->with('success', 'Pembayaran angsuran ke-' . $angsuranKe . ' berhasil dicatat.');
    }

    public function invoice(Pinjaman $pinjaman, Pembayaran $pembayaran)
    {
        if ((int) $pinjaman->user_id !== (int) auth()->id()) {
            return redirect()->route('karyawan.pinjaman.index')
                ->with('error', 'Anda tidak memiliki akses ke pinjaman ini.');
        }

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