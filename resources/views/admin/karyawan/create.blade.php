@extends('layouts.admin')
@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Karyawan Baru')

@section('content')
<div class="py-4 max-w-lg">
    <div class="bg-white rounded-xl shadow p-6">

        <form method="POST" action="{{ route('admin.karyawan.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror"
                       placeholder="Nama lengkap karyawan">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-400 @enderror"
                       placeholder="email@contoh.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('password') border-red-400 @enderror"
                       placeholder="Minimal 8 karakter">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="Ulangi password">
            </div>

            {{-- REKENING --}}
            <div class="border-t pt-5 mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Informasi Rekening</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                    <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('nama_bank') border-red-400 @enderror"
                           placeholder="Contoh: BCA, BRI, Mandiri">
                    @error('nama_bank') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekening</label>
                    <input type="text" name="no_rekening" value="{{ old('no_rekening') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('no_rekening') border-red-400 @enderror"
                           placeholder="Nomor rekening">
                    @error('no_rekening') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                    <input type="text" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('nama_pemilik_rekening') border-red-400 @enderror"
                           placeholder="Sesuai buku tabungan">
                    @error('nama_pemilik_rekening') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                    <i class="fa fa-save mr-1"></i> Simpan
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