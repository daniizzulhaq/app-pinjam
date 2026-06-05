{{-- ============================================================ --}}
{{-- FILE: resources/views/karyawan/pinjaman/show.blade.php     --}}
{{-- ============================================================ --}}
@extends('layouts.karyawan')
@section('title', 'Detail Pinjaman #' . $pinjaman->no_pinjaman)
@section('page-title', 'Detail Pinjaman')

@section('content')

@php
    $tipe       = $pinjaman->tenor_tipe ?? 'bulanan';
    $isHarian   = $tipe === 'harian';
    $satuan     = $isHarian ? 'hari' : 'bulan';

    $totalBayar = $pinjaman->pembayaran->sum('jumlah_dibayar');
    $sisaHutang = $pinjaman->total_pinjaman - $totalBayar;
    $persen     = $pinjaman->total_pinjaman > 0
        ? min(100, round($totalBayar / $pinjaman->total_pinjaman * 100))
        : 0;
    $angsuranKe = $pinjaman->pembayaran->count() + 1;
    $sudahLunas = $pinjaman->status === 'lunas';

    $statusLabel = [
        'menunggu_approval'          => ['label' => 'Menunggu Approval',          'class' => 'bg-yellow-100 text-yellow-700'],
        'menunggu_transfer_karyawan' => ['label' => 'Menunggu Anda Transfer',     'class' => 'bg-blue-100 text-blue-700'],
        'menunggu_konfirmasi'        => ['label' => 'Menunggu Konfirmasi Admin',  'class' => 'bg-purple-100 text-purple-700'],
        'aktif'                      => ['label' => 'Aktif',                      'class' => 'bg-green-100 text-green-700'],
        'lunas'                      => ['label' => 'Lunas',                      'class' => 'bg-emerald-100 text-emerald-700'],
        'ditolak'                    => ['label' => 'Ditolak',                    'class' => 'bg-red-100 text-red-700'],
    ];
    $badge = $statusLabel[$pinjaman->status] ?? ['label' => $pinjaman->status, 'class' => 'bg-gray-100 text-gray-600'];
@endphp

<div class="py-4 max-w-5xl">

    <div class="mb-4">
        <a href="{{ route('karyawan.pinjaman.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke daftar pinjaman
        </a>
    </div>

    {{-- Header --}}
    <div class="bg-white rounded-2xl shadow p-6 mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <code class="text-sm bg-gray-100 px-3 py-1 rounded-full text-blue-600 font-mono">
                        {{ $pinjaman->no_pinjaman }}
                    </code>
                    @if($isHarian)
                        <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-700 text-xs font-semibold px-2 py-1 rounded-full">
                            <i class="fa fa-sun-o"></i> Harian
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full {{ $badge['class'] }}">
                        {{ $badge['label'] }}
                    </span>
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ $pinjaman->nasabah->nama_lengkap }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Pengajuan {{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->translatedFormat('d F Y') }}
                </p>
            </div>

            @if(!$sudahLunas && $pinjaman->status === 'aktif')
                <a href="{{ route('karyawan.pembayaran.create', $pinjaman) }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm whitespace-nowrap">
                    <i class="fa fa-plus"></i> Input Pembayaran
                </a>
            @endif
        </div>

        @if(in_array($pinjaman->status, ['aktif', 'lunas']))
        <div class="mt-5">
            <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                <span>Progress Pelunasan</span>
                <span class="font-semibold text-gray-600">{{ $persen }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-700
                    {{ $persen >= 100 ? 'bg-emerald-500' : ($persen >= 50 ? 'bg-blue-500' : 'bg-amber-400') }}"
                    style="width: {{ $persen }}%"></div>
            </div>
            <div class="flex justify-between text-xs mt-1.5">
                <span class="text-emerald-600 font-medium">Terbayar: Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                <span class="text-red-500 font-medium">Sisa: Rp {{ number_format($sisaHutang, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- ALUR BUKTI TRANSFER (tampil saat belum aktif)               --}}
    {{-- ============================================================ --}}
    @if(!in_array($pinjaman->status, ['aktif', 'lunas', 'ditolak']))
    <div class="space-y-4 mb-5">

        {{-- STEP 1: Menunggu bukti transfer dari admin --}}
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $pinjaman->bukti_transfer_admin ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">1</div>
                <h3 class="font-semibold text-gray-700">Transfer dari Admin</h3>
                @if($pinjaman->bukti_transfer_admin)
                    <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                        <i class="fa fa-check mr-1"></i>Diterima
                    </span>
                @endif
            </div>

            @if($pinjaman->bukti_transfer_admin)
                <p class="text-xs text-gray-400 mb-2">
                    Admin mengirim bukti transfer pada {{ $pinjaman->tgl_transfer_admin?->format('d/m/Y H:i') }}
                </p>
                <a href="{{ asset('storage/'.$pinjaman->bukti_transfer_admin) }}" target="_blank">
                    <img src="{{ asset('storage/'.$pinjaman->bukti_transfer_admin) }}"
                         alt="Bukti Transfer Admin"
                         class="w-48 h-48 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition cursor-pointer">
                </a>
                <p class="text-xs text-blue-500 mt-1">Klik gambar untuk lihat penuh</p>
            @else
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-700">
                    <i class="fa fa-clock text-amber-400 text-xl"></i>
                    <span>Menunggu admin mengirimkan dana dan bukti transfer.</span>
                </div>
            @endif
        </div>

        {{-- STEP 2: Karyawan upload bukti transfer ke nasabah --}}
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $pinjaman->bukti_transfer_karyawan ? 'bg-purple-600 text-white' : ($pinjaman->status === 'menunggu_transfer_karyawan' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500') }}">2</div>
                <h3 class="font-semibold text-gray-700">Transfer ke Nasabah</h3>
                @if($pinjaman->bukti_transfer_karyawan)
                    <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                        <i class="fa fa-check mr-1"></i>Sudah diupload
                    </span>
                @endif
            </div>

            @if($pinjaman->bukti_transfer_karyawan)
                <p class="text-xs text-gray-400 mb-2">
                    Diupload {{ $pinjaman->tgl_transfer_karyawan?->format('d/m/Y H:i') }}
                </p>
                <a href="{{ asset('storage/'.$pinjaman->bukti_transfer_karyawan) }}" target="_blank">
                    <img src="{{ asset('storage/'.$pinjaman->bukti_transfer_karyawan) }}"
                         alt="Bukti Transfer Karyawan"
                         class="w-48 h-48 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition cursor-pointer">
                </a>
                <p class="text-xs text-blue-500 mt-1">Klik gambar untuk lihat penuh</p>
                <div class="mt-3 flex items-center gap-2 bg-purple-50 border border-purple-200 rounded-lg p-3 text-sm text-purple-700">
                    <i class="fa fa-hourglass-half"></i>
                    <span>Menunggu konfirmasi admin. Pinjaman akan aktif setelah admin approve.</span>
                </div>

            @elseif($pinjaman->status === 'menunggu_transfer_karyawan')
                {{-- Form upload bukti --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-700">
                    <i class="fa fa-info-circle mr-1"></i>
                    Dana sudah dikirim admin. Silakan transfer ke nasabah
                    <strong>{{ $pinjaman->nasabah->nama_lengkap }}</strong>
                    sebesar <strong>Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</strong>,
                    lalu upload bukti transfernya.
                </div>

                <form method="POST"
                      action="{{ route('karyawan.pinjaman.upload-bukti-karyawan', $pinjaman) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center mb-3">
                        <input type="file" name="bukti_transfer_karyawan" id="bukti_karyawan"
                               accept="image/*" class="hidden"
                               onchange="previewImage(this, 'preview-karyawan')">
                        <label for="bukti_karyawan" class="cursor-pointer">
                            <i class="fa fa-cloud-upload text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-sm text-gray-500">Klik untuk pilih bukti transfer</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — maks 2MB</p>
                        </label>
                        <img id="preview-karyawan" class="hidden mx-auto mt-3 w-40 h-40 object-cover rounded-lg border">
                    </div>
                    @error('bukti_transfer_karyawan')
                        <p class="text-red-500 text-xs mb-2"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fa fa-upload mr-1"></i> Upload Bukti Transfer ke Nasabah
                    </button>
                </form>
            @else
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 text-sm text-gray-500">
                    <i class="fa fa-lock text-gray-300 text-xl"></i>
                    <span>Tersedia setelah admin mengirimkan dana.</span>
                </div>
            @endif
        </div>

    </div>
    @endif

    {{-- Konten aktif: Stats + Tabs --}}
    @if(in_array($pinjaman->status, ['aktif', 'lunas']))

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        @php
            $stats = [
                ['label'=>'Pinjaman Pokok',            'value'=>'Rp '.number_format($pinjaman->jumlah_pinjaman, 0, ',', '.'),   'icon'=>'fa-money-bill-wave',      'color'=>'text-gray-700'],
                ['label'=>'Total + Bunga',             'value'=>'Rp '.number_format($pinjaman->total_pinjaman, 0, ',', '.'),    'icon'=>'fa-circle-dollar-to-slot', 'color'=>'text-blue-600'],
                ['label'=>'Cicilan / '.ucfirst($satuan),'value'=>'Rp '.number_format($pinjaman->cicilan_per_bulan, 0, ',', '.'), 'icon'=>'fa-calendar-check',       'color'=>'text-emerald-600'],
                ['label'=>'Tenor',                     'value'=>$pinjaman->tenor_bulan.' '.ucfirst($satuan),                   'icon'=>'fa-hourglass-half',        'color'=>'text-amber-600'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa {{ $s['icon'] }} text-gray-300 text-xs"></i>
                <p class="text-xs text-gray-400">{{ $s['label'] }}</p>
            </div>
            <p class="font-bold {{ $s['color'] }} text-sm">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'pembayaran' }">

        <div class="flex gap-1 bg-white rounded-xl shadow p-1 mb-5 w-fit">
            <button @click="tab='pembayaran'"
                    :class="tab==='pembayaran' ? 'bg-emerald-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fa fa-receipt mr-1"></i> Riwayat Pembayaran ({{ $pinjaman->pembayaran->count() }})
            </button>
            <button @click="tab='detail'"
                    :class="tab==='detail' ? 'bg-emerald-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fa fa-info-circle mr-1"></i> Detail Pinjaman
            </button>
            <button @click="tab='jadwal'"
                    :class="tab==='jadwal' ? 'bg-emerald-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fa fa-calendar mr-1"></i> Jadwal Angsuran
            </button>
        </div>

        {{-- Tab: Riwayat Pembayaran --}}
        <div x-show="tab==='pembayaran'" x-transition>
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800">Riwayat Pembayaran</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $pinjaman->pembayaran->count() }} dari {{ $pinjaman->tenor_bulan }} angsuran
                        </p>
                    </div>
                    @if(!$sudahLunas && $pinjaman->status === 'aktif')
                        <a href="{{ route('karyawan.pembayaran.create', $pinjaman) }}"
                           class="inline-flex items-center gap-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-4 py-2 rounded-lg text-xs font-semibold transition">
                            <i class="fa fa-plus"></i> Catat Angsuran ke-{{ $angsuranKe }}
                        </a>
                    @endif
                </div>

                @if($pinjaman->pembayaran->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Ke</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tgl Bayar</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Dibayar</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Denda</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($pinjaman->pembayaran->sortByDesc('angsuran_ke') as $p)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                        {{ $p->status === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}
                                        text-xs font-bold">{{ $p->angsuran_ke }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-700">
                                    {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') }}
                                    @if($p->hari_terlambat > 0)
                                        <span class="block text-xs text-red-400">Terlambat {{ $p->hari_terlambat }} hari</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $jenisMap = [
                                            'cicilan_normal'   => ['label'=>'Cicilan Normal','class'=>'bg-blue-50 text-blue-700'],
                                            'bayar_lunas'      => ['label'=>'Bayar Lunas',   'class'=>'bg-emerald-50 text-emerald-700'],
                                            'bayar_bunga_saja' => ['label'=>'Bunga Saja',    'class'=>'bg-purple-50 text-purple-700'],
                                            'tidak_bayar'      => ['label'=>'Tidak Bayar',   'class'=>'bg-red-50 text-red-600'],
                                        ];
                                        $jenis = $jenisMap[$p->jenis_pembayaran] ?? ['label'=>$p->jenis_pembayaran,'class'=>'bg-gray-100 text-gray-600'];
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium {{ $jenis['class'] }}">
                                        {{ $jenis['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right font-semibold {{ $p->jenis_pembayaran === 'tidak_bayar' ? 'text-red-500' : 'text-gray-800' }}">
                                    Rp {{ number_format($p->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if($p->denda > 0)
                                        <span class="text-red-500 font-medium">Rp {{ number_format($p->denda, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($p->status === 'lunas')
                                        <span class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded-full">Lunas</span>
                                    @else
                                        <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded-full">Sebagian</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('karyawan.pembayaran.invoice', [$pinjaman, $p]) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1 rounded-lg text-xs font-semibold transition">
                                        <i class="fa fa-file-invoice text-[11px]"></i> Invoice
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 border-t-2 border-gray-200">
                                <td colspan="3" class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Total</td>
                                <td class="px-5 py-3 text-right font-bold text-emerald-700">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right font-bold text-red-500">Rp {{ number_format($pinjaman->pembayaran->sum('denda'), 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="py-16 text-center">
                    <i class="fa fa-receipt text-gray-300 text-4xl mb-3 block"></i>
                    <p class="text-gray-500 font-medium">Belum ada riwayat pembayaran</p>
                    @if($pinjaman->status === 'aktif')
                        <a href="{{ route('karyawan.pembayaran.create', $pinjaman) }}"
                           class="inline-flex items-center gap-2 mt-4 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">
                            <i class="fa fa-plus"></i> Catat Pembayaran Pertama
                        </a>
                    @endif
                </div>
                @endif
            </div>

            @if($sudahLunas)
            <div class="mt-4 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start gap-3">
                <div class="w-9 h-9 flex-shrink-0 bg-emerald-100 rounded-full flex items-center justify-center">
                    <i class="fa fa-trophy text-emerald-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-emerald-800">Pinjaman Telah Lunas!</p>
                    <p class="text-sm text-emerald-600 mt-0.5">Seluruh kewajiban nasabah telah terpenuhi.</p>
                </div>
            </div>
            @endif

            @if(!$sudahLunas && $pinjaman->status === 'aktif')
            @php
                $start          = \Carbon\Carbon::parse($pinjaman->tanggal_mulai);
                $jatuhTempoNext = $isHarian ? $start->addDays($angsuranKe) : $start->addMonths($angsuranKe);
                $hariMenunggu   = now()->diffInDays($jatuhTempoNext, false);
            @endphp
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                <div class="w-9 h-9 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fa fa-bell text-blue-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-blue-800">
                        Angsuran ke-{{ $angsuranKe }} jatuh tempo {{ $jatuhTempoNext->translatedFormat('d F Y') }}
                    </p>
                    <p class="text-sm text-blue-600 mt-0.5">
                        @if($hariMenunggu > 0) {{ $hariMenunggu }} hari lagi &bull;
                        @elseif($hariMenunggu == 0) <span class="text-amber-600 font-medium">Hari ini!</span> &bull;
                        @else <span class="text-red-600 font-medium">Sudah lewat {{ abs($hariMenunggu) }} hari</span> &bull;
                        @endif
                        Nominal: <strong>Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}</strong> / {{ $satuan }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        {{-- Tab: Detail Pinjaman --}}
        <div x-show="tab==='detail'" x-transition>
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-5 pb-3 border-b border-gray-100">📋 Informasi Lengkap Pinjaman</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    @php
                        $tanggalSelesai = $isHarian
                            ? \Carbon\Carbon::parse($pinjaman->tanggal_mulai)->addDays($pinjaman->tenor_bulan)
                            : \Carbon\Carbon::parse($pinjaman->tanggal_mulai)->addMonths($pinjaman->tenor_bulan);
                        $fields = [
                            ['label'=>'Nama Nasabah',    'value'=>$pinjaman->nasabah->nama_lengkap],
                            ['label'=>'No. Pinjaman',    'value'=>$pinjaman->no_pinjaman, 'mono'=>true],
                            ['label'=>'Jumlah Pinjaman', 'value'=>'Rp '.number_format($pinjaman->jumlah_pinjaman, 0, ',', '.')],
                            ['label'=>'Total Bunga',     'value'=>'Rp '.number_format($pinjaman->total_bunga, 0, ',', '.')],
                            ['label'=>'Total Pinjaman',  'value'=>'Rp '.number_format($pinjaman->total_pinjaman, 0, ',', '.')],
                            ['label'=>'Cicilan/'.ucfirst($satuan), 'value'=>'Rp '.number_format($pinjaman->cicilan_per_bulan, 0, ',', '.')],
                            ['label'=>'Tenor',           'value'=>$pinjaman->tenor_bulan.' '.ucfirst($satuan).' ('.ucfirst($tipe).')'],
                            ['label'=>'Suku Bunga',      'value'=>($pinjaman->bunga_persen ?? '-').'% / bulan'],
                            ['label'=>'Tanggal Mulai',   'value'=>\Carbon\Carbon::parse($pinjaman->tanggal_mulai)->translatedFormat('d F Y')],
                            ['label'=>'Tanggal Selesai', 'value'=>$tanggalSelesai->translatedFormat('d F Y')],
                            ['label'=>'Status',          'value'=>$badge['label']],
                            ['label'=>'Catatan Pengajuan','value'=>$pinjaman->catatan_pengajuan ?? '-'],
                        ];
                    @endphp
                    @foreach($fields as $f)
                    <div class="flex flex-col">
                        <span class="text-xs text-gray-400 mb-0.5">{{ $f['label'] }}</span>
                        @if(!empty($f['mono']))
                            <code class="text-sm text-blue-600 bg-gray-50 px-2 py-0.5 rounded w-fit">{{ $f['value'] }}</code>
                        @else
                            <span class="text-sm font-semibold text-gray-800">{{ $f['value'] }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tab: Jadwal Angsuran --}}
        <div x-show="tab==='jadwal'" x-transition>
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Jadwal Angsuran</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Simulasi jadwal {{ $pinjaman->tenor_bulan }} kali angsuran ({{ $tipe }})
                    </p>
                </div>
                <div class="overflow-x-auto max-h-[520px] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-white z-10 shadow-sm">
                            <tr class="bg-gray-50 text-left border-b border-gray-100">
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Ke</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Cicilan</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Pokok</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Bunga</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $bungaPerPeriode = $pinjaman->total_bunga / $pinjaman->tenor_bulan;
                                $pokokPerPeriode = $pinjaman->jumlah_pinjaman / $pinjaman->tenor_bulan;
                                $bayarMap        = $pinjaman->pembayaran->keyBy('angsuran_ke');
                                $startDate       = \Carbon\Carbon::parse($pinjaman->tanggal_mulai);
                            @endphp
                            @for($i = 1; $i <= $pinjaman->tenor_bulan; $i++)
                                @php
                                    $tgl        = $isHarian ? (clone $startDate)->addDays($i) : (clone $startDate)->addMonths($i);
                                    $bayar      = $bayarMap->get($i);
                                    $sudahBayar = !is_null($bayar);
                                    $isNow      = !$sudahBayar && $i === $angsuranKe;
                                @endphp
                                <tr class="{{ $isNow ? 'bg-blue-50' : ($sudahBayar ? 'bg-emerald-50/40' : 'hover:bg-gray-50/60') }} transition">
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                            {{ $sudahBayar ? 'bg-emerald-100 text-emerald-700' : ($isNow ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-500') }}">
                                            {{ $i }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-700">
                                        {{ $tgl->translatedFormat('d M Y') }}
                                        @if($isNow) <span class="ml-1 text-xs text-blue-600 font-medium">← Berikutnya</span> @endif
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                        Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-right text-gray-600">Rp {{ number_format($pokokPerPeriode, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600">Rp {{ number_format($bungaPerPeriode, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3">
                                        @if($sudahBayar)
                                            <span class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                {{ $bayar->status === 'lunas' ? 'Lunas' : 'Sebagian' }}
                                            </span>
                                        @elseif($isNow)
                                            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">Menunggu</span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /x-data --}}
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection