{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/tenor/index.blade.php          --}}
{{-- ============================================================ --}}
@extends('layouts.admin')

@section('title', 'Data Tenor')
@section('page-title', 'Manajemen Tenor Pinjaman')

@section('content')
<div class="py-4">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
        <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Total: <strong>{{ $tenor->total() }}</strong> data tenor</p>
        <a href="{{ route('admin.tenor.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fa fa-plus mr-1"></i> Tambah Tenor
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Label</th>
                    <th class="px-4 py-3 text-center">Jumlah Bulan</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tenor as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->label }}</td>
                    <td class="px-4 py-3 text-center">
                        <code class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">{{ $item->bulan }} bulan</code>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->is_active)
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center space-x-1">
                        <a href="{{ route('admin.tenor.edit', $item) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded transition">
                            <i class="fa fa-pencil mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('admin.tenor.destroy', $item) }}"
                              class="inline" onsubmit="return confirm('Hapus tenor ini?')">
                            @csrf @method('DELETE')
                            <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded transition">
                                <i class="fa fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-gray-400">
                        <i class="fa fa-calendar text-4xl mb-2 block"></i>
                        Belum ada data tenor.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-500">
            @if($tenor->total() > 0)
            <span>Menampilkan {{ $tenor->firstItem() }}–{{ $tenor->lastItem() }} dari {{ $tenor->total() }} data</span>
            @endif
            {{ $tenor->links() }}
        </div>
    </div>
</div>
@endsection