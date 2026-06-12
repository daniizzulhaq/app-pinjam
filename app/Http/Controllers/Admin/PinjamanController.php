<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $pinjaman = Pinjaman::with(['nasabah', 'karyawan', 'bunga', 'tenor'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) =>
                $q->whereHas('nasabah', fn($n) =>
                    $n->where('nama_lengkap', 'like', '%'.$request->search.'%')
                )
            )
            ->latest()
            ->paginate(15);

        return view('admin.pinjaman.index', compact('pinjaman'));
    }

    public function show(Pinjaman $pinjaman)
    {
        $pinjaman->load(['nasabah', 'karyawan', 'pembayaran', 'bunga', 'tenor']);
        return view('admin.pinjaman.show', compact('pinjaman'));
    }

    /**
     * STEP 1 — Admin upload bukti transfer ke karyawan
     * Status: menunggu_approval → menunggu_transfer_karyawan
     */
    public function uploadBuktiAdmin(Request $request, Pinjaman $pinjaman)
    {
        if ($pinjaman->status !== 'menunggu_approval') {
            return back()->with('error', 'Pinjaman tidak dalam status menunggu approval.');
        }

        $request->validate([
            'bukti_transfer_admin' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $path = $request->file('bukti_transfer_admin')
            ->store('bukti-transfer/admin', 'public');

        $pinjaman->update([
            'bukti_transfer_admin' => $path,
            'tgl_transfer_admin'   => now(),
            'status'               => 'menunggu_transfer_karyawan',
        ]);

        return back()->with('success', 'Bukti transfer berhasil dikirim ke karyawan.');
    }

    /**
     * STEP 3 — Admin approve setelah karyawan upload bukti transfer ke nasabah
     * Status: menunggu_konfirmasi → aktif / ditolak
     */
    public function approval(Request $request, Pinjaman $pinjaman)
    {
        $validated = $request->validate([
            'action'           => ['required', 'in:disetujui,ditolak'],
            'catatan_approval' => ['nullable', 'string', 'max:500'],
        ]);

        if ($pinjaman->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Pinjaman belum siap untuk di-approve. Tunggu karyawan upload bukti transfer ke nasabah.');
        }

        $updateData = [
            'catatan_approval' => $validated['catatan_approval'],
            'tanggal_approval' => now(),
            'approved_by'      => auth()->id(),
        ];

        if ($validated['action'] === 'disetujui') {
            $tipe         = $pinjaman->tenor_tipe ?? 'bulanan';
            $tanggalMulai = $pinjaman->tanggal_pengajuan;

            // Harian: hari pertama sudah terhitung, jadi -1
            // Contoh: pinjam tgl 1, tenor 10 hari → jatuh tempo tgl 10
            $tanggalJatuhTempo = $tipe === 'harian'
                ? $tanggalMulai->copy()->addDays($pinjaman->tenor_bulan - 1)
                : $tanggalMulai->copy()->addMonths($pinjaman->tenor_bulan);

            $updateData['status']              = 'aktif';
            $updateData['tanggal_mulai']       = $tanggalMulai;
            $updateData['tanggal_jatuh_tempo'] = $tanggalJatuhTempo;
        } else {
            $updateData['status'] = 'ditolak';
        }

        $pinjaman->update($updateData);

        $msg = $validated['action'] === 'disetujui' ? 'Pinjaman disetujui dan aktif.' : 'Pinjaman ditolak.';
        return redirect()->route('admin.pinjaman.index')->with('success', $msg);
    }

    public function jatuhTempo()
    {
        $pinjaman = Pinjaman::with(['nasabah', 'karyawan'])
            ->aktif()
            ->whereDate('tanggal_jatuh_tempo', '<=', now()->addDays(30))
            ->orderBy('tanggal_jatuh_tempo')
            ->paginate(15);

        return view('admin.pinjaman.jatuh-tempo', compact('pinjaman'));
    }

    public function destroy(Pinjaman $pinjaman)
    {
        if (!in_array($pinjaman->status, ['menunggu_approval', 'ditolak'])) {
            return back()->with('error', 'Pinjaman aktif/lunas tidak bisa dihapus.');
        }

        if ($pinjaman->bukti_transfer_admin) {
            Storage::disk('public')->delete($pinjaman->bukti_transfer_admin);
        }

        $pinjaman->delete();

        return back()->with('success', 'Data pinjaman berhasil dihapus.');
    }
}