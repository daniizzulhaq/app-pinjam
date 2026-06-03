@extends('layouts.admin')

@section('title', 'Tambah Tenor')
@section('page-title', 'Tambah Tenor Pinjaman')

@section('content')
<div class="py-4">

    <div class="mb-4">
        <a href="{{ route('admin.tenor.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke daftar tenor
        </a>
    </div>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow p-6">

            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Data Tenor Baru</h2>
                <p class="text-sm text-gray-400 mt-0.5">Masukkan jumlah bulan tenor</p>
            </div>

            <form method="POST" action="{{ route('admin.tenor.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Label Tenor
                    </label>
                    <input type="text" name="label" value="{{ old('label') }}"
                           placeholder="Contoh: 12 Bulan"
                           class="w-full border {{ $errors->has('label') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('label')
                        <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Bulan <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <input type="number" name="bulan" value="{{ old('bulan') }}"
                               min="1" max="360" placeholder="Contoh: 12"
                               class="w-full border {{ $errors->has('bulan') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} border-r-0 rounded-l-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span class="bg-gray-100 border border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">Bulan</span>
                    </div>
                    @error('bulan')
                        <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Label akan otomatis terisi jika dikosongkan</p>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fa fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('admin.tenor.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-2 rounded-lg text-sm transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection