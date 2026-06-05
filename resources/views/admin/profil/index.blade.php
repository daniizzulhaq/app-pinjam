@extends('layouts.admin')

@section('title', 'Profil Lembaga')
@section('page-title', 'Profil Lembaga')

@push('styles')
<style>
    .logo-preview {
        width: 120px; height: 120px;
        object-fit: cover;
        border-radius: 12px;
        border: 3px solid #e2e8f0;
        transition: border-color .2s;
    }
    .logo-preview:hover { border-color: #3b82f6; }

    .rekening-row { animation: fadeIn .25s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

    .bank-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        background: #dbeafe;
        color: #1d4ed8;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto pt-6 space-y-6">

    {{-- ========== CARD HEADER ========== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            {{-- Logo Preview --}}
            <div class="relative group">
                <img id="logoPreview"
                     src="{{ $profil->logo_url }}"
                     alt="Logo"
                     class="logo-preview shadow">

                {{-- Overlay klik --}}
                <label for="logoInput"
                       class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-xl
                              opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                    <span class="text-white text-xs font-semibold text-center leading-snug px-2">
                        <i class="fa fa-camera block text-lg mb-1"></i>Ganti Logo
                    </span>
                </label>
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $profil->nama_lembaga }}</h2>
                @if($profil->tagline)
                    <p class="text-gray-500 text-sm mt-0.5">{{ $profil->tagline }}</p>
                @endif
                <p class="text-blue-600 text-xs mt-2 font-medium">
                    <i class="fa fa-info-circle mr-1"></i>
                    Klik foto untuk mengganti logo lembaga
                </p>
            </div>
        </div>
    </div>

    {{-- ========== FORM ========== --}}
    <form method="POST"
          action="{{ route('admin.profil.update') }}"
          enctype="multipart/form-data"
          id="profilForm">
        @csrf
        @method('PUT')

        {{-- Hidden input logo --}}
        <input type="file" id="logoInput" name="logo" accept="image/*" class="hidden">
        <input type="hidden" name="hapus_logo" id="hapusLogoInput" value="0">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===== KIRI: Identitas ===== --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Card Identitas --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">
                        <i class="fa fa-building mr-2 text-blue-500"></i>Identitas Lembaga
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Nama Lembaga --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lembaga <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_lembaga"
                                   value="{{ old('nama_lembaga', $profil->nama_lembaga) }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition
                                          @error('nama_lembaga') border-red-400 @enderror"
                                   placeholder="Contoh: Koperasi Sejahtera Mandiri">
                            @error('nama_lembaga')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tagline --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tagline / Slogan</label>
                            <input type="text" name="tagline"
                                   value="{{ old('tagline', $profil->tagline) }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   placeholder="Contoh: Solusi Keuangan Terpercaya">
                        </div>

                        {{-- No Telepon --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                    <i class="fa fa-phone text-xs"></i>
                                </span>
                                <input type="text" name="no_telepon"
                                       value="{{ old('no_telepon', $profil->no_telepon) }}"
                                       class="w-full rounded-lg border border-gray-300 pl-8 pr-3 py-2 text-sm
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                       placeholder="08xx-xxxx-xxxx">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                    <i class="fa fa-envelope text-xs"></i>
                                </span>
                                <input type="email" name="email"
                                       value="{{ old('email', $profil->email) }}"
                                       class="w-full rounded-lg border border-gray-300 pl-8 pr-3 py-2 text-sm
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition
                                              @error('email') border-red-400 @enderror"
                                       placeholder="admin@lembaga.co.id">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="alamat" rows="3"
                                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                             focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none"
                                      placeholder="Jl. ...">{{ old('alamat', $profil->alamat) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ===== Card Rekening ===== --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fa fa-university mr-2 text-blue-500"></i>Rekening Bank
                        </h3>
                        <button type="button" id="btnTambahRekening"
                                class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg
                                       flex items-center gap-1.5 transition font-medium">
                            <i class="fa fa-plus"></i> Tambah Rekening
                        </button>
                    </div>

                    <div id="rekeningContainer" class="space-y-3">
                        @php $rekening = old('rekening', $profil->rekening ?? []); @endphp

                        @forelse($rekening as $i => $rek)
                            @include('admin.profil._rekening_row', ['i' => $i, 'rek' => $rek])
                        @empty
                            <p id="emptyRekening" class="text-gray-400 text-sm text-center py-6 border-2 border-dashed rounded-xl">
                                Belum ada rekening ditambahkan.
                                Klik <strong>Tambah Rekening</strong> untuk menambah.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ===== KANAN: Logo & Aksi ===== --}}
            <div class="space-y-5">

                {{-- Card Logo --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">
                        <i class="fa fa-image mr-2 text-blue-500"></i>Logo Lembaga
                    </h3>

                    <div class="flex flex-col items-center gap-3">
                        <img id="logoPreview2"
                             src="{{ $profil->logo_url }}"
                             alt="Logo"
                             class="w-32 h-32 object-cover rounded-xl border-2 border-gray-200 shadow-sm">

                        <label for="logoInput"
                               class="cursor-pointer w-full text-center bg-gray-50 hover:bg-blue-50
                                      border border-dashed border-gray-300 hover:border-blue-400
                                      rounded-lg py-2 text-sm text-gray-600 hover:text-blue-600 transition">
                            <i class="fa fa-upload mr-1"></i> Pilih Foto / Logo
                        </label>

                        <p class="text-gray-400 text-xs text-center">JPG, PNG, WEBP · Maks 2MB</p>

                        @if($profil->logo)
                            <button type="button" id="btnHapusLogo"
                                    class="text-xs text-red-500 hover:text-red-700 underline transition">
                                <i class="fa fa-trash mr-1"></i> Hapus Logo
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Card Aksi --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                                   py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                        <i class="fa fa-save"></i> Simpan Profil
                    </button>

                    <a href="{{ route('admin.dashboard') }}"
                       class="w-full block text-center border border-gray-300 hover:bg-gray-50
                              text-gray-600 py-2.5 rounded-xl text-sm transition">
                        Batal
                    </a>
                </div>

                {{-- Info Card --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-xs text-blue-700 space-y-1.5">
                    <p class="font-semibold text-blue-800"><i class="fa fa-lightbulb mr-1"></i> Info</p>
                    <p>Data rekening yang disimpan di sini akan ditampilkan pada menu <strong>Invoice Pembayaran</strong> karyawan.</p>
                    <p>Logo akan muncul di header laporan PDF.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- ===== PARTIAL: Satu baris rekening ===== --}}
{{-- Letakkan di: resources/views/admin/profil/_rekening_row.blade.php --}}

@push('scripts')
<script>
    // ---- Logo Preview ----
    const logoInput    = document.getElementById('logoInput');
    const logoPreview  = document.getElementById('logoPreview');
    const logoPreview2 = document.getElementById('logoPreview2');

    logoInput?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        if (logoPreview)  logoPreview.src  = url;
        if (logoPreview2) logoPreview2.src = url;
        document.getElementById('hapusLogoInput').value = '0';
    });

    document.getElementById('btnHapusLogo')?.addEventListener('click', function () {
        if (!confirm('Yakin ingin menghapus logo?')) return;
        document.getElementById('hapusLogoInput').value = '1';
        const placeholder = "https://ui-avatars.com/api/?name=P&background=1e40af&color=fff&size=200";
        if (logoPreview)  logoPreview.src  = placeholder;
        if (logoPreview2) logoPreview2.src = placeholder;
        logoInput.value = '';
        this.style.display = 'none';
    });

    // ---- Rekening Dinamis ----
    let rekeningIndex = {{ count($rekening ?? []) }};

    function buatBarisRekening(i) {
        return `
        <div class="rekening-row flex gap-2 items-start bg-gray-50 rounded-xl p-3 border border-gray-200" id="rekRow${i}">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div>
                    <label class="text-xs text-gray-500 mb-0.5 block">Nama Bank</label>
                    <input type="text" name="rekening[${i}][bank]"
                           class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none"
                           placeholder="BCA, BRI, BNI ...">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-0.5 block">No. Rekening</label>
                    <input type="text" name="rekening[${i}][no_rek]"
                           class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none"
                           placeholder="12345678">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-0.5 block">Atas Nama</label>
                    <input type="text" name="rekening[${i}][atas_nama]"
                           class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none"
                           placeholder="Nama pemilik rekening">
                </div>
            </div>
            <button type="button" onclick="hapusRekening(${i})"
                    class="mt-5 text-red-400 hover:text-red-600 text-sm transition flex-shrink-0">
                <i class="fa fa-times-circle fa-lg"></i>
            </button>
        </div>`;
    }

    document.getElementById('btnTambahRekening').addEventListener('click', function () {
        const container = document.getElementById('rekeningContainer');
        const empty     = document.getElementById('emptyRekening');
        if (empty) empty.remove();
        container.insertAdjacentHTML('beforeend', buatBarisRekening(rekeningIndex));
        rekeningIndex++;
    });

    function hapusRekening(i) {
        const row = document.getElementById('rekRow' + i);
        if (row) row.remove();

        // Tampilkan pesan kosong jika tidak ada rekening
        const container = document.getElementById('rekeningContainer');
        if (container.querySelectorAll('.rekening-row').length === 0) {
            container.innerHTML = `
                <p id="emptyRekening" class="text-gray-400 text-sm text-center py-6 border-2 border-dashed rounded-xl">
                    Belum ada rekening ditambahkan.
                    Klik <strong>Tambah Rekening</strong> untuk menambah.
                </p>`;
        }
    }
</script>
@endpush