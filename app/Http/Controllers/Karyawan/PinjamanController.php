<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\{Pinjaman, Nasabah, BungaPinjaman, Tenor};
use Illuminate\Http\Request;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $pinjaman = Pinjaman::with(['nasabah', 'bunga', 'tenor'])
            ->where('user_id', auth()->id())
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('karyawan.pinjaman.index', compact('pinjaman'));
    }

    public function create(Request $request)
    {
        $nasabah = Nasabah::where('user_id', auth()->id())->get();
        $bunga   = BungaPinjaman::aktif()->get();
        $tenor   = Tenor::aktif()->get();

        $nasabahDipilih = $request->nasabah_id
            ? Nasabah::find($request->nasabah_id)
            : null;

        return view('karyawan.pinjaman.create', compact('nasabah', 'bunga', 'tenor', 'nasabahDipilih'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nasabah_id'        => ['required', 'exists:nasabah,id'],
            'bunga_id'          => ['required', 'exists:bunga_pinjaman,id'],
            'tenor_id'          => ['required', 'exists:tenor,id'],
            'jumlah_pinjaman'   => ['required', 'numeric', 'min:100000'],
            'catatan_pengajuan' => ['nullable', 'string', 'max:500'],
        ]);

        $bunga = BungaPinjaman::findOrFail($validated['bunga_id']);
        $tenor = Tenor::findOrFail($validated['tenor_id']);

        $jumlah  = $validated['jumlah_pinjaman'];
        $periode = $tenor->bulan;

        // Flat 1 bulan penuh, tidak prorate
        if ($tenor->tipe === 'harian') {
            $totalBunga = $jumlah * ($bunga->persentase / 100);
        } else {
            $totalBunga = $jumlah * ($bunga->persentase / 100) * $periode;
        }

        $totalPinjaman = $jumlah + $totalBunga;
        $cicilan       = $totalPinjaman / $periode;

        Pinjaman::create([
            'no_pinjaman'       => Pinjaman::generateNoPinjaman(),
            'nasabah_id'        => $validated['nasabah_id'],
            'user_id'           => auth()->id(),
            'bunga_id'          => $validated['bunga_id'],
            'tenor_id'          => $validated['tenor_id'],
            'jumlah_pinjaman'   => $jumlah,
            'bunga_persen'      => $bunga->persentase,
            'tenor_bulan'       => $periode,
            'tenor_tipe'        => $tenor->tipe,
            'cicilan_per_bulan' => $cicilan,
            'total_pinjaman'    => $totalPinjaman,
            'total_bunga'       => $totalBunga,
            'tanggal_pengajuan' => now(),
            'catatan_pengajuan' => $validated['catatan_pengajuan'],
            'status'            => 'menunggu_approval',
        ]);

        return redirect()->route('karyawan.pinjaman.index')
                         ->with('success', 'Pengajuan pinjaman berhasil dikirim, menunggu approval admin.');
    }

    public function show(Pinjaman $pinjaman)
    {
        if ((int) $pinjaman->user_id !== (int) auth()->id()) {
            return redirect()->route('karyawan.pinjaman.index')
                ->with('error', 'Anda tidak memiliki akses ke pinjaman ini.');
        }

        $pinjaman->load(['nasabah', 'pembayaran', 'bunga', 'tenor']);
        return view('karyawan.pinjaman.show', compact('pinjaman'));
    }
}