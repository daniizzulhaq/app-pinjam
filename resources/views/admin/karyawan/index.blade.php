@extends('layouts.admin')
@section('title', 'Manajemen Karyawan')
@section('page-title', 'Manajemen Karyawan')

@section('content')
<div class="py-4">

    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama / email..."
                   class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">
                <i class="fa fa-search"></i>
            </button>
        </form>
        <a href="{{ route('admin.karyawan.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fa fa-plus mr-1"></i> Tambah Karyawan
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Rekening</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($karyawan as $k)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $k->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $k->email }}</td>
                    <td class="px-4 py-3">
                        @if($k->no_rekening)
                            <div class="font-mono text-xs text-gray-800">{{ $k->no_rekening }}</div>
                            <div class="text-xs text-gray-400">{{ $k->nama_bank }} &middot; {{ $k->nama_pemilik_rekening }}</div>
                        @else
                            <span class="text-xs text-gray-400 italic">Belum diisi</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($k->is_active)
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center space-x-1">
                        <a href="{{ route('admin.karyawan.edit', $k) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.karyawan.destroy', $k) }}"
                              class="inline" onsubmit="return confirm('Nonaktifkan karyawan ini?')">
                            @csrf @method('DELETE')
                            <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded">
                                Nonaktifkan
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-400">
                        <i class="fa fa-users text-4xl mb-2 block"></i>
                        Belum ada data karyawan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            {{ $karyawan->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection