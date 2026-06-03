@extends('layouts.admin')
@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Data Karyawan')

@section('content')
<div class="py-4 max-w-lg">
    <div class="bg-white rounded-xl shadow p-6">

        <form method="POST" action="{{ route('admin.karyawan.update', $karyawan) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $karyawan->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $karyawan->email) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password Baru <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ $karyawan->is_active ? 'checked' : '' }}
                           class="rounded">
                    <span class="text-sm text-gray-700">Akun Aktif</span>
                </label>
            </div>

            {{-- REKENING --}}
            <div class="border-t pt-5 mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Informasi Rekening</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                    <input type="text" name="nama_bank" value="{{ old('nama_bank', $karyawan->nama_bank) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('nama_bank') border-red-400 @enderror"
                           placeholder="Contoh: BCA, BRI, Mandiri">
                    @error('nama_bank') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekening</label>
                    <input type="text" name="no_rekening" value="{{ old('no_rekening', $karyawan->no_rekening) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('no_rekening') border-red-400 @enderror"
                           placeholder="Nomor rekening">
                    @error('no_rekening') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                    <input type="text" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', $karyawan->nama_pemilik_rekening) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('nama_pemilik_rekening') border-red-400 @enderror"
                           placeholder="Sesuai buku tabungan">
                    @error('nama_pemilik_rekening') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                    <i class="fa fa-save mr-1"></i> Update
                </button>
                <a href="{{ route('admin.karyawan.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection