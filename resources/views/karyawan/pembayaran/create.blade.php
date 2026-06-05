@extends('layouts.karyawan')
@section('title', 'Input Pembayaran')
@section('page-title', 'Input Pembayaran')

@section('content')
<div class="py-4 max-w-2xl">

    <div class="mb-4">
        <a href="{{ route('karyawan.pinjaman.show', $pinjaman) }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke detail pinjaman
        </a>
    </div>

    @php
        $totalBayar = $pinjaman->pembayaran->sum('jumlah_dibayar');
        $sisaHutang = $pinjaman->total_pinjaman - $totalBayar;
        $persen     = $pinjaman->total_pinjaman > 0
            ? min(100, round($totalBayar / $pinjaman->total_pinjaman * 100))
            : 0;
        $profil = \App\Models\ProfilAdmin::profil();
    @endphp

    {{-- Info Pinjaman --}}
    <div class="bg-white rounded-xl shadow p-5 mb-4">
        <h3 class="font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">📋 Info Pinjaman</h3>
        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div>
                <p class="text-gray-400 text-xs">Nasabah</p>
                <p class="font-semibold text-gray-800">{{ $pinjaman->nasabah->nama_lengkap }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">No. Pinjaman</p>
                <code class="text-xs bg-gray-100 px-2 py-0.5 rounded text-blue-600">{{ $pinjaman->no_pinjaman }}</code>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Cicilan per Bulan</p>
                <p class="font-bold text-emerald-600">Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Angsuran Ke</p>
                <p class="font-bold text-gray-800">{{ $angsuranKe }} / {{ $pinjaman->tenor_bulan }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Total Terbayar</p>
                <p class="font-semibold text-gray-700">Rp {{ number_format($totalBayar, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Sisa Hutang</p>
                <p class="font-semibold text-red-500">Rp {{ number_format($sisaHutang, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Progress --}}
        <div class="flex justify-between text-xs text-gray-400 mb-1">
            <span>Progress Pelunasan</span>
            <span>{{ $persen }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: {{ $persen }}%"></div>
        </div>
    </div>

    {{-- Rekening Pembayaran --}}
    @if($profil->rekening && count($profil->rekening) > 0)
    <div class="bg-white rounded-xl shadow p-5 mb-4">
        <h3 class="font-semibold text-gray-800 mb-3 pb-3 border-b border-gray-100 text-sm flex items-center gap-2">
            <i class="fa fa-university text-blue-500"></i> Rekening Tujuan Pembayaran
        </h3>
        <div class="space-y-2">
            @foreach($profil->rekening as $rek)
            <div class="flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-lg px-4 py-3">
                <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-credit-card text-white text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">{{ $rek['bank'] }}</p>
                    <p class="font-bold text-gray-800 text-sm font-mono tracking-wider">{{ $rek['no_rek'] }}</p>
                    <p class="text-xs text-gray-500">a.n. {{ $rek['atas_nama'] }}</p>
                </div>
                <button type="button"
                        onclick="salinRekening('{{ $rek['no_rek'] }}', this)"
                        class="flex-shrink-0 text-xs bg-white border border-blue-200 text-blue-600
                               hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg transition font-medium">
                    <i class="fa fa-copy mr-1"></i> Salin
                </button>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-3">
            <i class="fa fa-info-circle mr-1"></i>
            Setelah transfer, upload bukti pembayaran di form di bawah.
        </p>
    </div>
    @endif

    {{-- Form Pembayaran --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Form Pembayaran Angsuran ke-{{ $angsuranKe }}</h2>
            <p class="text-sm text-gray-400 mt-0.5">Isi data pembayaran dengan benar</p>
        </div>

        <form method="POST"
              action="{{ route('karyawan.pembayaran.store', $pinjaman) }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Tanggal Bayar --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Bayar <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_bayar"
                       value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                       class="w-full border {{ $errors->has('tanggal_bayar') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                              rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                @error('tanggal_bayar')
                    <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Jenis Pembayaran --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Jenis Pembayaran <span class="text-red-500">*</span>
                </label>
                <select name="jenis_pembayaran"
                        id="jenis_pembayaran"
                        onchange="handleJenis(this.value)"
                        class="w-full border {{ $errors->has('jenis_pembayaran') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                               rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="cicilan_normal"   {{ old('jenis_pembayaran') == 'cicilan_normal'   ? 'selected' : '' }}>
                        Cicilan Normal — Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}
                    </option>
                    <option value="bayar_lunas"      {{ old('jenis_pembayaran') == 'bayar_lunas'      ? 'selected' : '' }}>
                        Bayar Lunas — Rp {{ number_format($sisaHutang, 0, ',', '.') }}
                    </option>
                    <option value="bayar_bunga_saja" {{ old('jenis_pembayaran') == 'bayar_bunga_saja' ? 'selected' : '' }}>
                        Bayar Bunga Saja
                    </option>
                    <option value="tidak_bayar"      {{ old('jenis_pembayaran') == 'tidak_bayar'      ? 'selected' : '' }}>
                        Tidak Bayar (Catat Tunggakan)
                    </option>
                </select>
                @error('jenis_pembayaran')
                    <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah Dibayar --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Jumlah Dibayar <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                    <input type="number" name="jumlah_dibayar" id="jumlah_dibayar"
                           value="{{ old('jumlah_dibayar') }}"
                           min="0" step="1000" placeholder="0"
                           class="w-full border {{ $errors->has('jumlah_dibayar') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                                  rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                @error('jumlah_dibayar')
                    <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <input type="text" name="keterangan"
                       value="{{ old('keterangan') }}"
                       placeholder="Catatan tambahan..."
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            {{-- Upload Bukti Pembayaran --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Bukti Pembayaran
                    <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>

                {{-- Drop area --}}
                <div id="dropArea"
                     onclick="document.getElementById('buktiInput').click()"
                     class="relative cursor-pointer border-2 border-dashed rounded-xl p-5 text-center transition
                            {{ $errors->has('bukti_pembayaran') ? 'border-red-400 bg-red-50' : 'border-gray-300 hover:border-emerald-400 hover:bg-emerald-50/30' }}">

                    {{-- Placeholder --}}
                    <div id="buktiPlaceholder">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fa fa-cloud-upload-alt text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500">Klik atau drag foto bukti transfer ke sini</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP · Maks 3 MB</p>
                    </div>

                    {{-- Preview --}}
                    <div id="buktiPreviewWrap" class="hidden">
                        <img id="buktiPreview"
                             src=""
                             alt="Preview"
                             class="mx-auto max-h-48 rounded-lg object-contain border border-gray-200 shadow-sm">
                        <p id="buktiFileName" class="text-xs text-gray-500 mt-2"></p>
                        <button type="button"
                                onclick="hapusBukti(event)"
                                class="mt-2 text-xs text-red-500 hover:text-red-700 underline">
                            <i class="fa fa-times mr-1"></i> Hapus
                        </button>
                    </div>
                </div>

                <input type="file"
                       id="buktiInput"
                       name="bukti_pembayaran"
                       accept="image/*"
                       class="hidden">

                @error('bukti_pembayaran')
                    <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Ringkasan --}}
            <div id="ringkasan" class="hidden mb-5 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm">
                <h4 class="font-semibold text-emerald-700 mb-2">✅ Ringkasan Pembayaran</h4>
                <div class="space-y-1 text-gray-700">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Angsuran ke</span>
                        <span class="font-medium">{{ $angsuranKe }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jumlah Dibayar</span>
                        <span id="ring_bayar" class="font-bold text-emerald-600">-</span>
                    </div>
                    <div class="flex justify-between border-t pt-1 mt-1">
                        <span class="text-gray-500">Sisa Setelah Bayar</span>
                        <span id="ring_sisa" class="font-bold text-red-500">-</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fa fa-save mr-1"></i> Simpan Pembayaran
                </button>
                <a href="{{ route('karyawan.pinjaman.show', $pinjaman) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-2 rounded-lg text-sm transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>

<script>
const cicilan       = {{ $pinjaman->cicilan_per_bulan }};
const sisaHutang    = {{ $sisaHutang }};
const bungaPerBulan = {{ $pinjaman->total_bunga / $pinjaman->tenor_bulan }};

function handleJenis(val) {
    const input = document.getElementById('jumlah_dibayar');
    if (val === 'cicilan_normal')    input.value = Math.round(cicilan);
    if (val === 'bayar_lunas')       input.value = Math.round(sisaHutang);
    if (val === 'bayar_bunga_saja')  input.value = Math.round(bungaPerBulan);
    if (val === 'tidak_bayar')       input.value = 0;
    updateRingkasan();
}

function updateRingkasan() {
    const bayar = parseFloat(document.getElementById('jumlah_dibayar').value) || 0;
    const sisa  = Math.max(0, sisaHutang - bayar);
    const fmt   = v => 'Rp ' + v.toLocaleString('id-ID', { maximumFractionDigits: 0 });
    document.getElementById('ring_bayar').textContent = fmt(bayar);
    document.getElementById('ring_sisa').textContent  = fmt(sisa);
    document.getElementById('ringkasan').classList.toggle('hidden', bayar <= 0);
}

document.getElementById('jumlah_dibayar').addEventListener('input', updateRingkasan);

// ---- Salin Rekening ----
function salinRekening(noRek, btn) {
    navigator.clipboard.writeText(noRek).then(() => {
        const ori = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-check mr-1"></i> Tersalin!';
        btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
        btn.classList.remove('text-blue-600', 'border-blue-200', 'bg-white');
        setTimeout(() => {
            btn.innerHTML = ori;
            btn.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
            btn.classList.add('text-blue-600', 'border-blue-200', 'bg-white');
        }, 2000);
    });
}

// ---- Upload Bukti ----
const buktiInput       = document.getElementById('buktiInput');
const buktiPreview     = document.getElementById('buktiPreview');
const buktiPreviewWrap = document.getElementById('buktiPreviewWrap');
const buktiPlaceholder = document.getElementById('buktiPlaceholder');
const buktiFileName    = document.getElementById('buktiFileName');

buktiInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    buktiPreview.src     = URL.createObjectURL(file);
    buktiFileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    buktiPlaceholder.classList.add('hidden');
    buktiPreviewWrap.classList.remove('hidden');
});

function hapusBukti(e) {
    e.stopPropagation();
    buktiInput.value     = '';
    buktiPreview.src     = '';
    buktiFileName.textContent = '';
    buktiPlaceholder.classList.remove('hidden');
    buktiPreviewWrap.classList.add('hidden');
}

// Drag & Drop
const dropArea = document.getElementById('dropArea');
dropArea.addEventListener('dragover',  e => { e.preventDefault(); dropArea.classList.add('border-emerald-400', 'bg-emerald-50'); });
dropArea.addEventListener('dragleave', () => dropArea.classList.remove('border-emerald-400', 'bg-emerald-50'));
dropArea.addEventListener('drop', e => {
    e.preventDefault();
    dropArea.classList.remove('border-emerald-400', 'bg-emerald-50');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt  = new DataTransfer();
        dt.items.add(file);
        buktiInput.files = dt.files;
        buktiInput.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection