@extends('layouts.karyawan')

@section('title', 'Edit Nasabah')
@section('page-title', 'Edit Data Nasabah')

@section('content')
<div class="py-4">

    <div class="mb-4">
        <a href="{{ route('karyawan.nasabah.show', $nasabah) }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke detail nasabah
        </a>
    </div>

    <form method="POST" action="{{ route('karyawan.nasabah.update', $nasabah) }}"
          enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- ===== KOLOM KIRI: Foto ===== --}}
            <div class="lg:col-span-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">

                {{-- Foto Nasabah --}}
                <div class="bg-white rounded-xl shadow p-5 md:p-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">
                        📷 Foto Nasabah
                    </h3>
                    <div class="mb-3">
                        @if($nasabah->foto_nasabah)
                            <img src="{{ asset('storage/' . $nasabah->foto_nasabah) }}"
                                 id="preview_foto_nasabah" alt="Foto Nasabah"
                                 class="w-full h-44 object-cover rounded-lg border border-gray-200">
                        @else
                            <div id="preview_foto_nasabah_placeholder"
                                 class="w-full h-44 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400">
                                <i class="fa fa-user text-3xl mb-1"></i>
                                <p class="text-xs">Belum ada foto</p>
                            </div>
                            <img id="preview_foto_nasabah" src="" alt=""
                                 class="w-full h-44 object-cover rounded-lg border border-gray-200 hidden">
                        @endif
                    </div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        {{ $nasabah->foto_nasabah ? 'Ganti Foto Nasabah' : 'Upload Foto Nasabah' }}
                    </label>
                    <input type="file" name="foto_nasabah" accept="image/*"
                           onchange="previewImage(this, 'preview_foto_nasabah', 'preview_foto_nasabah_placeholder')"
                           class="w-full text-xs text-gray-500 border border-gray-300 rounded-lg px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG. Maks 2MB. Kosongkan jika tidak diubah.</p>
                    @error('foto_nasabah')
                        <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto KTP --}}
                <div class="bg-white rounded-xl shadow p-5 md:p-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">
                        🪪 Foto KTP
                    </h3>
                    <div class="mb-3">
                        @if($nasabah->foto_ktp)
                            <img src="{{ asset('storage/' . $nasabah->foto_ktp) }}"
                                 id="preview_foto_ktp" alt="Foto KTP"
                                 class="w-full h-44 object-cover rounded-lg border border-gray-200">
                        @else
                            <div id="preview_foto_ktp_placeholder"
                                 class="w-full h-44 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400">
                                <i class="fa fa-id-card text-3xl mb-1"></i>
                                <p class="text-xs">Belum ada foto KTP</p>
                            </div>
                            <img id="preview_foto_ktp" src="" alt=""
                                 class="w-full h-44 object-cover rounded-lg border border-gray-200 hidden">
                        @endif
                    </div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        {{ $nasabah->foto_ktp ? 'Ganti Foto KTP' : 'Upload Foto KTP' }}
                    </label>
                    <input type="file" name="foto_ktp" accept="image/*"
                           onchange="previewImage(this, 'preview_foto_ktp', 'preview_foto_ktp_placeholder')"
                           class="w-full text-xs text-gray-500 border border-gray-300 rounded-lg px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG. Maks 2MB. Kosongkan jika tidak diubah.</p>
                    @error('foto_ktp')
                        <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- ===== KOLOM KANAN: Data Nasabah ===== --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow p-4 md:p-6">

                    <div class="mb-5 pb-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">Data Pribadi</h2>
                        <p class="text-sm text-gray-400 mt-0.5">Perubahan akan langsung tersimpan</p>
                    </div>

                    {{-- ROW 1 --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5 mb-4 md:mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_lengkap"
                                   value="{{ old('nama_lengkap', $nasabah->nama_lengkap) }}"
                                   placeholder="Masukkan nama lengkap"
                                   class="w-full border {{ $errors->has('nama_lengkap') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            @error('nama_lengkap')
                                <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                No KTP (NIK) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_ktp"
                                   value="{{ old('no_ktp', $nasabah->no_ktp) }}"
                                   placeholder="16 digit NIK" maxlength="16"
                                   class="w-full border {{ $errors->has('no_ktp') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            @error('no_ktp')
                                <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- ROW 2 --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5 mb-4 md:mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tempat Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tempat_lahir"
                                   value="{{ old('tempat_lahir', $nasabah->tempat_lahir) }}"
                                   placeholder="Kota tempat lahir"
                                   class="w-full border {{ $errors->has('tempat_lahir') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            @error('tempat_lahir')
                                <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_lahir"
                                   value="{{ old('tanggal_lahir', \Carbon\Carbon::parse($nasabah->tanggal_lahir)->format('Y-m-d')) }}"
                                   class="w-full border {{ $errors->has('tanggal_lahir') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            @error('tanggal_lahir')
                                <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select name="jenis_kelamin"
                                    class="w-full border {{ $errors->has('jenis_kelamin') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin', $nasabah->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $nasabah->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- ROW 3 --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5 mb-4 md:mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                No HP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_telepon"
                                   value="{{ old('no_telepon', $nasabah->no_telepon) }}"
                                   placeholder="08xxxxxxxxxx" maxlength="15"
                                   class="w-full border {{ $errors->has('no_telepon') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            @error('no_telepon')
                                <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                            <input type="text" name="pekerjaan"
                                   value="{{ old('pekerjaan', $nasabah->pekerjaan) }}"
                                   placeholder="Opsional"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        </div>
                    </div>

                    {{-- ROW 4 --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5 mb-4 md:mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kota <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="kota"
                                   value="{{ old('kota', $nasabah->kota) }}"
                                   placeholder="Kota domisili"
                                   class="w-full border {{ $errors->has('kota') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            @error('kota')
                                <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Provinsi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="provinsi"
                                   value="{{ old('provinsi', $nasabah->provinsi) }}"
                                   placeholder="Provinsi domisili"
                                   class="w-full border {{ $errors->has('provinsi') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            @error('provinsi')
                                <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-5 md:mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alamat" rows="3" placeholder="Alamat lengkap nasabah"
                                  class="w-full border {{ $errors->has('alamat') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 resize-none">{{ old('alamat', $nasabah->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100">
                        <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            <i class="fa fa-save mr-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('karyawan.nasabah.show', $nasabah) }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-2 rounded-lg text-sm transition">
                            Batal
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>

<script>
function previewImage(input, previewId, placeholderId) {
    const preview     = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection