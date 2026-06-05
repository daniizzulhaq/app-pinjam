<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\{Pinjaman, Pembayaran};
use Carbon\Carbon;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /**
     * Hitung denda otomatis:
     * Rp 50.000/hari, mulai dihitung setelah jam 17:00 di hari jatuh tempo.
     */
    private function hitungDenda(Pinjaman $pinjaman): array
    {
        $jatuhTempo = Carbon::parse($pinjaman->tanggal_jatuh_tempo);
        $sekarang   = Carbon::now();

        // Denda mulai berlaku setelah jam 17:00 hari jatuh tempo
        $batasWaktu = $jatuhTempo->copy()->setTime(17, 0, 0);

        if ($sekarang->lessThanOrEqualTo($batasWaktu)) {
            return ['hari' => 0, 'denda' => 0];
        }

        // Hitung hari keterlambatan (dari hari jatuh tempo ke hari ini)
        $hariTerlambat = (int) $jatuhTempo->startOfDay()->diffInDays($sekarang->startOfDay());

        // Hari jatuh tempo sendiri dihitung jika sudah lewat jam 17:00
        if ($sekarang->greaterThan($batasWaktu) && $hariTerlambat === 0) {
            $hariTerlambat = 1;
        }

        return [
            'hari'  => $hariTerlambat,
            'denda' => $hariTerlambat * 50000,
        ];
    }

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

        if ($pinjaman->tenor_tipe !== 'harian') {
            $angsuranKe = $pinjaman->pembayaran->count() + 1;
            if ($angsuranKe > $pinjaman->tenor_bulan) {
                return redirect()->route('karyawan.pinjaman.show', $pinjaman)
                    ->with('error', 'Semua angsuran sudah lunas.');
            }
        } else {
            $angsuranKe = $pinjaman->pembayaran->count() + 1;
        }

        $infoDenda = $this->hitungDenda($pinjaman);

        return view('karyawan.pembayaran.create', compact('pinjaman', 'angsuranKe', 'infoDenda'));
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

        $jenisAllowed = $pinjaman->tenor_tipe === 'harian'
            ? 'in:bayar_lunas,bayar_bunga_saja,tidak_bayar'
            : 'in:bayar_lunas,bayar_bunga_saja,tidak_bayar,cicilan_normal';

        $validated = $request->validate([
            'tanggal_bayar'    => ['required', 'date'],
            'jenis_pembayaran' => ['required', $jenisAllowed],
            'jumlah_dibayar'   => ['required', 'numeric', 'min:0'],
            'keterangan'       => ['nullable', 'string', 'max:255'],
            'bukti_pembayaran' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'denda_diwaive'    => ['nullable', 'boolean'],
            'alasan_waive'     => ['nullable', 'string', 'max:255'],
        ], [
            'bukti_pembayaran.image' => 'File bukti harus berupa gambar.',
            'bukti_pembayaran.max'   => 'Ukuran bukti maksimal 3 MB.',
        ]);

        $angsuranKe   = $pinjaman->pembayaran->count() + 1;
        $tanggalBayar = Carbon::parse($validated['tanggal_bayar']);

        if ($pinjaman->tenor_tipe === 'harian') {
            $jatuhTempoCicilan = Carbon::parse($pinjaman->tanggal_jatuh_tempo);
        } else {
            $jatuhTempoCicilan = Carbon::parse($pinjaman->tanggal_mulai)->addMonths($angsuranKe);
        }

        // Hitung denda otomatis
        $infoDenda     = $this->hitungDenda($pinjaman);
        $hariTerlambat = $infoDenda['hari'];
        $dendaAsli     = $infoDenda['denda'];

        // Waive denda jika dicentang
        $dendaDiwaive = (bool) ($validated['denda_diwaive'] ?? false);
        $denda        = $dendaDiwaive ? 0 : $dendaAsli;
        $alasanWaive  = $dendaDiwaive ? ($validated['alasan_waive'] ?? null) : null;

        // Hitung bunga & pokok per periode
        if ($pinjaman->tenor_tipe === 'harian') {
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
            'denda_diwaive'               => $dendaDiwaive,
            'alasan_waive'                => $alasanWaive,
            'hari_terlambat'              => $hariTerlambat,
            'jenis_pembayaran'            => $validated['jenis_pembayaran'],
            'status'                      => $statusBayar,
            'keterangan'                  => $validated['keterangan'],
            'bukti_pembayaran'            => $buktiPath,
        ]);

        // Update status & jatuh tempo pinjaman
        if ($pinjaman->tenor_tipe === 'harian') {
            if ($validated['jenis_pembayaran'] === 'bayar_lunas') {
                $pinjaman->update(['status' => 'lunas']);
            } elseif ($validated['jenis_pembayaran'] === 'bayar_bunga_saja') {
                $jatuhTempoMundur = Carbon::parse($pinjaman->tanggal_jatuh_tempo)
                    ->addDays($pinjaman->tenor_bulan);
                $pinjaman->update(['tanggal_jatuh_tempo' => $jatuhTempoMundur]);
            }
        } else {
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