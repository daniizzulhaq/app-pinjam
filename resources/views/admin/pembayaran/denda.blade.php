@extends('layouts.admin')

@section('title', 'Data Denda')
@section('page-title', 'Data Denda Pembayaran')

@section('content')
<div class="py-4">

    {{-- BACK --}}
    <div class="mb-4">
        <a href="{{ route('admin.pembayaran.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke semua pembayaran
        </a>
    </div>

    {{-- SUMMARY CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase mb-1">Total Transaksi Denda</p>
            <p class="text-2xl font-bold text-gray-800">{{ $pembayaran->total() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase mb-1">Total Nominal Denda</p>
            <p class="text-2xl font-bold text-red-600">
                Rp {{ number_format($pembayaran->sum('denda'), 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase mb-1">Rata-rata Denda</p>
            <p class="text-2xl font-bold text-orange-500">
                Rp {{ $pembayaran->total() > 0 ? number_format($pembayaran->sum('denda') / $pembayaran->total(), 0, ',', '.') : 0 }}
            </p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            <h3 class="text-sm font-semibold text-gray-800">Riwayat Pembayaran dengan Denda</h3>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Nasabah</th>
                    <th class="px-4 py-3 text-left">No. Pinjaman</th>
                    <th class="px-4 py-3 text-left">Tgl Bayar</th>
                    <th class="px-4 py-3 text-right">Jumlah Bayar</th>
                    <th class="px-4 py-3 text-right">Denda</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pembayaran as $item)
                <tr class="hover:bg-red-50 transition">
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ $loop->iteration + ($pembayaran->currentPage() - 1) * $pembayaran->perPage() }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">
                            {{ $item->pinjaman->nasabah->nama_lengkap ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $item->pinjaman->nasabah->no_telepon ?? '' }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.pinjaman.show', $item->pinjaman) }}"
                           class="font-mono text-xs text-blue-600 hover:underline">
                            {{ $item->pinjaman->no_pinjaman ?? '-' }}
                        </a>
                        <div class="text-xs text-gray-400 mt-0.5">
                            Tenor {{ $item->pinjaman->tenor_bulan ?? '-' }} bln
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-right text-gray-700">
                        Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="text-red-600 font-semibold">
                            Rp {{ number_format($item->denda, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-gray-800">
                        Rp {{ number_format($item->jumlah_bayar + $item->denda, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <i class="fa fa-check-circle text-4xl mb-2 block text-green-400"></i>
                        Tidak ada pembayaran dengan denda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-500">
            @if($pembayaran->total() > 0)
            <span>Menampilkan {{ $pembayaran->firstItem() }}–{{ $pembayaran->lastItem() }} dari {{ $pembayaran->total() }} data</span>
            @endif
            {{ $pembayaran->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection