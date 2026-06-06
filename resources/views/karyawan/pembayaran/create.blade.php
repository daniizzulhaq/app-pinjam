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
        $isHarian   = $pinjaman->tenor_tipe === 'harian';
        $totalBayar = $pinjaman->pembayaran->sum('jumlah_dibayar');
        $sisaHutang = $pinjaman->total_pinjaman - $totalBayar;
        $profil     = \App\Models\ProfilAdmin::profil();
        $jatuhTempo = \Carbon\Carbon::parse($pinjaman->tanggal_jatuh_tempo);
        $hariSisa   = now()->startOfDay()->diffInDays($jatuhTempo->startOfDay(), false);
        $adaDenda   = $infoDenda['denda'] > 0;

        // Hitung bunga baru jika bayar bunga (untuk info di form)
        $bungaBaru         = $pinjaman->jumlah_pinjaman * ($pinjaman->bunga_persen / 100);
        $totalPinjamanBaru = $pinjaman->jumlah_pinjaman + $bungaBaru;
        $jatuhTempoMundur  = $jatuhTempo->copy()->addDays($pinjaman->tenor_bulan);
    @endphp

    {{-- Alert Denda --}}
    @if($adaDenda)
    <div class="bg-red-50 border border-red-300 rounded-xl px-5 py-4 mb-4 flex items-start gap-3">
        <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fa fa-triangle-exclamation text-red-500"></i>
        </div>
        <div>
            <p class="font-semibold text-red-700 text-sm">⚠️ Pinjaman Ini Terkena Denda Keterlambatan</p>
            <p class="text-red-600 text-xs mt-1">
                Terlambat <strong>{{ $infoDenda['hari'] }} hari</strong> ×
                <strong>Rp 50.000</strong> =
                <strong>Rp {{ number_format($infoDenda['denda'], 0, ',', '.') }}</strong>
            </p>
            <p class="text-red-400 text-xs mt-0.5">
                Denda dihitung sejak lewat jam 17:00 di hari jatuh tempo
                ({{ $jatuhTempo->translatedFormat('d F Y') }})
            </p>
        </div>
    </div>
    @endif

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

            @if($isHarian)
            <div>
                <p class="text-gray-400 text-xs">Pokok Pinjaman</p>
                <p class="font-bold text-gray-800">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Bunga Periode Ini</p>
                <p class="font-bold text-orange-500">Rp {{ number_format($pinjaman->total_bunga, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Jatuh Tempo</p>
                <p class="font-bold {{ $hariSisa < 0 ? 'text-red-600' : ($hariSisa <= 3 ? 'text-orange-500' : 'text-gray-800') }}">
                    {{ $jatuhTempo->translatedFormat('d F Y') }}
                    @if($hariSisa < 0)
                        <span class="text-xs">(Terlambat {{ abs($hariSisa) }} hari)</span>
                    @elseif($hariSisa == 0)
                        <span class="text-xs">(Hari ini)</span>
                    @else
                        <span class="text-xs text-gray-400">({{ $hariSisa }} hari lagi)</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">Tenor</p>
                <p class="font-semibold text-gray-700">{{ $pinjaman->tenor_bulan }} Hari</p>
            </div>
            @else
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
            @endif

            @if($adaDenda)
            <div class="col-span-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                <p class="text-xs text-red-400">Denda Keterlambatan</p>
                <p class="font-bold text-red-600">
                    Rp {{ number_format($infoDenda['denda'], 0, ',', '.') }}
                    <span class="text-xs font-normal">({{ $infoDenda['hari'] }} hari × Rp 50.000)</span>
                </p>
            </div>
            @endif
        </div>

        @if($isHarian)
        <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-3 text-xs text-orange-700">
            <i class="fa fa-info-circle mr-1"></i>
            <strong>Pinjaman Tenor {{ $pinjaman->tenor_bulan }} Hari:</strong>
            Bayar bunga untuk memperpanjang jatuh tempo {{ $pinjaman->tenor_bulan }} hari ke depan —
            periode baru akan kena bunga + pokok lagi.
            Bayar lunas (pokok + bunga) untuk menutup pinjaman sepenuhnya.
        </div>
        @endif
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
            <h2 class="text-base font-semibold text-gray-800">
                @if($isHarian)
                    Form Pembayaran Pinjaman
                @else
                    Form Pembayaran Angsuran ke-{{ $angsuranKe }}
                @endif
            </h2>
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
                <select name="jenis_pembayaran" id="jenis_pembayaran"
                        onchange="handleJenis(this.value)"
                        class="w-full border {{ $errors->has('jenis_pembayaran') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                               rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">-- Pilih Jenis --</option>
                    @if($isHarian)
                        <option value="bayar_bunga_saja" {{ old('jenis_pembayaran') == 'bayar_bunga_saja' ? 'selected' : '' }}>
                            Bayar Bunga — Rp {{ number_format($pinjaman->total_bunga, 0, ',', '.') }}
                            (Jatuh tempo mundur {{ $pinjaman->tenor_bulan }} hari)
                        </option>
                        <option value="bayar_lunas" {{ old('jenis_pembayaran') == 'bayar_lunas' ? 'selected' : '' }}>
                            Bayar Lunas — Rp {{ number_format($pinjaman->jumlah_pinjaman + $pinjaman->total_bunga, 0, ',', '.') }}
                            (Pokok + Bunga, pinjaman selesai)
                        </option>
                        <option value="tidak_bayar" {{ old('jenis_pembayaran') == 'tidak_bayar' ? 'selected' : '' }}>
                            Tidak Bayar (Catat Tunggakan)
                        </option>
                    @else
                        <option value="cicilan_normal" {{ old('jenis_pembayaran') == 'cicilan_normal' ? 'selected' : '' }}>
                            Cicilan Normal — Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}
                        </option>
                        <option value="bayar_lunas" {{ old('jenis_pembayaran') == 'bayar_lunas' ? 'selected' : '' }}>
                            Bayar Lunas — Rp {{ number_format($sisaHutang, 0, ',', '.') }}
                        </option>
                        <option value="bayar_bunga_saja" {{ old('jenis_pembayaran') == 'bayar_bunga_saja' ? 'selected' : '' }}>
                            Bayar Bunga Saja
                        </option>
                        <option value="tidak_bayar" {{ old('jenis_pembayaran') == 'tidak_bayar' ? 'selected' : '' }}>
                            Tidak Bayar (Catat Tunggakan)
                        </option>
                    @endif
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

            {{-- DENDA SECTION --}}
            @if($adaDenda)
            <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm font-semibold text-red-700">
                            <i class="fa fa-triangle-exclamation mr-1"></i> Denda Keterlambatan
                        </p>
                        <p class="text-xs text-red-500 mt-0.5">
                            {{ $infoDenda['hari'] }} hari × Rp 50.000 =
                            <strong>Rp {{ number_format($infoDenda['denda'], 0, ',', '.') }}</strong>
                        </p>
                    </div>
                    <span class="text-lg font-bold text-red-600">
                        Rp {{ number_format($infoDenda['denda'], 0, ',', '.') }}
                    </span>
                </div>
                <label class="flex items-center gap-3 cursor-pointer select-none group">
                    <div class="relative">
                        <input type="checkbox"
                               name="denda_diwaive"
                               id="denda_diwaive"
                               value="1"
                               onchange="toggleWaive(this.checked)"
                               {{ old('denda_diwaive') ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-checked:bg-emerald-500 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Bebaskan / Waive Denda</p>
                        <p class="text-xs text-gray-400">Centang jika denda dibebaskan atas persetujuan</p>
                    </div>
                </label>
                <div id="alasanWaiveWrap" class="hidden mt-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Alasan Pembebasan Denda <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="alasan_waive"
                           value="{{ old('alasan_waive') }}"
                           placeholder="Contoh: Dibebaskan atas persetujuan pimpinan"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>
            @else
            <div class="mb-5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 flex items-center gap-2 text-sm text-emerald-700">
                <i class="fa fa-circle-check"></i>
                <span>Tidak ada denda keterlambatan</span>
            </div>
            @endif

            {{-- Keterangan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                <input type="text" name="keterangan"
                       value="{{ old('keterangan') }}"
                       placeholder="Catatan tambahan..."
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            {{-- Upload Bukti --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Bukti Pembayaran <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <div id="dropArea"
                     onclick="document.getElementById('buktiInput').click()"
                     class="relative cursor-pointer border-2 border-dashed rounded-xl p-5 text-center transition
                            {{ $errors->has('bukti_pembayaran') ? 'border-red-400 bg-red-50' : 'border-gray-300 hover:border-emerald-400 hover:bg-emerald-50/30' }}">
                    <div id="buktiPlaceholder">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fa fa-cloud-upload-alt text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500">Klik atau drag foto bukti transfer ke sini</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP · Maks 3 MB</p>
                    </div>
                    <div id="buktiPreviewWrap" class="hidden">
                        <img id="buktiPreview" src="" alt="Preview"
                             class="mx-auto max-h-48 rounded-lg object-contain border border-gray-200 shadow-sm">
                        <p id="buktiFileName" class="text-xs text-gray-500 mt-2"></p>
                        <button type="button" onclick="hapusBukti(event)"
                                class="mt-2 text-xs text-red-500 hover:text-red-700 underline">
                            <i class="fa fa-times mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
                <input type="file" id="buktiInput" name="bukti_pembayaran" accept="image/*" class="hidden">
                @error('bukti_pembayaran')
                    <p class="text-red-500 text-xs mt-1"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            {{-- Ringkasan --}}
            <div id="ringkasan" class="hidden mb-5 rounded-xl p-4 text-sm border">
                <h4 class="font-semibold mb-3" id="ring_judul">✅ Ringkasan Pembayaran</h4>
                <div class="space-y-1.5 text-gray-700" id="ring_detail"></div>
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
const isHarian        = {{ $isHarian ? 'true' : 'false' }};
const cicilan         = {{ $pinjaman->cicilan_per_bulan }};
const sisaHutang      = {{ $sisaHutang }};
const bungaFlat       = {{ $pinjaman->total_bunga }};
const pokok           = {{ $pinjaman->jumlah_pinjaman }};
const tenorHari       = {{ $pinjaman->tenor_bulan }};
const bungaPerBulan   = {{ $pinjaman->total_bunga / $pinjaman->tenor_bulan }};
const dendaJumlah     = {{ $infoDenda['denda'] }};
const dendaHari       = {{ $infoDenda['hari'] }};
// Bunga periode baru setelah bayar bunga (dihitung ulang dari pokok)
const bungaBaru       = {{ $bungaBaru }};
const totalBaru       = {{ $totalPinjamanBaru }};
const jatuhTempoMundur = '{{ $jatuhTempoMundur->translatedFormat("d F Y") }}';

const fmt = v => 'Rp ' + Math.round(v).toLocaleString('id-ID');

function toggleWaive(checked) {
    const wrap = document.getElementById('alasanWaiveWrap');
    if (wrap) wrap.classList.toggle('hidden', !checked);
    updateRingkasan();
}

function handleJenis(val) {
    const input = document.getElementById('jumlah_dibayar');
    if (isHarian) {
        if (val === 'bayar_bunga_saja') input.value = Math.round(bungaFlat);
        if (val === 'bayar_lunas')      input.value = Math.round(pokok + bungaFlat);
        if (val === 'tidak_bayar')      input.value = 0;
    } else {
        if (val === 'cicilan_normal')   input.value = Math.round(cicilan);
        if (val === 'bayar_lunas')      input.value = Math.round(sisaHutang);
        if (val === 'bayar_bunga_saja') input.value = Math.round(bungaPerBulan);
        if (val === 'tidak_bayar')      input.value = 0;
    }
    updateRingkasan(val);
}

function updateRingkasan(val) {
    val = val || document.getElementById('jenis_pembayaran').value;
    const bayar      = parseFloat(document.getElementById('jumlah_dibayar').value) || 0;
    const waiveEl    = document.getElementById('denda_diwaive');
    const isWaive    = waiveEl ? waiveEl.checked : false;
    const dendaAktif = isWaive ? 0 : dendaJumlah;

    if (!val || bayar <= 0) {
        document.getElementById('ringkasan').classList.add('hidden');
        return;
    }

    let html  = '';
    let judul = '✅ Ringkasan Pembayaran';
    let warna = 'bg-emerald-50 border-emerald-200 text-emerald-700';

    if (isHarian) {
        if (val === 'bayar_bunga_saja') {
            judul = '🔄 Bayar Bunga — Jatuh Tempo Diperpanjang';
            warna = 'bg-orange-50 border-orange-200 text-orange-700';
            html  = `
                <div class="flex justify-between"><span>Bunga periode ini</span><span>${fmt(bungaFlat)}</span></div>
                <div class="flex justify-between text-gray-500"><span>Pokok (tetap, tidak berkurang)</span><span>${fmt(pokok)}</span></div>
                ${dendaAktif > 0 ? `<div class="flex justify-between text-red-600"><span>Denda (${dendaHari} hari × Rp 50.000)</span><span class="font-bold">${fmt(dendaAktif)}</span></div>` : ''}
                ${isWaive ? `<div class="flex justify-between text-emerald-600"><span>Denda dibebaskan</span><span class="font-bold">✓ Waived</span></div>` : ''}
                <div class="flex justify-between border-t border-orange-200 pt-2 mt-2 font-bold text-base">
                    <span>Total dibayar sekarang</span><span>${fmt(bungaFlat + dendaAktif)}</span>
                </div>
                <div class="mt-3 pt-2 border-t border-orange-100 text-xs space-y-1 text-orange-800">
                    <p class="font-semibold">📅 Periode baru setelah ini:</p>
                    <div class="flex justify-between"><span>Jatuh tempo baru</span><span class="font-medium">${jatuhTempoMundur}</span></div>
                    <div class="flex justify-between"><span>Bunga periode baru</span><span class="font-medium">${fmt(bungaBaru)}</span></div>
                    <div class="flex justify-between"><span>Total tagihan periode baru</span><span class="font-medium">${fmt(totalBaru)}</span></div>
                </div>
            `;
        } else if (val === 'bayar_lunas') {
            judul = '✅ Bayar Lunas — Pinjaman Selesai';
            html  = `
                <div class="flex justify-between"><span>Pokok</span><span>${fmt(pokok)}</span></div>
                <div class="flex justify-between"><span>Bunga</span><span>${fmt(bungaFlat)}</span></div>
                ${dendaAktif > 0 ? `<div class="flex justify-between text-red-600"><span>Denda (${dendaHari} hari × Rp 50.000)</span><span class="font-bold">${fmt(dendaAktif)}</span></div>` : ''}
                ${isWaive ? `<div class="flex justify-between text-emerald-600"><span>Denda dibebaskan</span><span class="font-bold">✓ Waived</span></div>` : ''}
                <div class="flex justify-between border-t border-emerald-200 pt-2 mt-2 font-bold text-base">
                    <span>Total dibayar</span><span>${fmt(pokok + bungaFlat + dendaAktif)}</span>
                </div>
                <div class="flex justify-between text-xs mt-1 opacity-70"><span>Status pinjaman</span><span>LUNAS 🎉</span></div>
            `;
        } else if (val === 'tidak_bayar') {
            judul = '⚠️ Catat Tunggakan';
            warna = 'bg-red-50 border-red-200 text-red-700';
            html  = `<div class="flex justify-between"><span>Status</span><span class="font-bold">Tunggakan dicatat</span></div>`;
        }
    } else {
        const sisa = Math.max(0, sisaHutang - bayar);
        if (val === 'bayar_bunga_saja') {
            judul = '🔄 Bayar Bunga Saja';
            warna = 'bg-orange-50 border-orange-200 text-orange-700';
        } else if (val === 'bayar_lunas') {
            judul = '✅ Bayar Lunas — Pinjaman Selesai';
        } else if (val === 'tidak_bayar') {
            judul = '⚠️ Catat Tunggakan';
            warna = 'bg-red-50 border-red-200 text-red-700';
        }
        html = `
            <div class="flex justify-between"><span>Jumlah Dibayar</span><span>${fmt(bayar)}</span></div>
            ${dendaAktif > 0 ? `<div class="flex justify-between text-red-600"><span>Denda (${dendaHari} hari × Rp 50.000)</span><span class="font-bold">${fmt(dendaAktif)}</span></div>` : ''}
            ${isWaive ? `<div class="flex justify-between text-emerald-600"><span>Denda dibebaskan</span><span class="font-bold">✓ Waived</span></div>` : ''}
            <div class="flex justify-between border-t border-gray-200 pt-2 mt-2 font-bold text-base">
                <span>Total yang harus dibayar</span><span>${fmt(bayar + dendaAktif)}</span>
            </div>
            <div class="flex justify-between text-xs mt-1 opacity-70"><span>Sisa hutang setelah bayar</span><span class="text-red-500">${fmt(sisa)}</span></div>
        `;
    }

    const el = document.getElementById('ringkasan');
    el.className = `mb-5 rounded-xl p-4 text-sm border ${warna}`;
    document.getElementById('ring_judul').textContent = judul;
    document.getElementById('ring_detail').innerHTML  = html;
    el.classList.remove('hidden');
}

document.getElementById('jumlah_dibayar').addEventListener('input', () => updateRingkasan());

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

const buktiInput       = document.getElementById('buktiInput');
const buktiPreview     = document.getElementById('buktiPreview');
const buktiPreviewWrap = document.getElementById('buktiPreviewWrap');
const buktiPlaceholder = document.getElementById('buktiPlaceholder');
const buktiFileName    = document.getElementById('buktiFileName');

buktiInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    buktiPreview.src          = URL.createObjectURL(file);
    buktiFileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    buktiPlaceholder.classList.add('hidden');
    buktiPreviewWrap.classList.remove('hidden');
});

function hapusBukti(e) {
    e.stopPropagation();
    buktiInput.value          = '';
    buktiPreview.src          = '';
    buktiFileName.textContent = '';
    buktiPlaceholder.classList.remove('hidden');
    buktiPreviewWrap.classList.add('hidden');
}

const dropArea = document.getElementById('dropArea');
dropArea.addEventListener('dragover',  e => { e.preventDefault(); dropArea.classList.add('border-emerald-400', 'bg-emerald-50'); });
dropArea.addEventListener('dragleave', () => dropArea.classList.remove('border-emerald-400', 'bg-emerald-50'));
dropArea.addEventListener('drop', e => {
    e.preventDefault();
    dropArea.classList.remove('border-emerald-400', 'bg-emerald-50');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        buktiInput.files = dt.files;
        buktiInput.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection