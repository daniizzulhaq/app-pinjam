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

</div>
@endsection