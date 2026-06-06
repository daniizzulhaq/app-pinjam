{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/pinjaman/show.blade.php        --}}
{{-- ============================================================ --}}
@extends('layouts.admin')
@section('title', 'Detail Pinjaman')
@section('page-title', 'Detail Pinjaman')

@section('content')
@php
    $tipe     = $pinjaman->tenor_tipe ?? 'bulanan';
    $isHarian = $tipe === 'harian';
    $satuan   = $isHarian ? 'Hari' : 'Bulan';

    $totalBayar = $pinjaman->pembayaran->sum('jumlah_dibayar');
    // Harian: pokok tidak berkurang saat bayar bunga
    // Bulanan: sisa = total_pinjaman - total_dibayar
    $sisaHutang = $isHarian
        ? $pinjaman->jumlah_pinjaman
        : $pinjaman->total_pinjaman - $totalBayar;

    $statusLabel = [
        'menunggu_approval'          => ['label' => 'Menunggu Approval',           'class' => 'bg-yellow-100 text-yellow-700'],
        'menunggu_transfer_karyawan' => ['label' => 'Menunggu Transfer Karyawan',  'class' => 'bg-blue-100 text-blue-700'],
        'menunggu_konfirmasi'        => ['label' => 'Menunggu Konfirmasi Admin',   'class' => 'bg-purple-100 text-purple-700'],
        'aktif'                      => ['label' => 'Aktif',                       'class' => 'bg-green-100 text-green-700'],
        'lunas'                      => ['label' => 'Lunas',                       'class' => 'bg-emerald-100 text-emerald-700'],
        'ditolak'                    => ['label' => 'Ditolak',                     'class' => 'bg-red-100 text-red-700'],
    ];
    $badge = $statusLabel[$pinjaman->status] ?? ['label' => $pinjaman->status, 'class' => 'bg-gray-100 text-gray-600'];
@endphp

<div class="py-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Kiri: Info + Alur Bukti Transfer --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Info Pinjaman --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700 text-base">📄 Info Pinjaman</h3>
                <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-500">No. Pinjaman</dt>
                    <dd class="font-mono font-bold">{{ $pinjaman->no_pinjaman }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tgl. Pengajuan</dt>
                    <dd>{{ $pinjaman->tanggal_pengajuan->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Nasabah</dt>
                    <dd class="font-medium">{{ $pinjaman->nasabah->nama_lengkap }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Karyawan</dt>
                    <dd>{{ $pinjaman->karyawan->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Jumlah Pinjaman (Pokok)</dt>
                    <dd class="font-bold text-blue-600">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">
                        Total + Bunga
                        @if($isHarian)
                            <span class="text-xs text-orange-500">(Periode Ini)</span>
                        @endif
                    </dt>
                    <dd class="font-bold">Rp {{ number_format($pinjaman->total_pinjaman, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">
                        Bunga
                        @if($isHarian)
                            <span class="text-xs text-orange-500">(Periode Ini)</span>
                        @endif
                    </dt>
                    <dd>
                        {{ $pinjaman->bunga_persen }}%
                        @if($isHarian)
                            <span class="text-xs text-gray-400">/periode</span>
                        @else
                            <span class="text-xs text-gray-400">/bulan</span>
                        @endif
                        &nbsp;=&nbsp;
                        <span class="text-orange-600 font-semibold">Rp {{ number_format($pinjaman->total_bunga, 0, ',', '.') }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tenor</dt>
                    <dd class="flex items-center gap-1.5">
                        {{ $pinjaman->tenor_bulan }} {{ $satuan }}
                        @if($isHarian)
                            <span class="bg-orange-100 text-orange-700 text-xs px-1.5 py-0.5 rounded-full">Harian</span>
                        @else
                            <span class="bg-blue-100 text-blue-700 text-xs px-1.5 py-0.5 rounded-full">Bulanan</span>
                        @endif
                    </dd>
                </div>
                @if($pinjaman->tanggal_mulai)
                <div>
                    <dt class="text-gray-500">Tgl. Mulai</dt>
                    <dd>{{ $pinjaman->tanggal_mulai->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">
                        Jatuh Tempo
                        @if($isHarian)
                            <span class="text-xs text-orange-500">(Periode Ini)</span>
                        @endif
                    </dt>
                    {{-- Untuk harian: tanggal_jatuh_tempo diambil dari DB karena bisa berubah setelah perpanjang --}}
                    <dd class="text-red-600 font-medium">{{ $pinjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</dd>
                </div>
                @endif
            </dl>

            {{-- Info khusus harian --}}
            @if($isHarian)
            <div class="mt-4 pt-4 border-t border-gray-100 bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 text-xs text-orange-700">
                <p class="font-semibold mb-1"><i class="fa fa-info-circle mr-1"></i> Mekanisme Pinjaman Harian</p>
                <ul class="space-y-1 list-disc list-inside text-orange-600">
                    <li><strong>Bayar bunga saja</strong> → jatuh tempo diperpanjang {{ $pinjaman->tenor_bulan }} hari, bunga periode baru dihitung ulang dari pokok yang sama.</li>
                    <li><strong>Pokok tidak berkurang</strong> selama nasabah memilih bayar bunga.</li>
                    <li><strong>Bayar lunas</strong> → pokok + bunga periode ini dibayar sekaligus, pinjaman selesai.</li>
                </ul>
            </div>
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- ALUR BUKTI TRANSFER                                          --}}
        {{-- ============================================================ --}}

        {{-- STEP 1: Admin upload bukti transfer ke karyawan --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ in_array($pinjaman->status, ['menunggu_transfer_karyawan','menunggu_konfirmasi','aktif','lunas'])
                        ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">1</div>
                <h3 class="font-semibold text-gray-700 text-base">Transfer Dana ke Karyawan</h3>
                @if($pinjaman->bukti_transfer_admin)
                    <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                        <i class="fa fa-check mr-1"></i>Sudah diupload
                    </span>
                @endif
            </div>

            @if($pinjaman->bukti_transfer_admin)
                <div class="mb-3">
                    <p class="text-xs text-gray-400 mb-2">
                        Diupload {{ $pinjaman->tgl_transfer_admin?->format('d/m/Y H:i') }}
                    </p>
                    <a href="{{ asset('storage/'.$pinjaman->bukti_transfer_admin) }}" target="_blank">
                        <img src="{{ asset('storage/'.$pinjaman->bukti_transfer_admin) }}"
                             alt="Bukti Transfer Admin"
                             class="w-48 h-48 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition cursor-pointer">
                    </a>
                    <p class="text-xs text-blue-500 mt-1">Klik gambar untuk lihat penuh</p>
                </div>
            @endif

            @if($pinjaman->status === 'menunggu_approval')
                <form method="POST"
                      action="{{ route('admin.pinjaman.upload-bukti-admin', $pinjaman) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center mb-3"
                         id="dropzone-admin">
                        <input type="file" name="bukti_transfer_admin" id="bukti_admin"
                               accept="image/*" class="hidden"
                               onchange="previewImage(this, 'preview-admin')">
                        <label for="bukti_admin" class="cursor-pointer">
                            <i class="fa fa-cloud-upload text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-sm text-gray-500">Klik untuk pilih bukti transfer</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — maks 2MB</p>
                        </label>
                        <img id="preview-admin" class="hidden mx-auto mt-3 w-40 h-40 object-cover rounded-lg border">
                    </div>
                    @error('bukti_transfer_admin')
                        <p class="text-red-500 text-xs mb-2"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fa fa-paper-plane mr-1"></i> Kirim ke Karyawan
                    </button>
                </form>
            @elseif(!$pinjaman->bukti_transfer_admin)
                <p class="text-sm text-gray-400 italic">Menunggu pengajuan diproses.</p>
            @endif
        </div>

        {{-- STEP 2: Bukti transfer karyawan ke nasabah (view only untuk admin) --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ in_array($pinjaman->status, ['menunggu_konfirmasi','aktif','lunas'])
                        ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-500' }}">2</div>
                <h3 class="font-semibold text-gray-700 text-base">Bukti Transfer Karyawan ke Nasabah</h3>
                @if($pinjaman->bukti_transfer_karyawan)
                    <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                        <i class="fa fa-check mr-1"></i>Sudah diupload
                    </span>
                @endif
            </div>

            @if($pinjaman->bukti_transfer_karyawan)
                <div class="mb-3">
                    <p class="text-xs text-gray-400 mb-2">
                        Diupload oleh karyawan {{ $pinjaman->tgl_transfer_karyawan?->format('d/m/Y H:i') }}
                    </p>
                    <a href="{{ asset('storage/'.$pinjaman->bukti_transfer_karyawan) }}" target="_blank">
                        <img src="{{ asset('storage/'.$pinjaman->bukti_transfer_karyawan) }}"
                             alt="Bukti Transfer Karyawan"
                             class="w-48 h-48 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition cursor-pointer">
                    </a>
                    <p class="text-xs text-blue-500 mt-1">Klik gambar untuk lihat penuh</p>
                </div>
            @else
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 text-sm text-gray-500">
                    <i class="fa fa-clock text-gray-300 text-xl"></i>
                    <span>Menunggu karyawan upload bukti transfer ke nasabah.</span>
                </div>
            @endif
        </div>

        {{-- STEP 3: Form Approval --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ in_array($pinjaman->status, ['aktif','lunas']) ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">3</div>
                <h3 class="font-semibold text-gray-700 text-base">Konfirmasi & Approval</h3>
            </div>

            @if($pinjaman->status === 'menunggu_konfirmasi')
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 mb-4 text-xs text-emerald-700">
                    <i class="fa fa-info-circle mr-1"></i>
                    Karyawan sudah upload bukti transfer ke nasabah. Silakan verifikasi dan lakukan approval.
                </div>
                <form method="POST" action="{{ route('admin.pinjaman.approval', $pinjaman) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan_approval" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                  placeholder="Catatan approval (opsional)..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" name="action" value="disetujui"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                            <i class="fa fa-check mr-1"></i> Setujui & Aktifkan
                        </button>
                        <button type="submit" name="action" value="ditolak"
                                onclick="return confirm('Tolak pinjaman ini?')"
                                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg text-sm font-medium">
                            <i class="fa fa-times mr-1"></i> Tolak
                        </button>
                    </div>
                </form>
            @elseif($pinjaman->status === 'aktif')
                <div class="flex items-center gap-3 bg-green-50 rounded-lg p-4 text-sm text-green-700">
                    <i class="fa fa-check-circle text-green-500 text-xl"></i>
                    <div>
                        <p class="font-semibold">Pinjaman telah disetujui dan aktif</p>
                        <p class="text-xs text-green-600 mt-0.5">
                            Disetujui {{ $pinjaman->tanggal_approval?->format('d/m/Y') }}
                            @if($pinjaman->catatan_approval) · {{ $pinjaman->catatan_approval }} @endif
                        </p>
                    </div>
                </div>
            @elseif($pinjaman->status === 'ditolak')
                <div class="flex items-center gap-3 bg-red-50 rounded-lg p-4 text-sm text-red-700">
                    <i class="fa fa-times-circle text-red-400 text-xl"></i>
                    <div>
                        <p class="font-semibold">Pinjaman ditolak</p>
                        @if($pinjaman->catatan_approval)
                            <p class="text-xs mt-0.5">{{ $pinjaman->catatan_approval }}</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 text-sm text-gray-500">
                    <i class="fa fa-lock text-gray-300 text-xl"></i>
                    <span>Tersedia setelah karyawan upload bukti transfer ke nasabah.</span>
                </div>
            @endif
        </div>

        {{-- Riwayat Pembayaran --}}
        @if(in_array($pinjaman->status, ['aktif', 'lunas']))
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">💳 Riwayat Pembayaran</h3>
            @if($pinjaman->pembayaran->isEmpty())
                <p class="text-gray-400 text-sm">Belum ada pembayaran.</p>
            @else
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Ke-</th>
                        <th class="px-3 py-2 text-left">Tgl Bayar</th>
                        <th class="px-3 py-2 text-right">Dibayar</th>
                        <th class="px-3 py-2 text-center">Denda</th>
                        <th class="px-3 py-2 text-center">Jenis</th>
                        <th class="px-3 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pinjaman->pembayaran as $bayar)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-3 py-2">{{ $bayar->angsuran_ke }}</td>
                        <td class="px-3 py-2">
                            {{ $bayar->tanggal_bayar->format('d/m/Y') }}
                            @if($bayar->hari_terlambat > 0)
                                <span class="block text-xs text-red-400">Terlambat {{ $bayar->hari_terlambat }} hari</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right font-semibold {{ $bayar->jenis_pembayaran === 'tidak_bayar' ? 'text-red-500' : 'text-gray-800' }}">
                            Rp {{ number_format($bayar->jumlah_dibayar, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($bayar->denda > 0)
                                <span class="text-red-500 font-medium">Rp {{ number_format($bayar->denda, 0, ',', '.') }}</span>
                                @if($bayar->denda_diwaive)
                                    <span class="block text-xs text-emerald-500">✓ Dibebaskan</span>
                                @endif
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center">
                            @php
                                $jenisMap = [
                                    'cicilan_normal'   => ['label' => 'Cicilan Normal',    'class' => 'bg-blue-50 text-blue-700'],
                                    'bayar_lunas'      => ['label' => 'Bayar Lunas',        'class' => 'bg-emerald-50 text-emerald-700'],
                                    'bayar_bunga_saja' => ['label' => 'Bunga — Perpanjang', 'class' => 'bg-purple-50 text-purple-700'],
                                    'tidak_bayar'      => ['label' => 'Tidak Bayar',        'class' => 'bg-red-50 text-red-600'],
                                ];
                                $jenis = $jenisMap[$bayar->jenis_pembayaran]
                                    ?? ['label' => str_replace('_', ' ', $bayar->jenis_pembayaran), 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium {{ $jenis['class'] }}">
                                {{ $jenis['label'] }}
                            </span>
                            {{-- Untuk harian bayar bunga: info perpanjang --}}
                            @if($isHarian && $bayar->jenis_pembayaran === 'bayar_bunga_saja')
                                <span class="block text-xs text-orange-400 mt-0.5">
                                    <i class="fa fa-calendar-plus"></i>
                                    +{{ $pinjaman->tenor_bulan }} hari
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center">
                            @php
                                $statusBadge = match($bayar->status) {
                                    'lunas'       => ['label' => 'Lunas',       'class' => 'bg-emerald-100 text-emerald-700'],
                                    'bunga'       => ['label' => 'Bayar Bunga', 'class' => 'bg-orange-100 text-orange-700'],
                                    'cicilan'     => ['label' => 'Cicilan',     'class' => 'bg-blue-100 text-blue-700'],
                                    'tidak_bayar' => ['label' => 'Tunggakan',   'class' => 'bg-red-100 text-red-600'],
                                    default       => ['label' => 'Sebagian',    'class' => 'bg-amber-100 text-amber-700'],
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusBadge['class'] }}">
                                {{ $statusBadge['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 border-gray-200 text-xs font-semibold text-gray-500">
                        <td colspan="2" class="px-3 py-2 uppercase">Total</td>
                        <td class="px-3 py-2 text-right font-bold text-emerald-700">
                            Rp {{ number_format($totalBayar, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-center font-bold text-red-500">
                            Rp {{ number_format($pinjaman->pembayaran->sum('denda'), 0, ',', '.') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            </div>
            @endif
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

        {{-- Info Nasabah --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">👤 Info Nasabah</h3>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-gray-500 text-xs">No. KTP</dt><dd class="font-mono">{{ $pinjaman->nasabah->no_ktp }}</dd></div>
                <div><dt class="text-gray-500 text-xs">Nama</dt><dd class="font-medium">{{ $pinjaman->nasabah->nama_lengkap }}</dd></div>
                <div><dt class="text-gray-500 text-xs">Telepon</dt><dd>{{ $pinjaman->nasabah->no_telepon }}</dd></div>
                <div><dt class="text-gray-500 text-xs">Alamat</dt><dd>{{ $pinjaman->nasabah->alamat }}</dd></div>
            </dl>
        </div>

        {{-- Ringkasan Tenor --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">📊 Ringkasan Tenor</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Tipe</dt>
                    <dd>
                        @if($isHarian)
                            <span class="bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full">Harian</span>
                        @else
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">Bulanan</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Periode</dt>
                    <dd class="font-semibold">{{ $pinjaman->tenor_bulan }} {{ $satuan }}</dd>
                </div>
                @if($isHarian)
                {{-- Harian: tampil pokok & bunga periode ini secara terpisah --}}
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Pokok</dt>
                    <dd class="font-semibold text-blue-600">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Bunga Periode Ini</dt>
                    <dd class="font-semibold text-orange-500">Rp {{ number_format($pinjaman->total_bunga, 0, ',', '.') }}</dd>
                </div>
                @else
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Cicilan</dt>
                    <dd class="font-semibold text-emerald-600">
                        Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}
                        <span class="text-xs text-gray-400">/ {{ strtolower($satuan) }}</span>
                    </dd>
                </div>
                @endif

                @if(in_array($pinjaman->status, ['aktif','lunas']))
                @php
                    // Progress: harian berdasarkan jumlah cicilan bunga yang sudah dibayar
                    // Bulanan: berdasarkan total uang yang sudah masuk vs total pinjaman
                    if ($isHarian) {
                        $jumlahBayarBunga = $pinjaman->pembayaran
                            ->whereIn('jenis_pembayaran', ['bayar_bunga_saja', 'bayar_lunas'])
                            ->count();
                        $progressLabel   = $jumlahBayarBunga . 'x bayar';
                        $progressPersen  = 0; // progress bar tidak relevan untuk harian rolling
                        $sudahLunas      = $pinjaman->status === 'lunas';
                    } else {
                        $totalSudahBayar = $pinjaman->pembayaran->sum('jumlah_dibayar');
                        $sisaLunas       = max(0, $pinjaman->total_pinjaman - $totalSudahBayar);
                        $progressPersen  = $pinjaman->total_pinjaman > 0
                            ? min(100, round($totalSudahBayar / $pinjaman->total_pinjaman * 100))
                            : 0;
                        $sudahLunas = $pinjaman->status === 'lunas';
                    }
                @endphp

                @if($isHarian)
                {{-- Harian: tampilkan info jatuh tempo saat ini & jumlah perpanjang --}}
                @php
                    $jatuhTempoNow = \Carbon\Carbon::parse($pinjaman->tanggal_jatuh_tempo);
                    $hariSisaAdmin = (int) now()->startOfDay()->diffInDays($jatuhTempoNow->startOfDay(), false);
                    $jmlPerpanjang = $pinjaman->pembayaran->where('jenis_pembayaran', 'bayar_bunga_saja')->count();
                @endphp
                <div class="pt-2 border-t border-gray-100">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-400">Jatuh Tempo Sekarang</span>
                        <span class="{{ $hariSisaAdmin < 0 ? 'text-red-600 font-bold' : ($hariSisaAdmin <= 3 ? 'text-orange-500 font-semibold' : 'text-gray-600') }}">
                            {{ $jatuhTempoNow->format('d/m/Y') }}
                        </span>
                    </div>
                    @if($hariSisaAdmin < 0)
                        <p class="text-xs text-red-500 font-semibold">⚠ Terlambat {{ abs($hariSisaAdmin) }} hari</p>
                    @elseif($hariSisaAdmin == 0)
                        <p class="text-xs text-orange-500 font-semibold">⚠ Jatuh tempo hari ini</p>
                    @else
                        <p class="text-xs text-gray-400">Sisa {{ $hariSisaAdmin }} hari</p>
                    @endif
                    @if($jmlPerpanjang > 0)
                        <p class="text-xs text-purple-500 mt-1">
                            <i class="fa fa-calendar-plus"></i>
                            Sudah diperpanjang {{ $jmlPerpanjang }}x ({{ $jmlPerpanjang * $pinjaman->tenor_bulan }} hari)
                        </p>
                    @endif
                    @if($sudahLunas)
                        <p class="text-xs text-emerald-600 font-semibold mt-1">✓ Pinjaman Lunas</p>
                    @endif
                </div>

                @else
                {{-- Bulanan: progress bar --}}
                <div>
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Progress Lunas</span>
                        <span>{{ $progressPersen }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $progressPersen >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}"
                             style="width: {{ $progressPersen }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs mt-1">
                        <span class="text-emerald-600">Bayar: Rp {{ number_format($totalSudahBayar, 0, ',', '.') }}</span>
                        <span class="text-red-500">Sisa: Rp {{ number_format($sisaLunas, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif
                @endif

            </dl>
        </div>

        {{-- Timeline Status --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">🔄 Alur Status</h3>
            @php
                $steps = [
                    ['status' => 'menunggu_approval',          'label' => 'Pengajuan masuk'],
                    ['status' => 'menunggu_transfer_karyawan', 'label' => 'Admin kirim bukti transfer'],
                    ['status' => 'menunggu_konfirmasi',        'label' => 'Karyawan transfer ke nasabah'],
                    ['status' => 'aktif',                      'label' => 'Admin approval → Aktif'],
                ];
                $currentIndex = array_search($pinjaman->status, array_column($steps, 'status'));
                if ($pinjaman->status === 'lunas') $currentIndex = 3;
            @endphp
            <ol class="space-y-3">
                @foreach($steps as $i => $step)
                @php
                    $done    = $currentIndex !== false && $i <= $currentIndex;
                    $current = $currentIndex !== false && $i === $currentIndex;
                @endphp
                <li class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs flex-shrink-0
                        {{ $done ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        @if($done) <i class="fa fa-check text-[10px]"></i>
                        @else {{ $i + 1 }}
                        @endif
                    </div>
                    <span class="text-sm {{ $current ? 'font-semibold text-gray-800' : ($done ? 'text-gray-600' : 'text-gray-400') }}">
                        {{ $step['label'] }}
                    </span>
                </li>
                @endforeach
            </ol>

            {{-- Khusus harian: riwayat perpanjang jatuh tempo --}}
            @if($isHarian && in_array($pinjaman->status, ['aktif','lunas']))
            @php
                $riwayatPerpanjang = $pinjaman->pembayaran
                    ->where('jenis_pembayaran', 'bayar_bunga_saja')
                    ->sortBy('tanggal_bayar');
            @endphp
            @if($riwayatPerpanjang->count() > 0)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">
                    <i class="fa fa-calendar-plus mr-1 text-purple-400"></i> Riwayat Perpanjang
                </p>
                <ol class="space-y-1.5">
                    @foreach($riwayatPerpanjang as $rp)
                    <li class="flex items-center gap-2 text-xs text-gray-500">
                        <span class="w-5 h-5 flex-shrink-0 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-[10px] font-bold">
                            {{ $loop->iteration }}
                        </span>
                        <span>
                            {{ $rp->tanggal_bayar->format('d/m/Y') }} —
                            <span class="text-orange-500">+{{ $pinjaman->tenor_bulan }} hari</span>
                        </span>
                    </li>
                    @endforeach
                </ol>
            </div>
            @endif
            @endif
        </div>

    </div>

</div>

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