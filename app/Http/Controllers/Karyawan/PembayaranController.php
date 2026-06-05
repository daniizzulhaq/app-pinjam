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
        if ((int) $pinjaman->user_id !== (int) auth()->id()) {
            return redirect()->route('karyawan.pinjaman.index')
                ->with('error', 'Anda tidak memiliki akses ke pinjaman ini.');
        }

        if ($pinjaman->status !== 'aktif') {
            return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                ->with('error', 'Pembayaran hanya bisa diinput untuk pinjaman berstatus aktif. Status saat ini: ' . $pinjaman->status);
        }

        // Untuk harian: tidak ada batas angsuran ke-N, bebas bayar bunga kapan saja
        if ($pinjaman->tenor_tipe !== 'harian') {
            $angsuranKe = $pinjaman->pembayaran->count() + 1;
            if ($angsuranKe > $pinjaman->tenor_bulan) {
                return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                    ->with('error', 'Semua angsuran sudah lunas.');
            }
        } else {
            $angsuranKe = $pinjaman->pembayaran->count() + 1;
        }

        return view('karyawan.pembayaran.create', compact('pinjaman', 'angsuranKe'));
    }

    public function store(Request $request, Pinjaman $pinjaman)
    {
        if ((int) $pinjaman->user_id !== (int) auth()->id()) {
            return redirect()->route('karyawan.pinjaman.index')
                ->with('error', 'Anda tidak memiliki akses ke pinjaman ini.');
        }

        if ($pinjaman->status !== 'aktif') {
            return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                ->with('error', 'Pinjaman tidak aktif, pembayaran tidak dapat diproses.');
        }

        // Validasi jenis pembayaran berdasarkan tipe tenor
        $jenisAllowed = $pinjaman->tenor_tipe === 'harian'
            ? 'in:bayar_lunas,bayar_bunga_saja,tidak_bayar'
            : 'in:bayar_lunas,bayar_bunga_saja,tidak_bayar,cicilan_normal';

        $validated = $request->validate([
            'tanggal_bayar'    => ['required', 'date'],
            'jenis_pembayaran' => ['required', $jenisAllowed],
            'jumlah_dibayar'   => ['required', 'numeric', 'min:0'],
            'keterangan'       => ['nullable', 'string', 'max:255'],
            'bukti_pembayaran' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'bukti_pembayaran.image' => 'File bukti harus berupa gambar.',
            'bukti_pembayaran.max'   => 'Ukuran bukti maksimal 3 MB.',
        ]);

        $angsuranKe   = $pinjaman->pembayaran->count() + 1;
        $tanggalBayar = Carbon::parse($validated['tanggal_bayar']);

        // Jatuh tempo cicilan — harian pakai tanggal_jatuh_tempo aktif, bulanan pakai addMonths
        if ($pinjaman->tenor_tipe === 'harian') {
            $jatuhTempoCicilan = Carbon::parse($pinjaman->tanggal_jatuh_tempo);
        } else {
            $jatuhTempoCicilan = Carbon::parse($pinjaman->tanggal_mulai)->addMonths($angsuranKe);
        }

        // Hitung denda keterlambatan
        $hariTerlambat = 0;
        $denda         = 0;
        if ($tanggalBayar->greaterThan($jatuhTempoCicilan)) {
            $hariTerlambat = $tanggalBayar->diffInDays($jatuhTempoCicilan);
            $denda         = $pinjaman->cicilan_per_bulan * 0.001 * $hariTerlambat;
        }

        // Hitung bunga & pokok per periode
        if ($pinjaman->tenor_tipe === 'harian') {
            // Harian: bunga flat 1 bulan penuh, pokok = jumlah pinjaman
            $bungaPerPeriode = $pinjaman->total_bunga;
            $pokokPerPeriode = $pinjaman->jumlah_pinjaman;
        } else {
            $bungaPerPeriode = $pinjaman->total_bunga / $pinjaman->tenor_bulan;
            $pokokPerPeriode = $pinjaman->jumlah_pinjaman / $pinjaman->tenor_bulan;
        }

        // Status bayar
        if ($pinjaman->tenor_tipe === 'harian') {
            $statusBayar = match($validated['jenis_pembayaran']) {
                'bayar_lunas'      => 'lunas',
                'tidak_bayar'      => 'tidak_bayar',
                'bayar_bunga_saja' => 'bunga',
                default            => 'sebagian',
            };
        } else {
            $statusBayar = match(true) {
                $validated['jenis_pembayaran'] === 'bayar_lunas'                    => 'lunas',
                $validated['jenis_pembayaran'] === 'tidak_bayar'                    => 'tidak_bayar',
                $validated['jumlah_dibayar'] >= floor($pinjaman->cicilan_per_bulan) => 'lunas',
                default                                                              => 'sebagian',
            };
        }

        // Upload bukti
        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')
                ->store('bukti_pembayaran', 'public');
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
            'pokok_dibayar'               => $pinjaman->tenor_tipe === 'harian' && $validated['jenis_pembayaran'] === 'bayar_lunas'
                                                ? $pokokPerPeriode
                                                : ($pinjaman->tenor_tipe === 'harian' ? 0 : $pokokPerPeriode),
            'bunga_dibayar'               => in_array($validated['jenis_pembayaran'], ['bayar_bunga_saja', 'bayar_lunas'])
                                                ? $bungaPerPeriode
                                                : ($pinjaman->tenor_tipe === 'harian' ? 0 : $bungaPerPeriode),
            'denda'                       => $denda,
            'hari_terlambat'              => $hariTerlambat,
            'jenis_pembayaran'            => $validated['jenis_pembayaran'],
            'status'                      => $statusBayar,
            'keterangan'                  => $validated['keterangan'],
            'bukti_pembayaran'            => $buktiPath,
        ]);

        // Update status pinjaman & jatuh tempo
        if ($pinjaman->tenor_tipe === 'harian') {
            if ($validated['jenis_pembayaran'] === 'bayar_lunas') {
                // Lunas: tutup pinjaman
                $pinjaman->update(['status' => 'lunas']);
            } elseif ($validated['jenis_pembayaran'] === 'bayar_bunga_saja') {
                // Bayar bunga: mundurkan jatuh tempo sesuai tenor (hari)
                $jatuhTempoMundur = Carbon::parse($pinjaman->tanggal_jatuh_tempo)
                    ->addDays($pinjaman->tenor_bulan);
                $pinjaman->update(['tanggal_jatuh_tempo' => $jatuhTempoMundur]);
            }
            // tidak_bayar: tidak ada perubahan
        } else {
            // Bulanan: lunas jika angsuran sudah habis atau bayar lunas
            if ($angsuranKe >= $pinjaman->tenor_bulan || $validated['jenis_pembayaran'] === 'bayar_lunas') {
                $pinjaman->update(['status' => 'lunas']);
            }
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

        abort_if((int) $pembayaran->pinjaman_id !== (int) $pinjaman->id, 404);

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