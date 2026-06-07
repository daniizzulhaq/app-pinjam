{{-- ============================================================ --}}
{{-- FILE: resources/views/karyawan/pinjaman/create.blade.php   --}}
{{-- ============================================================ --}}
@extends('layouts.karyawan')
@section('title', 'Ajukan Pinjaman')
@section('page-title', 'Ajukan Pinjaman Baru')

@section('content')
<div class="py-4">

    <div class="mb-4">
        <a href="{{ route('karyawan.pinjaman.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke daftar pinjaman
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-4 md:p-6 max-w-2xl">

        <div class="mb-5 md:mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Form Pengajuan Pinjaman</h2>
            <p class="text-sm text-gray-400 mt-0.5">Pinjaman akan diproses setelah disetujui admin</p>
        </div>

        <form method="POST" action="{{ route('karyawan.pinjaman.store') }}">
            @csrf

            {{-- Nasabah --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nasabah <span class="text-red-500">*</span>
                </label>
                <select name="nasabah_id"
                        class="w-full border {{ $errors->has('nasabah_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">-- Pilih Nasabah --</option>
                    @foreach($nasabah as $n)
                        <option value="{{ $n->id }}"
                            {{ old('nasabah_id', $nasabahDipilih?->id) == $n->id ? 'selected' : '' }}>
                            {{ $n->nama_lengkap }} — {{ $n->no_ktp }}
                        </option>
                    @endforeach
                </select>
                @error('nasabah_id')
                    <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Pengajuan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Pengajuan
                </label>
                <div class="flex flex-wrap gap-3 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tgl_mode" value="otomatis" id="tgl_otomatis"
                               class="accent-emerald-600"
                               {{ old('tanggal_pengajuan') ? '' : 'checked' }}
                               onchange="toggleTanggal()">
                        <span class="text-sm text-gray-700">Otomatis (hari ini)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tgl_mode" value="manual" id="tgl_manual"
                               class="accent-emerald-600"
                               {{ old('tanggal_pengajuan') ? 'checked' : '' }}
                               onchange="toggleTanggal()">
                        <span class="text-sm text-gray-700">Input manual</span>
                    </label>
                </div>
                <div id="tgl_otomatis_preview"
                     class="{{ old('tanggal_pengajuan') ? 'hidden' : '' }} flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm text-gray-500">
                    <i class="fa fa-calendar-check text-emerald-500"></i>
                    <span>{{ now()->translatedFormat('d F Y') }}</span>
                    <span class="text-xs text-gray-400 hidden sm:inline">(tanggal hari ini, terisi otomatis)</span>
                </div>
                <div id="tgl_manual_input" class="{{ old('tanggal_pengajuan') ? '' : 'hidden' }}">
                    <input type="date" name="tanggal_pengajuan"
                           value="{{ old('tanggal_pengajuan') }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="w-full border {{ $errors->has('tanggal_pengajuan') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fa fa-info-circle mr-0.5"></i> Tidak boleh lebih dari hari ini
                    </p>
                </div>
                @error('tanggal_pengajuan')
                    <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah Pinjaman --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Jumlah Pinjaman <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                    <input type="number" name="jumlah_pinjaman"
                           value="{{ old('jumlah_pinjaman') }}"
                           min="100000" step="50000"
                           placeholder="0"
                           class="w-full border {{ $errors->has('jumlah_pinjaman') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400"
                           oninput="hitungCicilan()">
                </div>
                @error('jumlah_pinjaman')
                    <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Bunga & Tenor --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Bunga <span class="text-red-500">*</span>
                    </label>
                    <select name="bunga_id"
                            class="w-full border {{ $errors->has('bunga_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400"
                            onchange="hitungCicilan()">
                        <option value="">-- Pilih Bunga --</option>
                        @foreach($bunga as $b)
                            <option value="{{ $b->id }}"
                                    data-persen="{{ $b->persentase }}"
                                {{ old('bunga_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bunga }} ({{ $b->persentase }}%/bln)
                            </option>
                        @endforeach
                    </select>
                    @error('bunga_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tenor <span class="text-red-500">*</span>
                    </label>
                    <select name="tenor_id"
                            class="w-full border {{ $errors->has('tenor_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400"
                            onchange="hitungCicilan()">
                        <option value="">-- Pilih Tenor --</option>
                        @php
                            $tenorBulanan = $tenor->where('tipe', 'bulanan');
                            $tenorHarian  = $tenor->where('tipe', 'harian');
                        @endphp
                        @if($tenorBulanan->count())
                        <optgroup label="📅 Bulanan">
                            @foreach($tenorBulanan as $t)
                                <option value="{{ $t->id }}"
                                        data-bulan="{{ $t->bulan }}"
                                        data-tipe="{{ $t->tipe }}"
                                    {{ old('tenor_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->label }}
                                </option>
                            @endforeach
                        </optgroup>
                        @endif
                        @if($tenorHarian->count())
                        <optgroup label="☀️ Harian">
                            @foreach($tenorHarian as $t)
                                <option value="{{ $t->id }}"
                                        data-bulan="{{ $t->bulan }}"
                                        data-tipe="{{ $t->tipe }}"
                                    {{ old('tenor_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->label }}
                                </option>
                            @endforeach
                        </optgroup>
                        @endif
                    </select>
                    @error('tenor_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Simulasi Pinjaman --}}
            <div id="simulasi" class="hidden mb-5 bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-emerald-700 mb-3">📊 Simulasi Pinjaman</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">Total Bunga</p>
                        <p id="sim_bunga" class="font-semibold text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Total Pinjaman</p>
                        <p id="sim_total" class="font-semibold text-gray-800">-</p>
                    </div>
                    <div class="col-span-2" id="sim_cicilan_wrap">
                        <p id="sim_cicilan_label" class="text-gray-500 text-xs">Cicilan per Bulan</p>
                        <p id="sim_cicilan" class="font-bold text-emerald-700 text-lg">-</p>
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="mb-5 md:mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                <textarea name="catatan_pengajuan" rows="3"
                          placeholder="Tujuan pinjaman, keterangan tambahan..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 resize-none">{{ old('catatan_pengajuan') }}</textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fa fa-paper-plane mr-1"></i> Kirim Pengajuan
                </button>
                <a href="{{ route('karyawan.pinjaman.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-2 rounded-lg text-sm transition">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

<script>
function toggleTanggal() {
    const isManual = document.getElementById('tgl_manual').checked;
    document.getElementById('tgl_otomatis_preview').classList.toggle('hidden', isManual);
    document.getElementById('tgl_manual_input').classList.toggle('hidden', !isManual);
    if (!isManual) {
        document.querySelector('[name=tanggal_pengajuan]').value = '';
    }
}

function hitungCicilan() {
    const jumlah    = parseFloat(document.querySelector('[name=jumlah_pinjaman]').value) || 0;
    const bungaSel  = document.querySelector('[name=bunga_id]');
    const tenorSel  = document.querySelector('[name=tenor_id]');

    const persenBln = parseFloat(bungaSel.selectedOptions[0]?.dataset.persen) || 0;
    const periode   = parseFloat(tenorSel.selectedOptions[0]?.dataset.bulan)  || 0;
    const tipe      = tenorSel.selectedOptions[0]?.dataset.tipe || 'bulanan';

    if (!jumlah || !persenBln || !periode) {
        document.getElementById('simulasi').classList.add('hidden');
        return;
    }

    let totalBunga;
    if (tipe === 'harian') {
        totalBunga = jumlah * (persenBln / 100);
    } else {
        totalBunga = jumlah * (persenBln / 100) * periode;
    }

    const totalPinjaman = jumlah + totalBunga;
    const cicilan       = totalPinjaman / periode;
    const fmt = v => 'Rp ' + v.toLocaleString('id-ID', { maximumFractionDigits: 0 });

    document.getElementById('sim_bunga').textContent = fmt(totalBunga);
    document.getElementById('sim_total').textContent = fmt(totalPinjaman);

    const cicilanWrap = document.getElementById('sim_cicilan_wrap');
    if (tipe === 'harian') {
        cicilanWrap.classList.add('hidden');
    } else {
        cicilanWrap.classList.remove('hidden');
        document.getElementById('sim_cicilan_label').textContent = 'Cicilan per Bulan';
        document.getElementById('sim_cicilan').textContent       = fmt(cicilan);
    }

    document.getElementById('simulasi').classList.remove('hidden');
}
</script>

@endsection