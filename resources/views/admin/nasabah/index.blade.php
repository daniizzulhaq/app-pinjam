@extends('layouts.admin')

@section('title', 'Data Nasabah')
@section('page-title', 'Data Nasabah')

@section('content')
<div class="py-4">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
        <i class="fa fa-check-circle text-green-500"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama / no KTP..."
                   class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm transition">
                <i class="fa fa-search"></i>
            </button>
            @if(request('search'))
            <a href="{{ route('admin.nasabah.index') }}"
               class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm text-gray-500 transition">
                <i class="fa fa-times"></i>
            </a>
            @endif
        </form>
        <a href="{{ route('admin.nasabah.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fa fa-plus mr-1"></i> Tambah Nasabah
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Nama Lengkap</th>
                    <th class="px-4 py-3 text-left">No KTP</th>
                    <th class="px-4 py-3 text-left">No HP</th>
                    <th class="px-4 py-3 text-left">Pekerjaan</th>
                    <th class="px-4 py-3 text-left">Marketing</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($nasabah as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ $loop->iteration + ($nasabah->currentPage() - 1) * $nasabah->perPage() }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $item->nama_lengkap }}</div>
                        <div class="text-xs text-gray-400">
                            {{ $item->tempat_lahir }}, {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d M Y') }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <code class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">{{ $item->no_ktp }}</code>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-600">{{ $item->no_telepon }}</div>
                        @php
                            $wa = preg_replace('/[^0-9]/', '', $item->no_telepon);
                            if (str_starts_with($wa, '0')) {
                                $wa = '62' . substr($wa, 1);
                            }
                        @endphp
                        <a href="https://wa.me/{{ $wa }}" target="_blank"
                           class="inline-flex items-center gap-1 text-xs text-green-600 hover:text-green-700 mt-0.5">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Chat WA
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $item->pekerjaan ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->karyawan->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center space-x-1">
                        <a href="{{ route('admin.nasabah.show', $item) }}"
                           class="bg-sky-500 hover:bg-sky-600 text-white text-xs px-3 py-1 rounded transition">
                            <i class="fa fa-eye mr-1"></i>Detail
                        </a>
                        <a href="{{ route('admin.nasabah.edit', $item) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded transition">
                            <i class="fa fa-pencil mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('admin.nasabah.destroy', $item) }}"
                              class="inline" onsubmit="return confirm('Hapus nasabah ini?')">
                            @csrf @method('DELETE')
                            <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded transition">
                                <i class="fa fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <i class="fa fa-users text-4xl mb-2 block"></i>
                        Belum ada data nasabah.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t flex items-center justify-between text-sm text-gray-500">
            @if($nasabah->total() > 0)
            <span>Menampilkan {{ $nasabah->firstItem() }}–{{ $nasabah->lastItem() }} dari {{ $nasabah->total() }} nasabah</span>
            @endif
            {{ $nasabah->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection