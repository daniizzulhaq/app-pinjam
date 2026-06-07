@extends('layouts.karyawan')

@section('title', 'Daftar Denda Pinjaman')
@section('page-title', 'Daftar Pinjaman Kena Denda')

@section('content')
<div class="pt-4 pb-8">

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-xl border border-red-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fa fa-triangle-exclamation text-red-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Pinjaman Kena Denda</p>
                <p class="text-2xl font-bold text-red-600">{{ $totalPinjamanDenda }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-orange-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <i class="fa fa-money-bill-wave text-orange-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Total Denda Terakumulasi</p>
                <p class="text-xl font-bold text-orange-600">
                    Rp {{ number_format($totalDendaKeseluruhan, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-yellow-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                <i class="fa fa-calendar-xmark text-yellow-600 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-0.5">Denda Per Hari</p>
                <p class="text-xl font-bold text-yellow-600">Rp 50.000 / pinjaman</p>
            </div>
        </div>

    </div>

    {{-- INFO BANNER --}}
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-6 flex items-start gap-3">
        <i class="fa fa-circle-info text-red-400 mt-0.5 flex-shrink-0"></i>
        <p class="text-sm text-red-700 leading-relaxed">
            Denda dihitung sejak pukul <strong>17:00</strong> pada tanggal jatuh tempo.
            Setiap pinjaman yang melewati batas waktu dikenakan denda
            <strong>Rp 50.000 per hari</strong> hingga pelunasan dilakukan.
        </p>
    </div>

    {{-- SEARCH --}}
    <form method="GET" action="{{ route('karyawan.denda.index') }}" class="mb-5">
        <div class="flex gap-2">
            <div class="relative flex-1">
                <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama / NIK nasabah..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg
                              focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
            </div>
            <button type="submit"
                    class="px-4 py-2.5 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition font-medium">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('karyawan.denda.index') }}"
               class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition font-medium">
                Reset
            </a>
            @endif
        </div>
    </form>

    {{-- EMPTY STATE --}}
    @if($pinjamanDenda->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100 py-16 text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa fa-circle-check text-emerald-500 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium">
            @if(request('search'))
                Tidak ada hasil untuk "<strong>{{ request('search') }}</strong>"
            @else
                Tidak ada pinjaman yang kena denda saat ini. 🎉
            @endif
        </p>
    </div>

    @else

    {{-- ===== DESKTOP TABLE (md ke atas) ===== --}}
    <div class="hidden md:block bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Nasabah</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Pinjaman</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Jatuh Tempo</th>
                        <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Hari Terlambat</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Total Denda</th>
                        <th class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wide px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pinjamanDenda as $i => $pinjaman)
                    <tr class="hover:bg-gray-50/70 transition-colors">

                        {{-- Nomor --}}
                        <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">{{ $i + 1 }}</td>

                        {{-- Nasabah --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center
                                            justify-center text-emerald-700 font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($pinjaman->nasabah->nama_lengkap ?? 'N', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 leading-tight">
                                        {{ $pinjaman->nasabah->nama_lengkap ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        NIK: {{ $pinjaman->nasabah->no_ktp ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Nominal Pinjaman --}}
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-gray-700">
                                Rp {{ number_format($pinjaman->jumlah_pinjaman ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $pinjaman->tenor->lama_bulan ?? '-' }} bulan
                            </p>
                        </td>

                        {{-- Jatuh Tempo --}}
                        <td class="px-4 py-3.5">
                            <p class="text-gray-700 font-medium">
                                {{ \Carbon\Carbon::parse($pinjaman->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                            </p>
                            <p class="text-xs text-red-500 mt-0.5">
                                <i class="fa fa-clock mr-0.5"></i>
                                {{ \Carbon\Carbon::parse($pinjaman->tanggal_jatuh_tempo)->diffForHumans() }}
                            </p>
                        </td>

                        {{-- Hari Terlambat --}}
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $pinjaman->hari_terlambat >= 30
                                    ? 'bg-red-100 text-red-700'
                                    : ($pinjaman->hari_terlambat >= 7
                                        ? 'bg-orange-100 text-orange-700'
                                        : 'bg-yellow-100 text-yellow-700') }}">
                                <i class="fa fa-hourglass-half text-xs"></i>
                                {{ $pinjaman->hari_terlambat }} hari
                            </span>
                        </td>

                        {{-- Total Denda --}}
                        <td class="px-4 py-3.5 text-right">
                            <p class="font-bold text-red-600">
                                Rp {{ number_format($pinjaman->total_denda, 0, ',', '.') }}
                            </p>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3.5 text-center">
                            <a href="{{ route('karyawan.pembayaran.create', $pinjaman->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600
                                      hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition">
                                <i class="fa fa-money-bill text-xs"></i>
                                Bayar
                            </a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer table --}}
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Menampilkan {{ $pinjamanDenda->count() }} pinjaman kena denda
            </p>
            <div class="flex items-center gap-2 text-sm font-semibold">
                <span class="text-gray-500">Total Denda:</span>
                <span class="text-red-600">
                    Rp {{ number_format($totalDendaKeseluruhan, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ===== MOBILE CARDS (di bawah md) ===== --}}
    <div class="flex flex-col gap-3 md:hidden">
        @foreach($pinjamanDenda as $i => $pinjaman)
        <div class="bg-white rounded-xl border border-gray-100 p-4">

            {{-- Header card: avatar + nama + badge terlambat --}}
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center
                                text-emerald-700 font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($pinjaman->nasabah->nama_lengkap ?? 'N', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-sm leading-tight truncate">
                            {{ $pinjaman->nasabah->nama_lengkap ?? '-' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            NIK: {{ $pinjaman->nasabah->no_ktp ?? '-' }}
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0
                    {{ $pinjaman->hari_terlambat >= 30
                        ? 'bg-red-100 text-red-700'
                        : ($pinjaman->hari_terlambat >= 7
                            ? 'bg-orange-100 text-orange-700'
                            : 'bg-yellow-100 text-yellow-700') }}">
                    <i class="fa fa-hourglass-half text-xs"></i>
                    {{ $pinjaman->hari_terlambat }} hari
                </span>
            </div>

            {{-- Detail row --}}
            <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Pinjaman</p>
                    <p class="font-semibold text-gray-700">
                        Rp {{ number_format($pinjaman->jumlah_pinjaman ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $pinjaman->tenor->lama_bulan ?? '-' }} bulan</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Jatuh Tempo</p>
                    <p class="font-medium text-gray-700">
                        {{ \Carbon\Carbon::parse($pinjaman->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                    </p>
                    <p class="text-xs text-red-500 mt-0.5">
                        <i class="fa fa-clock mr-0.5"></i>
                        {{ \Carbon\Carbon::parse($pinjaman->tanggal_jatuh_tempo)->diffForHumans() }}
                    </p>
                </div>
            </div>

            {{-- Footer card: total denda + tombol bayar --}}
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-400">Total Denda</p>
                    <p class="font-bold text-red-600">
                        Rp {{ number_format($pinjaman->total_denda, 0, ',', '.') }}
                    </p>
                </div>
                <a href="{{ route('karyawan.pembayaran.create', $pinjaman->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600
                          hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition">
                    <i class="fa fa-money-bill text-xs"></i>
                    Bayar
                </a>
            </div>

        </div>
        @endforeach

        {{-- Footer mobile --}}
        <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                {{ $pinjamanDenda->count() }} pinjaman kena denda
            </p>
            <div class="flex items-center gap-2 text-sm font-semibold">
                <span class="text-gray-500">Total:</span>
                <span class="text-red-600">
                    Rp {{ number_format($totalDendaKeseluruhan, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    @endif

</div>
@endsection