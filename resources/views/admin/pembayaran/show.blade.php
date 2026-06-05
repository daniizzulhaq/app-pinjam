@extends('layouts.admin')

@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')

@section('content')
<div class="py-4">

    {{-- HEADER --}}
      <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Detail Pembayaran</h2>
            <p class="text-xs text-gray-400 mt-0.5">Informasi lengkap transaksi pembayaran</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pembayaran.invoice', $pembayaran) }}"
               class="inline-flex items-center gap-1.5 text-sm bg-blue-600 hover:bg-blue-700
                      text-white px-4 py-2 rounded-lg transition font-medium">
                <i class="fa fa-file-invoice text-xs"></i> Lihat Invoice
            </a>
            <a href="{{ route('admin.pembayaran.index') }}"
               class="inline-flex items-center gap-1.5 text-sm bg-gray-100 hover:bg-gray-200
                      text-gray-600 px-4 py-2 rounded-lg transition font-medium">
                <i class="fa fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </div>

    {{-- BADGE NO PEMBAYARAN --}}
    <div class="mb-4 flex items-center gap-2">
        <span class="text-xs text-gray-400 uppercase tracking-wide">No. Pembayaran</span>
        <span class="font-mono text-sm font-semibold text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1 rounded-lg">
            {{ $pembayaran->no_pembayaran }}
        </span>
        @php
            $statusColor = match($pembayaran->status ?? 'lunas') {
                'lunas'       => 'bg-green-100 text-green-700',
                'sebagian'    => 'bg-yellow-100 text-yellow-700',
                'tidak_bayar' => 'bg-red-100 text-red-600',
                default       => 'bg-gray-100 text-gray-500',
            };
        @endphp
        <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $statusColor }}">
            {{ ucfirst(str_replace('_', ' ', $pembayaran->status ?? 'lunas')) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- KOLOM KIRI: Info Pembayaran + Bukti --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Card Info Pembayaran --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa fa-receipt text-blue-500 text-sm"></i>
                    <span class="font-semibold text-sm text-gray-700">Informasi Pembayaran</span>
                </div>
                <div class="divide-y divide-gray-50 text-sm">
                    <div class="flex px-5 py-3">
                        <span class="w-44 text-gray-400 shrink-0">Tanggal Bayar</span>
                        <span class="text-gray-800 font-medium">
                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    <div class="flex px-5 py-3">
                        <span class="w-44 text-gray-400 shrink-0">Jumlah Dibayar</span>
                        <span class="text-green-600 font-bold text-base">
                            Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}
                        </span>
                    </div>
                    @if($pembayaran->denda > 0)
                    <div class="flex px-5 py-3">
                        <span class="w-44 text-gray-400 shrink-0">Denda</span>
                        <span class="text-red-600 font-semibold">
                            Rp {{ number_format($pembayaran->denda, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    <div class="flex px-5 py-3">
                        <span class="w-44 text-gray-400 shrink-0">Metode Bayar</span>
                        <span class="text-gray-700">{{ $pembayaran->metode_bayar ?? '-' }}</span>
                    </div>
                    <div class="flex px-5 py-3">
                        <span class="w-44 text-gray-400 shrink-0">Keterangan</span>
                        <span class="text-gray-700">{{ $pembayaran->keterangan ?? '-' }}</span>
                    </div>
                    <div class="flex px-5 py-3">
                        <span class="w-44 text-gray-400 shrink-0">Dicatat oleh</span>
                        <span class="text-gray-700">{{ $pembayaran->karyawan->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Card Rincian Pelunasan --}}
            @php
                $totalPinjaman  = $pembayaran->pinjaman->total_pinjaman;
                $progress       = $totalPinjaman > 0
                    ? min(100, round(($totalBayarSampaiIni / $totalPinjaman) * 100))
                    : 0;
            @endphp
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa fa-chart-bar text-emerald-500 text-sm"></i>
                    <span class="font-semibold text-sm text-gray-700">Rincian Pelunasan</span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                        {{-- Total Pinjaman --}}
                        <div class="border border-gray-200 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-400 mb-1">Total Pinjaman</div>
                            <div class="text-sm font-bold text-gray-800">
                                Rp {{ number_format($totalPinjaman, 0, ',', '.') }}
                            </div>
                        </div>
                        {{-- Dibayar Sebelumnya --}}
                        <div class="border border-gray-200 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-400 mb-1">Dibayar Sebelumnya</div>
                            <div class="text-sm font-bold text-gray-500">
                                Rp {{ number_format($totalBayarSebelumnya, 0, ',', '.') }}
                            </div>
                        </div>
                        {{-- Dibayar Kali Ini --}}
                        <div class="border border-green-200 bg-green-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-green-500 mb-1">Dibayar Kali Ini</div>
                            <div class="text-sm font-bold text-green-700">
                                Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}
                            </div>
                        </div>
                        {{-- Sisa --}}
                        @if($sisaSetelahBayar == 0)
                        <div class="border border-green-400 bg-green-500 rounded-lg p-3 text-center">
                            <div class="text-xs text-green-100 mb-1">Sisa Setelah Bayar</div>
                            <div class="text-sm font-bold text-white flex items-center justify-center gap-1">
                                <i class="fa fa-check-circle"></i> LUNAS
                            </div>
                        </div>
                        @else
                        <div class="border border-red-200 bg-red-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-red-400 mb-1">Sisa Setelah Bayar</div>
                            <div class="text-sm font-bold text-red-600">
                                Rp {{ number_format($sisaSetelahBayar, 0, ',', '.') }}
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Progress Bar --}}
                    <div>
                        <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                            <span>Progress Pelunasan</span>
                            <span class="font-semibold {{ $progress >= 100 ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $progress }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full transition-all duration-500
                                        {{ $progress >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                                 style="width: {{ $progress }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Nasabah + Pinjaman + Bukti --}}
        <div class="flex flex-col gap-4">

            {{-- Card Nasabah --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa fa-user text-indigo-500 text-sm"></i>
                    <span class="font-semibold text-sm text-gray-700">Data Nasabah</span>
                </div>
                <div class="divide-y divide-gray-50 text-sm">
                    <div class="flex flex-col px-5 py-3 gap-0.5">
                        <span class="text-xs text-gray-400">Nama Lengkap</span>
                        <span class="font-medium text-gray-800">
                            {{ $pembayaran->pinjaman->nasabah->nama_lengkap ?? '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col px-5 py-3 gap-0.5">
                        <span class="text-xs text-gray-400">No. KTP</span>
                        <span class="font-mono text-gray-700">
                            {{ $pembayaran->pinjaman->nasabah->no_ktp ?? '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col px-5 py-3 gap-0.5">
                        <span class="text-xs text-gray-400">Telepon</span>
                        <span class="text-gray-700">
                            {{ $pembayaran->pinjaman->nasabah->telepon ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card Pinjaman --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa fa-landmark text-yellow-500 text-sm"></i>
                    <span class="font-semibold text-sm text-gray-700">Data Pinjaman</span>
                </div>
                <div class="divide-y divide-gray-50 text-sm">
                    <div class="flex flex-col px-5 py-3 gap-0.5">
                        <span class="text-xs text-gray-400">No. Pinjaman</span>
                        <span class="font-mono text-blue-600 font-semibold">
                            {{ $pembayaran->pinjaman->no_pinjaman ?? '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col px-5 py-3 gap-0.5">
                        <span class="text-xs text-gray-400">Total Pinjaman</span>
                        <span class="font-medium text-gray-800">
                            Rp {{ number_format($totalPinjaman, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex flex-col px-5 py-3 gap-0.5">
                        <span class="text-xs text-gray-400">Bunga</span>
                        <span class="text-gray-700">
                            {{ $pembayaran->pinjaman->bunga->besar_bunga ?? '-' }}%
                        </span>
                    </div>
                    <div class="flex flex-col px-5 py-3 gap-0.5">
                        <span class="text-xs text-gray-400">Tenor</span>
                        <span class="text-gray-700">
                            {{ $pembayaran->pinjaman->tenor->lama_tenor ?? '-' }} bulan
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card Bukti Pembayaran --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa fa-image text-emerald-500 text-sm"></i>
                        <span class="font-semibold text-sm text-gray-700">Bukti Transfer</span>
                    </div>
                    @if($pembayaran->bukti_pembayaran)
                        <span class="text-xs bg-emerald-50 text-emerald-600 border border-emerald-200
                                     px-2 py-0.5 rounded-full font-medium">
                            <i class="fa fa-check-circle mr-1"></i>Tersedia
                        </span>
                    @else
                        <span class="text-xs bg-yellow-50 text-yellow-600 border border-yellow-200
                                     px-2 py-0.5 rounded-full font-medium">
                            <i class="fa fa-clock mr-1"></i>Belum Diupload
                        </span>
                    @endif
                </div>

                @if($pembayaran->bukti_pembayaran)
                {{-- Ada bukti: tampilkan thumbnail + tombol aksi --}}
                <div class="p-4">
                    {{-- Thumbnail --}}
                    <div class="relative group cursor-zoom-in rounded-xl overflow-hidden border border-gray-100 bg-gray-50"
                         onclick="lihatBukti('{{ $pembayaran->bukti_url }}')">
                        <img src="{{ $pembayaran->bukti_url }}"
                             alt="Bukti Transfer"
                             class="w-full object-cover max-h-52 group-hover:scale-105
                                    transition-transform duration-300">
                        {{-- Overlay zoom hint --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition
                                    flex items-center justify-center">
                            <span class="opacity-0 group-hover:opacity-100 transition bg-white/90
                                         text-gray-700 text-xs font-medium px-3 py-1.5 rounded-full
                                         flex items-center gap-1.5 shadow">
                                <i class="fa fa-search-plus"></i> Perbesar
                            </span>
                        </div>
                    </div>

                    {{-- Info file --}}
                    <div class="mt-3 flex items-center gap-2 text-xs text-gray-400">
                        <i class="fa fa-file-image"></i>
                        <span class="truncate">{{ basename($pembayaran->bukti_pembayaran) }}</span>
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <button onclick="lihatBukti('{{ $pembayaran->bukti_url }}')"
                                class="flex items-center justify-center gap-1.5 text-xs font-medium
                                       bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200
                                       px-3 py-2 rounded-lg transition">
                            <i class="fa fa-eye"></i> Lihat
                        </button>
                        <a href="{{ $pembayaran->bukti_url }}" download target="_blank"
                           class="flex items-center justify-center gap-1.5 text-xs font-medium
                                  bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200
                                  px-3 py-2 rounded-lg transition">
                            <i class="fa fa-download"></i> Unduh
                        </a>
                    </div>

                    {{-- Waktu upload --}}
                    @if($pembayaran->updated_at)
                    <p class="mt-3 text-center text-xs text-gray-300">
                        Diupload {{ $pembayaran->updated_at->diffForHumans() }}
                    </p>
                    @endif
                </div>

                @else
                {{-- Belum ada bukti --}}
                <div class="p-6 flex flex-col items-center text-center gap-2">
                    <div class="w-14 h-14 rounded-full bg-yellow-50 border border-yellow-200
                                flex items-center justify-center mb-1">
                        <i class="fa fa-file-image text-yellow-400 text-2xl"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Bukti belum diupload</p>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Karyawan belum mengunggah bukti transfer untuk pembayaran ini.
                    </p>
                    <div class="mt-1 text-xs text-gray-300 flex items-center gap-1">
                        <i class="fa fa-info-circle"></i>
                        Bukti dapat diupload melalui halaman karyawan
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>

</div>

{{-- Modal Bukti --}}
<div id="modalBukti"
     class="fixed inset-0 bg-black/75 z-50 hidden items-center justify-center p-4"
     onclick="tutupBukti()">
    <div class="relative max-w-2xl w-full" onclick="event.stopPropagation()">

        {{-- Header modal --}}
        <div class="flex items-center justify-between bg-white/10 backdrop-blur-sm
                    rounded-t-2xl px-5 py-3 mb-0.5">
            <div class="flex items-center gap-2 text-white">
                <i class="fa fa-image text-emerald-400"></i>
                <span class="text-sm font-medium">Bukti Transfer</span>
                <span class="text-xs text-white/50 font-mono">
                    {{ $pembayaran->no_pembayaran }}
                </span>
            </div>
            <button onclick="tutupBukti()"
                    class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center
                           justify-center text-white transition">
                <i class="fa fa-times text-sm"></i>
            </button>
        </div>

        {{-- Gambar --}}
        <div class="bg-black/30 rounded-b-2xl overflow-hidden">
            <img id="modalBuktiImg" src="" alt="Bukti Transfer"
                 class="w-full object-contain max-h-[75vh]">
        </div>

        {{-- Footer modal --}}
        <div class="flex gap-2 mt-3">
            <a id="modalBuktiLink" href="" target="_blank"
               class="flex-1 flex items-center justify-center gap-2 bg-white text-gray-700 text-sm
                      font-medium py-2.5 rounded-xl hover:bg-gray-50 transition">
                <i class="fa fa-external-link-alt"></i> Buka di Tab Baru
            </a>
            <a id="modalBuktiDownload" href="" download
               class="flex-1 flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600
                      text-white text-sm font-medium py-2.5 rounded-xl transition">
                <i class="fa fa-download"></i> Unduh Bukti
            </a>
        </div>

        <p class="text-center text-white/30 text-xs mt-2">Tekan ESC atau klik di luar untuk menutup</p>
    </div>
</div>

<script>
function lihatBukti(url) {
    document.getElementById('modalBuktiImg').src          = url;
    document.getElementById('modalBuktiLink').href        = url;
    document.getElementById('modalBuktiDownload').href    = url;
    const modal = document.getElementById('modalBukti');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}
function tutupBukti() {
    const modal = document.getElementById('modalBukti');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupBukti(); });
</script>
@endsection