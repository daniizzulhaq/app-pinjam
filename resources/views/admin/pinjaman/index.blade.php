{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/pinjaman/index.blade.php       --}}
{{-- ============================================================ --}}
@extends('layouts.admin')
@section('title', 'Data Pinjaman')
@section('page-title', 'Data Pinjaman')

@section('content')
<div class="py-4">

    {{-- FILTER --}}
    <form method="GET" class="flex flex-wrap gap-2 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama nasabah..."
               class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-blue-400">
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">-- Semua Status --</option>
            <option value="menunggu_approval" {{ request('status') == 'menunggu_approval' ? 'selected' : '' }}>Menunggu Approval</option>
            <option value="aktif"             {{ request('status') == 'aktif'             ? 'selected' : '' }}>Aktif</option>
            <option value="lunas"             {{ request('status') == 'lunas'             ? 'selected' : '' }}>Lunas</option>
            <option value="ditolak"           {{ request('status') == 'ditolak'           ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">
            <i class="fa fa-filter"></i> Filter
        </button>
        <a href="{{ route('admin.pinjaman.index') }}" class="text-sm text-gray-500 px-3 py-2">Reset</a>
    </form>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">No. Pinjaman</th>
                    <th class="px-4 py-3 text-left">Nasabah</th>
                    <th class="px-4 py-3 text-left">Karyawan</th>
                    <th class="px-4 py-3 text-right">Jumlah</th>
                    <th class="px-4 py-3 text-center">Tenor</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pinjaman as $p)
                @php
                    $tipe   = $p->tenor_tipe ?? 'bulanan';
                    $satuan = $tipe === 'harian' ? 'hr' : 'bln';
                    $badge  = [
                        'menunggu_approval'          => 'bg-yellow-100 text-yellow-700',
                        'menunggu_transfer_karyawan' => 'bg-orange-100 text-orange-700',
                        'menunggu_konfirmasi'        => 'bg-purple-100 text-purple-700',
                        'aktif'                      => 'bg-green-100 text-green-700',
                        'lunas'                      => 'bg-blue-100 text-blue-700',
                        'ditolak'                    => 'bg-red-100 text-red-700',
                    ][$p->status] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-blue-600">{{ $p->no_pinjaman }}</td>
                    <td class="px-4 py-3 font-medium">{{ $p->nasabah->nama_lengkap }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $p->karyawan->name }}</td>
                    <td class="px-4 py-3 text-right font-medium">
                        Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span>{{ $p->tenor_bulan }} {{ $satuan }}</span>
                        @if($tipe === 'harian')
                            <span class="block text-xs text-orange-500 mt-0.5">
                                <i class="fa fa-sun-o mr-0.5"></i>Harian
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="{{ $badge }} text-xs px-2 py-1 rounded-full capitalize">
                            {{ str_replace('_', ' ', $p->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.pinjaman.show', $p) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                                Detail
                            </a>

                            @if(in_array($p->status, ['menunggu_approval', 'ditolak']))
                            <form action="{{ route('admin.pinjaman.destroy', $p) }}" method="POST"
                                  onsubmit="return confirm('Hapus pinjaman {{ $p->no_pinjaman }}? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-10 text-gray-400">
                        <i class="fa fa-file-invoice text-4xl mb-2 block"></i>
                        Belum ada data pinjaman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            {{ $pinjaman->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection