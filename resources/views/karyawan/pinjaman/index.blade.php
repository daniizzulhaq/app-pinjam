@extends('layouts.karyawan')
@section('title', 'Data Pinjaman')
@section('page-title', 'Data Pinjaman')

@section('content')
<div class="py-4">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
        <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- FILTER & HEADER --}}
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <select name="status"
                    onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 flex-1 sm:flex-none">
                <option value="">Semua Status</option>
                <option value="menunggu_approval" {{ request('status') == 'menunggu_approval' ? 'selected' : '' }}>Menunggu Approval</option>
                <option value="aktif"             {{ request('status') == 'aktif'             ? 'selected' : '' }}>Aktif</option>
                <option value="lunas"             {{ request('status') == 'lunas'             ? 'selected' : '' }}>Lunas</option>
                <option value="ditolak"           {{ request('status') == 'ditolak'           ? 'selected' : '' }}>Ditolak</option>
            </select>
            @if(request('status'))
            <a href="{{ route('karyawan.pinjaman.index') }}"
               class="bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm text-gray-500 transition flex-shrink-0">
                <i class="fa fa-times"></i>
            </a>
            @endif
        </form>
        <a href="{{ route('karyawan.pinjaman.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center flex-shrink-0">
            <i class="fa fa-plus mr-1"></i> Ajukan Pinjaman
        </a>
    </div>

    {{-- Desktop Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden hidden md:block">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">No. Pinjaman</th>
                    <th class="px-4 py-3 text-left">Nasabah</th>
                    <th class="px-4 py-3 text-right">Jumlah</th>
                    <th class="px-4 py-3 text-center">Tenor</th>
                    <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pinjaman as $p)
                @php
                    $badge = [
                        'menunggu_approval' => 'bg-yellow-100 text-yellow-700',
                        'aktif'             => 'bg-green-100 text-green-700',
                        'lunas'             => 'bg-blue-100 text-blue-700',
                        'ditolak'           => 'bg-red-100 text-red-700',
                    ][$p->status] ?? 'bg-gray-100 text-gray-600';
                    $tipe   = $p->tenor_tipe ?? 'bulanan';
                    $satuan = $tipe === 'harian' ? 'hr' : 'bln';
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ $loop->iteration + ($pinjaman->currentPage() - 1) * $pinjaman->perPage() }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs text-blue-600">{{ $p->no_pinjaman }}</span>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ \Carbon\Carbon::parse($p->tanggal_pengajuan)->format('d M Y') }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $p->nasabah->nama_lengkap ?? '-' }}</div>
                        <div class="text-xs text-gray-400 font-mono">{{ $p->nasabah->no_ktp ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                        Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-gray-700">{{ $p->tenor_bulan }} {{ $satuan }}</span>
                        @if($tipe === 'harian')
                            <span class="block text-xs text-orange-500 mt-0.5">
                                <i class="fa fa-sun-o mr-0.5"></i>Harian
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">
                        {{ $p->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d M Y') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="{{ $badge }} text-xs px-2 py-1 rounded-full capitalize">
                            {{ str_replace('_', ' ', $p->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center space-x-1">
                        <a href="{{ route('karyawan.pinjaman.show', $p) }}"
                           class="bg-sky-500 hover:bg-sky-600 text-white text-xs px-3 py-1 rounded transition">
                            <i class="fa fa-eye mr-1"></i>Detail
                        </a>
                        @if($p->status === 'aktif')
                        <a href="{{ route('karyawan.pembayaran.create', $p) }}"
                           class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs px-3 py-1 rounded transition">
                            <i class="fa fa-money-bill mr-1"></i>Bayar
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <i class="fa fa-file-invoice text-4xl mb-2 block"></i>
                        Belum ada data pinjaman.
                        <div class="mt-2">
                            <a href="{{ route('karyawan.pinjaman.create') }}"
                               class="text-emerald-600 hover:underline text-sm">
                                Ajukan pinjaman sekarang →
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t flex flex-wrap items-center justify-between gap-2 text-sm text-gray-500">
            @if($pinjaman->total() > 0)
            <span>Menampilkan {{ $pinjaman->firstItem() }}–{{ $pinjaman->lastItem() }} dari {{ $pinjaman->total() }} pinjaman</span>
            @endif
            {{ $pinjaman->withQueryString()->links() }}
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($pinjaman as $p)
        @php
            $badge = [
                'menunggu_approval' => 'bg-yellow-100 text-yellow-700',
                'aktif'             => 'bg-green-100 text-green-700',
                'lunas'             => 'bg-blue-100 text-blue-700',
                'ditolak'           => 'bg-red-100 text-red-700',
            ][$p->status] ?? 'bg-gray-100 text-gray-600';
            $tipe   = $p->tenor_tipe ?? 'bulanan';
            $satuan = $tipe === 'harian' ? 'hr' : 'bln';
        @endphp
        <div class="bg-white rounded-xl shadow p-4">
            {{-- Row 1: No pinjaman + status --}}
            <div class="flex items-start justify-between gap-2 mb-2">
                <div>
                    <span class="font-mono text-xs text-blue-600 block">{{ $p->no_pinjaman }}</span>
                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($p->tanggal_pengajuan)->format('d M Y') }}</span>
                </div>
                <span class="{{ $badge }} text-xs px-2 py-1 rounded-full capitalize flex-shrink-0">
                    {{ str_replace('_', ' ', $p->status) }}
                </span>
            </div>

            {{-- Row 2: Nasabah --}}
            <p class="font-medium text-gray-800 text-sm">{{ $p->nasabah->nama_lengkap ?? '-' }}</p>
            <p class="text-xs text-gray-400 font-mono mb-2">{{ $p->nasabah->no_ktp ?? '' }}</p>

            {{-- Row 3: Detail angka --}}
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 mb-3">
                <span>
                    <i class="fa fa-money-bill-wave mr-1 text-gray-400"></i>
                    <span class="font-medium text-gray-700 font-mono">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</span>
                </span>
                <span>
                    <i class="fa fa-clock mr-1 text-gray-400"></i>
                    {{ $p->tenor_bulan }} {{ $satuan }}
                    @if($tipe === 'harian')
                        <span class="text-orange-500 ml-0.5">· Harian</span>
                    @endif
                </span>
                <span>
                    <i class="fa fa-calendar mr-1 text-gray-400"></i>
                    {{ $p->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d M Y') : '-' }}
                </span>
            </div>

            {{-- Row 4: Aksi --}}
            <div class="flex gap-2">
                <a href="{{ route('karyawan.pinjaman.show', $p) }}"
                   class="bg-sky-500 hover:bg-sky-600 text-white text-xs px-3 py-1.5 rounded transition">
                    <i class="fa fa-eye mr-1"></i>Detail
                </a>
                @if($p->status === 'aktif')
                <a href="{{ route('karyawan.pembayaran.create', $p) }}"
                   class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs px-3 py-1.5 rounded transition">
                    <i class="fa fa-money-bill mr-1"></i>Bayar
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow text-center py-12 text-gray-400">
            <i class="fa fa-file-invoice text-4xl mb-2 block"></i>
            Belum ada data pinjaman.
            <div class="mt-2">
                <a href="{{ route('karyawan.pinjaman.create') }}" class="text-emerald-600 hover:underline text-sm">
                    Ajukan pinjaman sekarang →
                </a>
            </div>
        </div>
        @endforelse

        @if($pinjaman->total() > 0)
        <div class="bg-white rounded-xl shadow px-4 py-3 text-xs text-gray-500">
            <div class="mb-2">Menampilkan {{ $pinjaman->firstItem() }}–{{ $pinjaman->lastItem() }} dari {{ $pinjaman->total() }} pinjaman</div>
            {{ $pinjaman->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection