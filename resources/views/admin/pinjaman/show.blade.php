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
@endphp

<div class="py-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Info Pinjaman --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">📄 Info Pinjaman</h3>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-500">No. Pinjaman</dt>
                    <dd class="font-mono font-bold">{{ $pinjaman->no_pinjaman }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Status</dt>
                    <dd>{{ str_replace('_', ' ', $pinjaman->status) }}</dd>
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
                    <dt class="text-gray-500">Jumlah Pinjaman</dt>
                    <dd class="font-bold text-blue-600">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Total (+ bunga)</dt>
                    <dd class="font-bold">Rp {{ number_format($pinjaman->total_pinjaman, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Bunga</dt>
                    <dd>{{ $pinjaman->bunga_persen }}%
                        @if($isHarian)
                            <span class="text-xs text-gray-400">(≈ {{ number_format($pinjaman->bunga_persen / 30, 4) }}%/hari)</span>
                        @else
                            <span class="text-xs text-gray-400">/bulan</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tenor</dt>
                    <dd class="flex items-center gap-1.5">
                        {{ $pinjaman->tenor_bulan }} {{ $satuan }}
                        @if($isHarian)
                            <span class="bg-orange-100 text-orange-700 text-xs px-1.5 py-0.5 rounded-full">
                                <i class="fa fa-sun-o mr-0.5"></i>Harian
                            </span>
                        @else
                            <span class="bg-blue-100 text-blue-700 text-xs px-1.5 py-0.5 rounded-full">
                                <i class="fa fa-calendar mr-0.5"></i>Bulanan
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Cicilan / {{ $satuan }}</dt>
                    <dd class="font-bold">Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tgl. Pengajuan</dt>
                    <dd>{{ $pinjaman->tanggal_pengajuan->format('d/m/Y') }}</dd>
                </div>
                @if($pinjaman->tanggal_mulai)
                <div>
                    <dt class="text-gray-500">Tgl. Mulai</dt>
                    <dd>{{ $pinjaman->tanggal_mulai->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Jatuh Tempo</dt>
                    <dd class="text-red-600 font-medium">{{ $pinjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Form Approval --}}
        @if($pinjaman->status === 'menunggu_approval')
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-1 text-base">✅ Proses Approval</h3>
            @if($isHarian)
                <p class="text-xs text-orange-600 mb-4">
                    <i class="fa fa-info-circle mr-1"></i>
                    Tenor <strong>harian</strong> — jatuh tempo dihitung
                    <strong>{{ $pinjaman->tenor_bulan }} hari</strong> dari tanggal persetujuan.
                </p>
            @else
                <p class="text-xs text-blue-600 mb-4">
                    <i class="fa fa-info-circle mr-1"></i>
                    Tenor <strong>bulanan</strong> — jatuh tempo dihitung
                    <strong>{{ $pinjaman->tenor_bulan }} bulan</strong> dari tanggal persetujuan.
                </p>
            @endif
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
                        <i class="fa fa-check mr-1"></i> Setujui
                    </button>
                    <button type="submit" name="action" value="ditolak"
                            onclick="return confirm('Tolak pengajuan ini?')"
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg text-sm font-medium">
                        <i class="fa fa-times mr-1"></i> Tolak
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Riwayat Pembayaran --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">💳 Riwayat Pembayaran</h3>
            @if($pinjaman->pembayaran->isEmpty())
                <p class="text-gray-400 text-sm">Belum ada pembayaran.</p>
            @else
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
                    <tr>
                        <td class="px-3 py-2">{{ $bayar->angsuran_ke }}</td>
                        <td class="px-3 py-2">{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($bayar->jumlah_dibayar, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-center text-red-500">
                            {{ $bayar->denda > 0 ? 'Rp '.number_format($bayar->denda, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-3 py-2 text-center text-xs">
                            {{ str_replace('_', ' ', $bayar->jenis_pembayaran) }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($bayar->status === 'lunas')
                                <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded-full">Lunas</span>
                            @else
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full">Sebagian</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

    </div>

    {{-- Sidebar Info Nasabah --}}
    <div class="space-y-4">
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
                            <span class="bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full">
                                <i class="fa fa-sun-o mr-0.5"></i>Harian
                            </span>
                        @else
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">
                                <i class="fa fa-calendar mr-0.5"></i>Bulanan
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Periode</dt>
                    <dd class="font-semibold">{{ $pinjaman->tenor_bulan }} {{ $satuan }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Cicilan</dt>
                    <dd class="font-semibold text-emerald-600">
                        Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}
                        <span class="text-xs text-gray-400">/ {{ strtolower($satuan) }}</span>
                    </dd>
                </div>
                @php
                    $totalSudahBayar = $pinjaman->pembayaran->sum('jumlah_dibayar');
                    $sisaLunas       = $pinjaman->total_pinjaman - $totalSudahBayar;
                    $progressPersen  = $pinjaman->total_pinjaman > 0
                        ? min(100, round($totalSudahBayar / $pinjaman->total_pinjaman * 100))
                        : 0;
                @endphp
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
            </dl>
        </div>
    </div>

</div>
@endsection