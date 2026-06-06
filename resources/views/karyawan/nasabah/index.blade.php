@extends('layouts.karyawan')
@section('title', 'Data Nasabah')
@section('page-title', 'Data Nasabah Saya')

@section('content')
<div class="py-4">
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama / no. KTP..."
                   class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">
                <i class="fa fa-search"></i>
            </button>
        </form>
        <a href="{{ route('karyawan.nasabah.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fa fa-user-plus mr-1"></i> Tambah Nasabah
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">No. KTP</th>
                    <th class="px-4 py-3 text-left">Nama Lengkap</th>
                    <th class="px-4 py-3 text-left">Telepon</th>
                    <th class="px-4 py-3 text-left">Kota</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($nasabah as $n)
                @php
                    $wa = preg_replace('/[^0-9]/', '', $n->no_telepon);
                    if (str_starts_with($wa, '0')) {
                        $wa = '62' . substr($wa, 1);
                    }

                    $pinjamanAktif = $n->pinjaman->first();
                    $pesanWa = '';
                    if ($pinjamanAktif) {
                        $jatuhTempo   = $pinjamanAktif->tanggal_jatuh_tempo
                            ? \Carbon\Carbon::parse($pinjamanAktif->tanggal_jatuh_tempo)->format('d/m/Y')
                            : '-';
                        $totalTagihan = number_format($pinjamanAktif->total_pinjaman, 0, ',', '.');
                        $pesanWa = urlencode(
                            "Jangan Lupa JAPO tanggal : {$jatuhTempo}\n" .
                            "Sebesar Rp. {$totalTagihan}\n" .
                            "Transfer di Rek. BCA 8755194596 an. Rahmat M Dotulong."
                        );
                    }
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $n->no_ktp }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $n->nama_lengkap }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        <span class="block">{{ $n->no_telepon }}</span>
                        <a href="https://wa.me/{{ $wa }}{{ $pesanWa ? '?text='.$pesanWa : '' }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 text-xs text-green-600 hover:text-green-700 mt-0.5">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Chat WA
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $n->kota }}</td>
                    <td class="px-4 py-3 text-center space-x-1">
                        <a href="{{ route('karyawan.nasabah.show', $n) }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">Detail</a>
                        <a href="{{ route('karyawan.nasabah.edit', $n) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded">Edit</a>
                        <a href="{{ route('karyawan.pinjaman.create', ['nasabah_id' => $n->id]) }}"
                           class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs px-3 py-1 rounded">+ Pinjaman</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-400">
                        <i class="fa fa-users text-4xl mb-2 block"></i>
                        Belum ada nasabah. <a href="{{ route('karyawan.nasabah.create') }}" class="text-emerald-600 underline">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            {{ $nasabah->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection