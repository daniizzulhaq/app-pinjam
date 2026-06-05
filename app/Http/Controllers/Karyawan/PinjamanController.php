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
            'tanggal_pengajuan' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $bunga = BungaPinjaman::findOrFail($validated['bunga_id']);
        $tenor = Tenor::findOrFail($validated['tenor_id']);

        $jumlah  = $validated['jumlah_pinjaman'];
        $periode = $tenor->bulan;

        if ($tenor->tipe === 'harian') {
            $totalBunga = $jumlah * ($bunga->persentase / 100);
        } else {
            $totalBunga = $jumlah * ($bunga->persentase / 100) * $periode;
        }

        $totalPinjaman    = $jumlah + $totalBunga;
        $cicilan          = $totalPinjaman / $periode;
        $tanggalPengajuan = $validated['tanggal_pengajuan']
            ? \Carbon\Carbon::parse($validated['tanggal_pengajuan'])
            : now();

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
            'tanggal_pengajuan' => $tanggalPengajuan,
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

    /**
     * STEP 2 — Karyawan upload bukti transfer ke nasabah
     * Status: menunggu_transfer_karyawan → menunggu_konfirmasi
     */
    public function uploadBuktiKaryawan(Request $request, Pinjaman $pinjaman)
    {
        if ((int) $pinjaman->user_id !== (int) auth()->id()) {
            return redirect()->route('karyawan.pinjaman.index')
                ->with('error', 'Anda tidak memiliki akses ke pinjaman ini.');
        }

        if ($pinjaman->status !== 'menunggu_transfer_karyawan') {
            return back()->with('error', 'Belum ada bukti transfer dari admin, atau status tidak sesuai.');
        }

        $request->validate([
            'bukti_transfer_karyawan' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $path = $request->file('bukti_transfer_karyawan')
            ->store('bukti-transfer/karyawan', 'public');

        $pinjaman->update([
            'bukti_transfer_karyawan' => $path,
            'tgl_transfer_karyawan'   => now(),
            'status'                  => 'menunggu_konfirmasi',
        ]);

        return back()->with('success', 'Bukti transfer ke nasabah berhasil diupload. Menunggu konfirmasi admin.');
    }
}