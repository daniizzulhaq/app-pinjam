@extends('layouts.admin')

@section('title', 'Laporan Pinjaman')
@section('page-title', 'Laporan Pinjaman')

@section('content')
<div class="py-4">

    {{-- FILTER --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                <select name="bulan"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $b)
                        <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                <select name="tahun"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), date('Y') - 5) as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Semua Status</option>
                    <option value="menunggu_approval" {{ request('status') == 'menunggu_approval' ? 'selected' : '' }}>Menunggu Approval</option>
                    <option value="aktif"             {{ request('status') == 'aktif'             ? 'selected' : '' }}>Aktif</option>
                    <option value="lunas"             {{ request('status') == 'lunas'             ? 'selected' : '' }}>Lunas</option>
                    <option value="ditolak"           {{ request('status') == 'ditolak'           ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fa fa-filter mr-1"></i> Tampilkan
                </button>
                @if(request()->hasAny(['bulan','tahun','status']))
                <a href="{{ route('admin.laporan.pinjaman') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fa fa-times"></i> Reset
                </a>
                @endif
            </div>

            {{-- TOMBOL EXPORT --}}
            <div class="flex gap-2 ml-auto">
                <a href="{{ route('admin.laporan.pinjaman.pdf', request()->query()) }}"
                   target="_blank"
                   class="flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fa fa-file-pdf"></i> Cetak PDF
                </a>
                <a href="{{ route('admin.laporan.pinjaman.excel', request()->query()) }}"
                   class="flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fa fa-file-excel"></i> Export Excel
                </a>
            </div>
        </form>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase mb-1">Total Pinjaman</p>
            <p class="text-2xl font-bold text-gray-800">{{ $pinjaman->total() }}</p>
            <p class="text-xs text-gray-400 mt-1">transaksi ditemukan</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase mb-1">Total Pokok</p>
            <p class="text-2xl font-bold text-blue-600">
                Rp {{ number_format($totalPokok, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase mb-1">Total Bunga</p>
            <p class="text-2xl font-bold text-emerald-600">
                Rp {{ number_format($totalBunga, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">No. Pinjaman</th>
                    <th class="px-4 py-3 text-left">Nasabah</th>
                    <th class="px-4 py-3 text-left">Karyawan</th>
                    <th class="px-4 py-3 text-left">Tgl Pengajuan</th>
                    <th class="px-4 py-3 text-right">Pokok</th>
                    <th class="px-4 py-3 text-right">Bunga</th>
                    <th class="px-4 py-3 text-center">Tenor</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pinjaman as $item)
                @php
                    $badge = match($item->status) {
                        'menunggu_approval' => 'bg-yellow-100 text-yellow-700',
                        'aktif'             => 'bg-green-100 text-green-700',
                        'lunas'             => 'bg-blue-100 text-blue-700',
                        'ditolak'           => 'bg-red-100 text-red-700',
                        default             => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ $loop->iteration + ($pinjaman->currentPage() - 1) * $pinjaman->perPage() }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs text-blue-600">{{ $item->no_pinjaman }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $item->nasabah->nama_lengkap ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $item->nasabah->no_ktp ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->karyawan->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                        Rp {{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right text-emerald-700">
                        Rp {{ number_format($item->total_bunga, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $item->tenor_bulan }} bln</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $badge }}">
                            {{ ucwords(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-12 text-gray-400">
                        <i class="fa fa-file-invoice text-4xl mb-2 block"></i>
                        Tidak ada data pinjaman untuk filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-500">
            @if($pinjaman->total() > 0)
            <span>Menampilkan {{ $pinjaman->firstItem() }}–{{ $pinjaman->lastItem() }} dari {{ $pinjaman->total() }} data</span>
            @endif
            {{ $pinjaman->links() }}
        </div>
    </div>

</div>
@endsection