@extends('layouts.karyawan')
@section('title', 'Tambah Nasabah')
@section('page-title', 'Input Data Nasabah Baru')
 
@section('content')
<div class="py-4 max-w-2xl">
<div class="bg-white rounded-xl shadow p-6">
<form method="POST" action="{{ route('karyawan.nasabah.store') }}" enctype="multipart/form-data">
    @csrf
 
    <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Data Pribadi</h4>
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. KTP <span class="text-red-500">*</span></label>
            <input type="text" name="no_ktp" value="{{ old('no_ktp') }}" maxlength="16"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none @error('no_ktp') border-red-400 @enderror">
            @error('no_ktp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none @error('nama_lengkap') border-red-400 @enderror">
            @error('nama_lengkap') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
            <select name="jenis_kelamin" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">-- Pilih --</option>
                <option value="L" {{ old('jenis_kelamin')=='L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin')=='P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
            <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
        </div>
    </div>
 
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
        <textarea name="alamat" rows="2"
                  class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">{{ old('alamat') }}</textarea>
    </div>
 
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kota <span class="text-red-500">*</span></label>
            <input type="text" name="kota" value="{{ old('kota') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
            <input type="text" name="provinsi" value="{{ old('provinsi') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto KTP</label>
            <input type="file" name="foto_ktp" accept="image/*"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Nasabah</label>
            <input type="file" name="foto_nasabah" accept="image/*"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
 
    <div class="flex gap-3">
        <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
            <i class="fa fa-save mr-1"></i> Simpan Nasabah
        </button>
        <a href="{{ route('karyawan.nasabah.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm">Batal</a>
    </div>
</form>
</div>
</div>
@endsection