{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/bunga/index.blade.php          --}}
{{-- ============================================================ --}}
@extends('layouts.admin')

@section('title', 'Data Bunga')
@section('page-title', 'Manajemen Bunga Pinjaman')

@section('content')
<div class="py-4">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
        <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Total: <strong>{{ $bunga->total() }}</strong> data bunga</p>
        <a href="{{ route('admin.bunga.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fa fa-plus mr-1"></i> Tambah Bunga
        </a>
    </div>

    {{-- Info hint --}}
    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mb-4 text-xs">
        <i class="fa fa-info-circle text-blue-400 mt-0.5"></i>
        <span>
            Persentase bunga diinput dalam satuan <strong>%/bulan</strong>.
            Untuk pinjaman dengan tenor <strong>harian</strong>, sistem otomatis mengkonversi menjadi %/hari (dibagi 30).
        </span>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Nama Bunga</th>
                    <th class="px-4 py-3 text-center">Persentase</th>
                    <th class="px-4 py-3 text-center">Jenis</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bunga as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ $loop->iteration + ($bunga->currentPage() - 1) * $bunga->perPage() }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->nama_bunga }}</td>
                    <td class="px-4 py-3 text-center">
                        <code class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">{{ $item->persentase }}%/bln</code>
                        <div class="text-xs text-gray-400 mt-0.5">
                            ≈ {{ number_format($item->persentase / 30, 4) }}%/hari
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->jenis == 'flat')
                            <span class="bg-purple-100 text-purple-700 text-xs px-2 py-1 rounded-full">Flat</span>
                        @else
                            <span class="bg-cyan-100 text-cyan-700 text-xs px-2 py-1 rounded-full">Efektif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->is_active)
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center space-x-1">
                        <a href="{{ route('admin.bunga.edit', $item) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded transition">
                            <i class="fa fa-pencil mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('admin.bunga.destroy', $item) }}"
                              class="inline" onsubmit="return confirm('Hapus data bunga ini?')">
                            @csrf @method('DELETE')
                            <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded transition">
                                <i class="fa fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400">
                        <i class="fa fa-percent text-4xl mb-2 block"></i>
                        Belum ada data bunga.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-500">
            @if($bunga->total() > 0)
            <span>Menampilkan {{ $bunga->firstItem() }}–{{ $bunga->lastItem() }} dari {{ $bunga->total() }} data</span>
            @endif
            {{ $bunga->links() }}
        </div>
    </div>
</div>
@endsection