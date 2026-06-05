@extends('layouts.admin')

@section('title', 'Data Denda')
@section('page-title', 'Data Denda Pembayaran')

@section('content')
<div class="py-4 pb-8">

    {{-- BACK --}}
    <div class="mb-5">
        <a href="{{ route('admin.pembayaran.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke semua pembayaran
        </a>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-xl border border-red-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fa fa-receipt text-red-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Total Transaksi Denda</p>
                <p class="text-2xl font-bold text-red-600">{{ $totalTransaksi }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-orange-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <i class="fa fa-money-bill-wave text-orange-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Total Nominal Denda</p>
                <p class="text-xl font-bold text-orange-600">
                    Rp {{ number_format($totalDenda, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-yellow-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                <i class="fa fa-chart-line text-yellow-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Rata-rata Denda</p>
                <p class="text-xl font-bold text-yellow-600">
                    Rp {{ $totalTransaksi > 0 ? number_format($totalDenda / $totalTransaksi, 0, ',', '.') : 0 }}
                </p>
            </div>
        </div>

    </div>

    {{-- INFO BANNER --}}
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-6 flex items-start gap-3">
        <i class="fa fa-circle-info text-red-400 mt-0.5 flex-shrink-0"></i>
        <p class="text-sm text-red-700 leading-relaxed">
            Denda dihitung sejak pukul <strong>17:00</strong> pada tanggal jatuh tempo.
            Setiap pinjaman yang melewati batas waktu dikenakan denda
            <strong>Rp 50.000 per hari</strong> hingga pelunasan dilakukan.
        </p>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            <h3 class="text-sm font-semibold text-gray-800">Riwayat Pembayaran dengan Denda</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">#</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Nasabah</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">No. Pinjaman</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Tgl Bayar</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Jumlah Bayar</th>
                        <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Denda</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pembayaran as $item)
                    <tr class="hover:bg-gray-50/70 transition-colors">

                        {{-- Nomor --}}
                        <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">
                            {{ $loop->iteration + ($pembayaran->currentPage() - 1) * $pembayaran->perPage() }}
                        </td>

                        {{-- Nasabah --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center
                                            justify-center text-emerald-700 font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($item->pinjaman->nasabah->nama_lengkap ?? 'N', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 leading-tight">
                                        {{ $item->pinjaman->nasabah->nama_lengkap ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $item->pinjaman->nasabah->no_telepon ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- No. Pinjaman --}}
                        <td class="px-4 py-3.5">
                            <a href="{{ route('admin.pinjaman.show', $item->pinjaman) }}"
                               class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $item->pinjaman->no_pinjaman ?? '-' }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Tenor {{ $item->pinjaman->tenor_bulan ?? '-' }} bln
                            </p>
                        </td>

                        {{-- Tgl Bayar --}}
                        <td class="px-4 py-3.5 text-gray-600">
                            {{ \Carbon\Carbon::parse($item->tanggal_bayar)->translatedFormat('d M Y') }}
                        </td>

                        {{-- Jumlah Bayar --}}
                        <td class="px-4 py-3.5 text-right text-gray-700">
                            Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}
                        </td>

                        {{-- Denda --}}
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                         text-xs font-bold bg-red-100 text-red-700">
                                <i class="fa fa-triangle-exclamation text-xs"></i>
                                Rp {{ number_format($item->denda, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- Total --}}
                        <td class="px-4 py-3.5 text-right font-bold text-gray-800">
                            Rp {{ number_format($item->jumlah_dibayar + $item->denda, 0, ',', '.') }}
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa fa-circle-check text-emerald-500 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada pembayaran dengan denda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer / Pagination --}}
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
            @if($totalTransaksi > 0)
                <span class="text-xs text-gray-400">
                    Menampilkan {{ $pembayaran->firstItem() }}–{{ $pembayaran->lastItem() }}
                    dari {{ $totalTransaksi }} data
                </span>
            @else
                <span></span>
            @endif
            {{ $pembayaran->withQueryString()->links() }}
        </div>

    </div>

</div>
@endsection