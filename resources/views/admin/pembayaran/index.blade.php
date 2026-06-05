@extends('layouts.admin')

@section('title', 'Data Pembayaran')
@section('page-title', 'Data Pembayaran')

@section('content')
<div class="py-4">

    {{-- HEADER / FILTER --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="bulan"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">-- Semua Bulan --</option>
                @foreach(range(1, 12) as $b)
                    <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>

            <select name="tahun"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">-- Semua Tahun --</option>
                @foreach(range(date('Y'), date('Y') - 5) as $y)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm transition">
                <i class="fa fa-filter mr-1"></i> Filter
            </button>

            @if(request('bulan') || request('tahun'))
            <a href="{{ route('admin.pembayaran.index') }}"
               class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm text-gray-500 transition">
                <i class="fa fa-times"></i> Reset
            </a>
            @endif
        </form>

        <a href="{{ route('admin.pembayaran.denda') }}"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fa fa-exclamation-triangle mr-1"></i> Lihat Denda
        </a>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">No. Bayar</th>
                    <th class="px-4 py-3 text-left">Nasabah</th>
                    <th class="px-4 py-3 text-left">No. Pinjaman</th>
                    <th class="px-4 py-3 text-left">Tgl Bayar</th>
                    <th class="px-4 py-3 text-right">Jumlah Bayar</th>
                    <th class="px-4 py-3 text-right">Denda</th>
                    <th class="px-4 py-3 text-left">Karyawan</th>
                    <th class="px-4 py-3 text-center">Bukti</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pembayaran as $item)
                @php
                    $statusColor = match($item->status ?? 'lunas') {
                        'lunas'      => 'bg-green-100 text-green-700',
                        'sebagian'   => 'bg-yellow-100 text-yellow-700',
                        'tidak_bayar'=> 'bg-red-100 text-red-600',
                        default      => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ $loop->iteration + ($pembayaran->currentPage() - 1) * $pembayaran->perPage() }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs text-blue-600">{{ $item->no_pembayaran }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">
                            {{ $item->pinjaman->nasabah->nama_lengkap ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $item->pinjaman->nasabah->no_ktp ?? '' }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs text-blue-600">
                            {{ $item->pinjaman->no_pinjaman ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                        Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($item->denda > 0)
                            <span class="text-red-600 font-medium">
                                Rp {{ number_format($item->denda, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs">
                        {{ $item->karyawan->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->bukti_pembayaran)
                            <button onclick="lihatBukti('{{ $item->bukti_url }}')"
                                    class="inline-flex items-center gap-1 text-xs bg-emerald-50 hover:bg-emerald-100
                                           text-emerald-700 border border-emerald-200 px-2 py-1 rounded-lg transition font-medium">
                                <i class="fa fa-image text-[10px]"></i> Lihat
                            </button>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status ?? 'lunas')) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.pembayaran.show', $item) }}"
                           class="inline-flex items-center gap-1 text-xs bg-blue-50 hover:bg-blue-100
                                  text-blue-700 border border-blue-200 px-3 py-1.5 rounded-lg transition font-medium">
                            <i class="fa fa-eye text-[10px]"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-12 text-gray-400">
                        <i class="fa fa-inbox text-4xl mb-2 block"></i>
                        Belum ada data pembayaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-500">
            @if($pembayaran->total() > 0)
            <span>Menampilkan {{ $pembayaran->firstItem() }}–{{ $pembayaran->lastItem() }} dari {{ $pembayaran->total() }} data</span>
            @endif
            {{ $pembayaran->withQueryString()->links() }}
        </div>
    </div>

</div>

{{-- Modal Lihat Bukti --}}
<div id="modalBukti"
     class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4"
     onclick="tutupBukti()">
    <div class="relative max-w-lg w-full" onclick="event.stopPropagation()">
        <button onclick="tutupBukti()"
                class="absolute -top-3 -right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center
                       shadow-lg text-gray-500 hover:text-red-500 transition z-10">
            <i class="fa fa-times text-sm"></i>
        </button>
        <img id="modalBuktiImg"
             src=""
             alt="Bukti Pembayaran"
             class="w-full rounded-2xl shadow-2xl object-contain max-h-[80vh]">
        <a id="modalBuktiLink"
           href=""
           target="_blank"
           class="mt-3 flex items-center justify-center gap-2 bg-white text-gray-700 text-sm
                  font-medium py-2 rounded-xl hover:bg-gray-50 transition">
            <i class="fa fa-external-link-alt"></i> Buka di Tab Baru
        </a>
    </div>
</div>

<script>
function lihatBukti(url) {
    document.getElementById('modalBuktiImg').src  = url;
    document.getElementById('modalBuktiLink').href = url;
    const modal = document.getElementById('modalBukti');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function tutupBukti() {
    const modal = document.getElementById('modalBukti');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupBukti(); });
</script>
@endsection