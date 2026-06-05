{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/pinjaman/jatuh-tempo.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.admin')
@section('title', 'Monitoring Jatuh Tempo')
@section('page-title', 'Monitoring Jatuh Tempo Pinjaman')

@section('content')
<div class="py-4">

    {{-- INFO BANNER --}}
    <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl mb-4 text-sm">
        <i class="fa fa-exclamation-triangle text-amber-500"></i>
        Menampilkan pinjaman aktif yang jatuh tempo dalam <strong>30 hari ke depan</strong>.
        Mencakup tenor <strong>harian</strong> maupun <strong>bulanan</strong>.
    </div>

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Nasabah</th>
                    <th class="px-4 py-3 text-left">Marketing</th>
                    <th class="px-4 py-3 text-center">Tenor</th>
                    <th class="px-4 py-3 text-right">Pokok Pinjaman</th>
                    <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-center">Sisa Hari</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pinjaman as $item)
                @php
                    $tipe     = $item->tenor_tipe ?? 'bulanan';
                    $satuan   = $tipe === 'harian' ? 'hr' : 'bln';
                    $sisaHari = now()->startOfDay()->diffInDays(
                        \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay(), false
                    );
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $item->nasabah->nama_lengkap }}</div>
                        <div class="text-xs text-gray-400">{{ $item->nasabah->no_telepon }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->karyawan->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-gray-700 text-xs">{{ $item->tenor_bulan }} {{ $satuan }}</span>
                        @if($tipe === 'harian')
                            <span class="block mt-0.5">
                                <span class="bg-orange-100 text-orange-700 text-xs px-1.5 py-0.5 rounded-full">
                                    <i class="fa fa-sun-o mr-0.5"></i>Harian
                                </span>
                            </span>
                        @else
                            <span class="block mt-0.5">
                                <span class="bg-blue-100 text-blue-700 text-xs px-1.5 py-0.5 rounded-full">
                                    <i class="fa fa-calendar mr-0.5"></i>Bulanan
                                </span>
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-gray-700">
                        Rp {{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($sisaHari < 0)
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">
                                Terlambat {{ abs($sisaHari) }} hari
                            </span>
                        @elseif($sisaHari <= 7)
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">
                                {{ $sisaHari }} hari lagi
                            </span>
                        @elseif($sisaHari <= 14)
                            <span class="bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded-full font-medium">
                                {{ $sisaHari }} hari lagi
                            </span>
                        @else
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-medium">
                                {{ $sisaHari }} hari lagi
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.pinjaman.show', $item) }}"
                           class="bg-sky-500 hover:bg-sky-600 text-white text-xs px-3 py-1 rounded transition">
                            <i class="fa fa-eye mr-1"></i>Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <i class="fa fa-check-circle text-4xl mb-2 block text-green-400"></i>
                        Tidak ada pinjaman yang jatuh tempo dalam 30 hari ke depan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-500">
            @if($pinjaman->total() > 0)
                <span>Menampilkan {{ $pinjaman->firstItem() }}–{{ $pinjaman->lastItem() }} dari {{ $pinjaman->total() }} pinjaman</span>
            @endif
            {{ $pinjaman->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection