@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="py-4">

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-blue-100 text-blue-600 rounded-full p-3">
                <i class="fa fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Nasabah</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($data['total_nasabah']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-emerald-100 text-emerald-600 rounded-full p-3">
                <i class="fa fa-id-badge text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Karyawan</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($data['total_karyawan']) }}</p>
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

    {{-- ROW 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-purple-100 text-purple-600 rounded-full p-3">
                <i class="fa fa-file-invoice-dollar text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pinjaman Aktif</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($data['pinjaman_aktif']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-cyan-100 text-cyan-600 rounded-full p-3">
                <i class="fa fa-hand-holding-usd text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Pinjaman Bulan Ini</p>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($data['total_pinjaman_bulan_ini'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-green-100 text-green-600 rounded-full p-3">
                <i class="fa fa-money-bill-wave text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Pembayaran Bulan Ini</p>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($data['total_pembayaran_bulan_ini'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">⚡ Aksi Cepat</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.pinjaman.index', ['status' => 'menunggu_approval']) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-4 py-2 rounded-lg font-medium">
                <i class="fa fa-check-circle mr-1"></i> Review Pengajuan
            </a>
            <a href="{{ route('admin.pinjaman.jatuh-tempo') }}"
               class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg font-medium">
                <i class="fa fa-exclamation mr-1"></i> Cek Jatuh Tempo
            </a>
            <a href="{{ route('admin.karyawan.create') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded-lg font-medium">
                <i class="fa fa-user-plus mr-1"></i> Tambah Karyawan
            </a>
            <a href="{{ route('admin.pembayaran.denda') }}"
               class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-4 py-2 rounded-lg font-medium">
                <i class="fa fa-ban mr-1"></i> Monitor Denda
            </a>
            <a href="{{ route('admin.laporan.pinjaman') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded-lg font-medium">
                <i class="fa fa-print mr-1"></i> Cetak Laporan
            </a>
        </div>
    </div>

</div>
@endsection