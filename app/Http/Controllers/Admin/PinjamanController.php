<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

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

    public function approval(Request $request, Pinjaman $pinjaman)
    {
        $validated = $request->validate([
            'action'           => ['required', 'in:disetujui,ditolak'],
            'catatan_approval' => ['nullable', 'string', 'max:500'],
        ]);

        if ($pinjaman->status !== 'menunggu_approval') {
            return back()->with('error', 'Pinjaman sudah diproses sebelumnya.');
        }

        $updateData = [
            'status'           => $validated['action'],
            'catatan_approval' => $validated['catatan_approval'],
            'tanggal_approval' => now(),
            'approved_by'      => auth()->id(),
        ];

        if ($validated['action'] === 'disetujui') {
            $tipe = $pinjaman->tenor_tipe ?? 'bulanan';

            // Hitung tanggal jatuh tempo sesuai tipe tenor
            $tanggalJatuhTempo = $tipe === 'harian'
                ? now()->addDays($pinjaman->tenor_bulan)
                : now()->addMonths($pinjaman->tenor_bulan);

            $updateData['status']              = 'aktif';
            $updateData['tanggal_mulai']       = now();
            $updateData['tanggal_jatuh_tempo'] = $tanggalJatuhTempo;
        }

        $pinjaman->update($updateData);

        $msg = $validated['action'] === 'disetujui' ? 'Pinjaman disetujui.' : 'Pinjaman ditolak.';
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
}