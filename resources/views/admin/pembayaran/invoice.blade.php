@extends('layouts.admin')
@section('title', 'Invoice Pembayaran #' . $pembayaran->no_pembayaran)
@section('page-title', 'Invoice Pembayaran')

@section('content')
@php $profil = \App\Models\ProfilAdmin::profil(); @endphp

<div class="py-4 max-w-2xl">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-5 no-print">
        <a href="{{ route('admin.pembayaran.show', $pembayaran) }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke detail pembayaran
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white
                       px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fa fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    {{-- INVOICE CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" id="invoice-area">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#0f2d6b] to-[#22529a] px-8 py-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ $profil->logo_url }}"
                         alt="Logo"
                         class="w-11 h-11 rounded-xl object-cover border-2 border-white/30 flex-shrink-0">
                    <div>
                        <div class="font-bold text-base leading-tight">{{ $profil->nama_lembaga }}</div>
                        <div class="text-white/60 text-xs tracking-wide uppercase mt-0.5">
                            {{ $profil->tagline ?? 'Sistem Informasi Keuangan' }}
                        </div>
                        @if($profil->no_telepon)
                        <div class="text-white/50 text-xs mt-0.5">
                            <i class="fa fa-phone mr-1"></i>{{ $profil->no_telepon }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-white/60 uppercase tracking-wider mb-1">Kwitansi Pembayaran</div>
                    <div class="font-mono font-bold text-lg tracking-wide">{{ $pembayaran->no_pembayaran }}</div>
                    <div class="text-xs text-white/50 mt-1">
                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Status banner --}}
        @php
            $isLunas      = $pembayaran->status === 'lunas';
            $isTidakBayar = $pembayaran->status === 'tidak_bayar';
        @endphp
        <div class="px-8 py-2.5 text-xs font-semibold flex items-center gap-2
            {{ $isLunas
                ? 'bg-emerald-50 text-emerald-700 border-b border-emerald-100'
                : ($isTidakBayar
                    ? 'bg-red-50 text-red-600 border-b border-red-100'
                    : 'bg-amber-50 text-amber-700 border-b border-amber-100') }}">
            <i class="fa {{ $isLunas ? 'fa-circle-check' : ($isTidakBayar ? 'fa-circle-xmark' : 'fa-circle-half-stroke') }}"></i>
            Status Angsuran:
            <span class="font-bold">
                {{ $isLunas ? 'LUNAS' : ($isTidakBayar ? 'TIDAK BAYAR / TUNGGAKAN' : 'SEBAGIAN') }}
            </span>
        </div>

        <div class="px-8 py-6">

            {{-- Nasabah & Pinjaman --}}
            <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-100">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">Data Nasabah</p>
                    <p class="font-bold text-gray-800 text-sm">{{ $pinjaman->nasabah->nama_lengkap }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">KTP: {{ $pinjaman->nasabah->no_ktp ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $pinjaman->nasabah->no_telepon ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">Data Pinjaman</p>
                    <code class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-mono">{{ $pinjaman->no_pinjaman }}</code>
                    <p class="text-xs text-gray-500 mt-1">Tenor: {{ $pinjaman->tenor_bulan }} bulan</p>
                    <p class="text-xs text-gray-500">Marketing: {{ $pinjaman->karyawan->name ?? '-' }}</p>
                </div>
            </div>

            {{-- Detail Angsuran --}}
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-3 font-semibold">
                Rincian Angsuran ke-{{ $pembayaran->angsuran_ke }}
            </p>

            <div class="space-y-2.5 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Jatuh Tempo</span>
                    <span class="text-gray-700">
                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_jatuh_tempo_cicilan)->translatedFormat('d F Y') }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tanggal Bayar</span>
                    <span class="font-medium text-gray-700">
                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}
                        @if($pembayaran->hari_terlambat > 0)
                            <span class="text-red-500 text-xs ml-1">(Terlambat {{ $pembayaran->hari_terlambat }} hari)</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Jenis Pembayaran</span>
                    @php
                        $jenisMap = [
                            'cicilan_normal'   => ['label' => 'Cicilan Normal',   'class' => 'bg-blue-50 text-blue-700'],
                            'bayar_lunas'      => ['label' => 'Bayar Lunas',      'class' => 'bg-emerald-50 text-emerald-700'],
                            'bayar_bunga_saja' => ['label' => 'Bayar Bunga Saja', 'class' => 'bg-purple-50 text-purple-700'],
                            'tidak_bayar'      => ['label' => 'Tidak Bayar',      'class' => 'bg-red-50 text-red-600'],
                        ];
                        $jenis = $jenisMap[$pembayaran->jenis_pembayaran]
                            ?? ['label' => $pembayaran->jenis_pembayaran, 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $jenis['class'] }}">
                        {{ $jenis['label'] }}
                    </span>
                </div>

                <div class="border-t border-dashed border-gray-200 my-1"></div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Pokok</span>
                    <span class="text-gray-700">Rp {{ number_format($pembayaran->pokok_dibayar, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Bunga</span>
                    <span class="text-gray-700">Rp {{ number_format($pembayaran->bunga_dibayar, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Denda Keterlambatan</span>
                    @if($pembayaran->denda > 0)
                        <span class="text-red-500 font-medium">Rp {{ number_format($pembayaran->denda, 0, ',', '.') }}</span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </div>
            </div>

            {{-- Total Bayar --}}
            <div class="bg-gray-50 rounded-xl px-5 py-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Total Dibayar</span>
                    <span class="text-xl font-bold text-emerald-600">
                        Rp {{ number_format($pembayaran->jumlah_dibayar + $pembayaran->denda, 0, ',', '.') }}
                    </span>
                </div>
                @if($pembayaran->keterangan)
                <p class="text-xs text-gray-400 mt-2 border-t border-gray-200 pt-2">
                    <i class="fa fa-note-sticky mr-1"></i> {{ $pembayaran->keterangan }}
                </p>
                @endif
            </div>

            {{-- Sisa Hutang --}}
            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-blue-400 mb-1">Total Pinjaman</p>
                    <p class="text-xs font-bold text-blue-700">
                        Rp {{ number_format($pinjaman->total_pinjaman, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-emerald-400 mb-1">Total Terbayar</p>
                    <p class="text-xs font-bold text-emerald-700">
                        Rp {{ number_format($totalBayarSampaiIni, 0, ',', '.') }}
                    </p>
                </div>
                <div class="rounded-xl p-3 text-center {{ $sisaSetelahBayar <= 0 ? 'bg-emerald-50' : 'bg-red-50' }}">
                    <p class="text-xs mb-1 {{ $sisaSetelahBayar <= 0 ? 'text-emerald-400' : 'text-red-400' }}">Sisa Hutang</p>
                    <p class="text-xs font-bold {{ $sisaSetelahBayar <= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                        {{ $sisaSetelahBayar <= 0 ? 'LUNAS ✓' : 'Rp ' . number_format($sisaSetelahBayar, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Rekening Pembayaran --}}
            @if($profil->rekening && count($profil->rekening) > 0)
            <div class="mb-6 rounded-xl overflow-hidden border border-blue-100">
                <div class="bg-blue-600 px-4 py-2.5">
                    <p class="text-xs text-white font-semibold uppercase tracking-wide">
                        <i class="fa fa-university mr-1.5"></i>Rekening Pembayaran
                    </p>
                </div>
                <div class="divide-y divide-blue-50">
                    @foreach($profil->rekening as $rek)
                    <div class="flex items-center gap-3 bg-blue-50 px-4 py-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-credit-card text-white text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">{{ $rek['bank'] }}</p>
                            <p class="font-bold text-gray-800 text-sm font-mono tracking-wider">{{ $rek['no_rek'] }}</p>
                            <p class="text-xs text-gray-500">a.n. {{ $rek['atas_nama'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Progress --}}
            @php
                $persen = $pinjaman->total_pinjaman > 0
                    ? min(100, round($totalBayarSampaiIni / $pinjaman->total_pinjaman * 100))
                    : 0;
            @endphp
            <div class="mb-6">
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>Progress Pelunasan</span>
                    <span class="font-semibold">
                        {{ $persen }}% (Angsuran {{ $pembayaran->angsuran_ke }} / {{ $pinjaman->tenor_bulan }})
                    </span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all
                        {{ $persen >= 100 ? 'bg-emerald-500' : ($persen >= 50 ? 'bg-blue-500' : 'bg-amber-400') }}"
                        style="width: {{ $persen }}%"></div>
                </div>
            </div>

            {{-- Footer invoice --}}
            <div class="border-t border-dashed border-gray-200 pt-5 flex items-end justify-between">
                <div>
                    <p class="text-xs text-gray-400">Dicetak oleh</p>
                    <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
                    @if($profil->alamat)
                    <p class="text-xs text-gray-400 mt-1 max-w-xs">{{ $profil->alamat }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 mb-1">Tanda Tangan Admin</p>
                    <div class="w-32 border-b border-gray-300 mt-8"></div>
                    <p class="text-xs text-gray-400 mt-1">( {{ auth()->user()->name }} )</p>
                </div>
            </div>

        </div>{{-- /px-8 --}}
    </div>{{-- /invoice-area --}}

    {{-- Aksi bawah --}}
    <div class="mt-4 flex gap-3 no-print">
        <button onclick="window.print()"
                class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700
                       text-white py-2.5 rounded-xl text-sm font-semibold transition">
            <i class="fa fa-print"></i> Cetak Invoice
        </button>
        <a href="{{ route('admin.pembayaran.show', $pembayaran) }}"
           class="flex-1 flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200
                  text-gray-700 py-2.5 rounded-xl text-sm font-semibold transition">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    #invoice-area {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>
@endsection