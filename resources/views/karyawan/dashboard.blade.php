@extends('layouts.karyawan')

@section('title', 'Dashboard Karyawan')
@section('page-title', 'Dashboard')

@section('content')
<div class="py-4">

    {{-- Welcome --}}
    <div class="bg-emerald-600 text-white rounded-xl p-5 mb-6">
        <h3 class="text-lg font-bold">Halo, {{ auth()->user()->name }}! 👋</h3>
        <p class="text-emerald-200 text-sm mt-1">Berikut ringkasan aktivitas nasabah Anda hari ini.</p>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-blue-100 text-blue-600 rounded-full p-3">
                <i class="fa fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Nasabah Saya</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($data['total_nasabah']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-green-100 text-green-600 rounded-full p-3">
                <i class="fa fa-file-invoice-dollar text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pinjaman Aktif</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($data['pinjaman_aktif']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3">
                <i class="fa fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Menunggu Approval</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($data['pinjaman_menunggu']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-red-100 text-red-600 rounded-full p-3">
                <i class="fa fa-exclamation-triangle text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Jatuh Tempo</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($data['pinjaman_jatuh_tempo']) }}</p>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">⚡ Aksi Cepat</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('karyawan.nasabah.create') }}"
               class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm px-4 py-2 rounded-lg font-medium">
                <i class="fa fa-user-plus mr-1"></i> Tambah Nasabah
            </a>
            <a href="{{ route('karyawan.pinjaman.create') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded-lg font-medium">
                <i class="fa fa-plus-circle mr-1"></i> Ajukan Pinjaman
            </a>
            <a href="{{ route('karyawan.pinjaman.index', ['status' => 'aktif']) }}"
               class="bg-purple-500 hover:bg-purple-600 text-white text-sm px-4 py-2 rounded-lg font-medium">
                <i class="fa fa-money-bill mr-1"></i> Input Pembayaran
            </a>
        </div>
    </div>

    {{-- JATUH TEMPO --}}
    @if($pinjamanJatuhTempo->count() > 0)
    <div class="bg-white rounded-xl shadow overflow-x-auto mt-6">
        <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
            <i class="fa fa-exclamation-triangle text-amber-500"></i>
            <h3 class="text-base font-semibold text-gray-700">Pinjaman Jatuh Tempo</h3>
            <span class="ml-auto bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                {{ $pinjamanJatuhTempo->count() }}
            </span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Nasabah</th>
                    <th class="px-4 py-3 text-center">Tenor</th>
                    <th class="px-4 py-3 text-right">Pokok Pinjaman</th>
                    <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-center">Sisa Hari</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pinjamanJatuhTempo as $item)
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
                    <td class="px-4 py-3 text-center">
                        <span class="text-gray-700 text-xs">{{ $item->tenor_bulan }} {{ $satuan }}</span>
                        <span class="block mt-0.5">
                            @if($tipe === 'harian')
                                <span class="bg-orange-100 text-orange-700 text-xs px-1.5 py-0.5 rounded-full">
                                    <i class="fa fa-sun-o mr-0.5"></i>Harian
                                </span>
                            @else
                                <span class="bg-blue-100 text-blue-700 text-xs px-1.5 py-0.5 rounded-full">
                                    <i class="fa fa-calendar mr-0.5"></i>Bulanan
                                </span>
                            @endif
                        </span>
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
                        <a href="{{ route('karyawan.pinjaman.show', $item) }}"
                           class="bg-sky-500 hover:bg-sky-600 text-white text-xs px-3 py-1 rounded transition">
                            <i class="fa fa-eye mr-1"></i>Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection