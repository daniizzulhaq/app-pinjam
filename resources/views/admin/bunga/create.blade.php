@extends('layouts.admin')

@section('title', 'Tambah Bunga')
@section('page-title', 'Tambah Bunga Pinjaman')

@section('content')
<div class="py-4">

    <div class="mb-4">
        <a href="{{ route('admin.bunga.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke daftar bunga
        </a>
    </div>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow p-6">

            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Data Bunga Baru</h2>
                <p class="text-sm text-gray-400 mt-0.5">Lengkapi semua field yang wajib diisi</p>
            </div>

            <form method="POST" action="{{ route('admin.bunga.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Bunga <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_bunga" value="{{ old('nama_bunga') }}"
                           placeholder="Contoh: Bunga Flat 2%/bulan"
                           class="w-full border {{ $errors->has('nama_bunga') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('nama_bunga')
                        <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Persentase (%/bulan) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <input type="number" name="persentase" value="{{ old('persentase') }}"
                               step="0.01" min="0" max="100" placeholder="Contoh: 2.50"
                               class="w-full border {{ $errors->has('persentase') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} border-r-0 rounded-l-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span class="bg-gray-100 border border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">%</span>
                    </div>
                    @error('persentase')
                        <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis Bunga <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis"
                            class="w-full border {{ $errors->has('jenis') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="flat"    {{ old('jenis') == 'flat'    ? 'selected' : '' }}>Flat</option>
                        <option value="efektif" {{ old('jenis') == 'efektif' ? 'selected' : '' }}>Efektif</option>
                    </select>
                    @error('jenis')
                        <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 text-xs text-blue-700">
                    <p class="font-semibold mb-1"><i class="fa fa-info-circle mr-1"></i> Keterangan Jenis Bunga</p>
                    <p>• <strong>Flat</strong>: Bunga dihitung dari pokok pinjaman awal setiap bulan</p>
                    <p>• <strong>Efektif</strong>: Bunga dihitung dari sisa pokok pinjaman</p>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fa fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('admin.bunga.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-2 rounded-lg text-sm transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection