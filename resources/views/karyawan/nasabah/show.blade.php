@extends('layouts.karyawan')

@section('title', 'Detail Nasabah')
@section('page-title', 'Detail Nasabah')

@section('content')
<div class="py-4">

    <div class="mb-4">
        <a href="{{ route('karyawan.nasabah.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke daftar nasabah
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- ===== KOLOM KIRI ===== --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Data Pribadi --}}
            <div class="bg-white rounded-xl shadow p-6">
                <div class="mb-4 pb-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">👤 Data Nasabah</h3>
                    <a href="{{ route('karyawan.nasabah.edit', $nasabah) }}"
                       class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded transition">
                        <i class="fa fa-pencil mr-1"></i>Edit
                    </a>
                </div>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs">Nama Lengkap</dt>
                        <dd class="font-semibold text-gray-800 mt-0.5">{{ $nasabah->nama_lengkap }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs">No. KTP</dt>
                        <dd class="mt-0.5">
                            <code class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">{{ $nasabah->no_ktp }}</code>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs">Tempat, Tgl Lahir</dt>
                        <dd class="mt-0.5 text-gray-700">
                            {{ $nasabah->tempat_lahir }}, {{ \Carbon\Carbon::parse($nasabah->tanggal_lahir)->format('d M Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs">Jenis Kelamin</dt>
                        <dd class="mt-0.5 text-gray-700">
                            {{ $nasabah->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs">No. HP</dt>
                        <dd class="mt-0.5 text-gray-700">{{ $nasabah->no_telepon }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs">Pekerjaan</dt>
                        <dd class="mt-0.5 text-gray-700">{{ $nasabah->pekerjaan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs">Alamat</dt>
                        <dd class="mt-0.5 text-gray-700 leading-relaxed">{{ $nasabah->alamat }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Foto Dokumen --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">

                {{-- Header --}}
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa fa-files-o text-gray-400"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Dokumen Identitas</p>
                            <p class="text-xs text-gray-400">Foto nasabah &amp; KTP</p>
                        </div>
                    </div>
                    @if($nasabah->foto_nasabah && $nasabah->foto_ktp)
                        <span class="text-xs bg-green-100 text-green-700 px-2.5 py-1 rounded-full">Lengkap</span>
                    @elseif($nasabah->foto_nasabah || $nasabah->foto_ktp)
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full">Sebagian</span>
                    @else
                        <span class="text-xs bg-red-100 text-red-700 px-2.5 py-1 rounded-full">Belum ada</span>
                    @endif
                </div>

                {{-- Grid Foto --}}
                <div class="grid grid-cols-2 divide-x divide-gray-100">

                    {{-- Foto Nasabah --}}
                    <div class="p-4">
                        <div class="flex items-center gap-1.5 mb-3">
                            <i class="fa fa-user-circle text-gray-400 text-xs"></i>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Foto Nasabah</span>
                        </div>

                        @if($nasabah->foto_nasabah)
                            <div class="relative group cursor-pointer rounded-lg overflow-hidden border border-gray-200 aspect-[4/3]"
                                 onclick="openModal('{{ asset('storage/' . $nasabah->foto_nasabah) }}', 'Foto Nasabah')">
                                <img src="{{ asset('storage/' . $nasabah->foto_nasabah) }}"
                                     alt="Foto Nasabah"
                                     class="w-full h-full object-cover transition duration-200 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition flex items-end">
                                    <div class="w-full px-3 py-2.5 flex items-center justify-between translate-y-full group-hover:translate-y-0 transition duration-200">
                                        <span class="text-white text-xs font-medium">Foto Nasabah</span>
                                        <div class="flex gap-1.5">
                                            <span class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-xs px-2.5 py-1 rounded flex items-center gap-1">
                                                <i class="fa fa-search-plus"></i> Lihat
                                            </span>
                                            <a href="{{ asset('storage/' . $nasabah->foto_nasabah) }}"
                                               download target="_blank"
                                               onclick="event.stopPropagation()"
                                               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-xs px-2 py-1 rounded flex items-center">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 mt-2">
                                <i class="fa fa-check-circle text-green-500 text-xs"></i>
                                <span class="text-xs text-green-600">Sudah diupload</span>
                            </div>
                        @else
                            <div class="rounded-lg border-2 border-dashed border-gray-200 aspect-[4/3] flex flex-col items-center justify-center bg-gray-50 text-gray-300">
                                <i class="fa fa-user text-4xl mb-2"></i>
                                <p class="text-xs text-gray-400">Belum diupload</p>
                            </div>
                            <div class="flex items-center gap-1.5 mt-2">
                                <i class="fa fa-times-circle text-red-400 text-xs"></i>
                                <span class="text-xs text-red-400">Belum ada foto</span>
                            </div>
                        @endif
                    </div>

                    {{-- Foto KTP --}}
                    <div class="p-4">
                        <div class="flex items-center gap-1.5 mb-3">
                            <i class="fa fa-id-card text-gray-400 text-xs"></i>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Foto KTP</span>
                        </div>

                        @if($nasabah->foto_ktp)
                            <div class="relative group cursor-pointer rounded-lg overflow-hidden border border-gray-200 aspect-[4/3]"
                                 onclick="openModal('{{ asset('storage/' . $nasabah->foto_ktp) }}', 'Foto KTP')">
                                <img src="{{ asset('storage/' . $nasabah->foto_ktp) }}"
                                     alt="Foto KTP"
                                     class="w-full h-full object-cover transition duration-200 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition flex items-end">
                                    <div class="w-full px-3 py-2.5 flex items-center justify-between translate-y-full group-hover:translate-y-0 transition duration-200">
                                        <span class="text-white text-xs font-medium">Foto KTP</span>
                                        <div class="flex gap-1.5">
                                            <span class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-xs px-2.5 py-1 rounded flex items-center gap-1">
                                                <i class="fa fa-search-plus"></i> Lihat
                                            </span>
                                            <a href="{{ asset('storage/' . $nasabah->foto_ktp) }}"
                                               download target="_blank"
                                               onclick="event.stopPropagation()"
                                               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-xs px-2 py-1 rounded flex items-center">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 mt-2">
                                <i class="fa fa-check-circle text-green-500 text-xs"></i>
                                <span class="text-xs text-green-600">Sudah diupload</span>
                            </div>
                        @else
                            <div class="rounded-lg border-2 border-dashed border-gray-200 aspect-[4/3] flex flex-col items-center justify-center bg-gray-50 text-gray-300">
                                <i class="fa fa-id-card text-4xl mb-2"></i>
                                <p class="text-xs text-gray-400">Belum diupload</p>
                            </div>
                            <div class="flex items-center gap-1.5 mt-2">
                                <i class="fa fa-times-circle text-red-400 text-xs"></i>
                                <span class="text-xs text-red-400">Belum ada foto</span>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa fa-info-circle text-gray-400 text-xs"></i>
                        <span class="text-xs text-gray-400">Klik gambar untuk memperbesar &bull; Format: JPG, PNG</span>
                    </div>
                    @if(!$nasabah->foto_nasabah || !$nasabah->foto_ktp)
                    <a href="{{ route('karyawan.nasabah.edit', $nasabah) }}"
                       class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 transition">
                        <i class="fa fa-upload"></i> Upload
                    </a>
                    @endif
                </div>

            </div>

            {{-- Tombol Ajukan Pinjaman --}}
            <a href="{{ route('karyawan.pinjaman.create', ['nasabah_id' => $nasabah->id]) }}"
               class="flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl text-sm font-medium transition">
                <i class="fa fa-plus-circle"></i> Ajukan Pinjaman Baru
            </a>
        </div>

        {{-- ===== KOLOM KANAN: Riwayat Pinjaman ===== --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Statistik --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800">{{ $nasabah->pinjaman->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Pinjaman</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600">
                        {{ $nasabah->pinjaman->where('status', 'aktif')->count() }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Aktif</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $nasabah->pinjaman->where('status', 'lunas')->count() }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Lunas</p>
                </div>
            </div>

            {{-- Tabel Riwayat Pinjaman --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">📋 Riwayat Pinjaman</h3>
                </div>

                @if($nasabah->pinjaman->isEmpty())
                    <div class="text-center py-12 text-gray-400">
                        <i class="fa fa-file-invoice text-3xl mb-2 block"></i>
                        Belum ada riwayat pinjaman.
                        <div class="mt-3">
                            <a href="{{ route('karyawan.pinjaman.create', ['nasabah_id' => $nasabah->id]) }}"
                               class="text-emerald-600 hover:underline text-sm">
                                Ajukan pinjaman sekarang →
                            </a>
                        </div>
                    </div>
                @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">No. Pinjaman</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                            <th class="px-4 py-3 text-center">Tenor</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($nasabah->pinjaman as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs text-blue-600">{{ $p->no_pinjaman }}</span>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($p->tanggal_pengajuan)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800">
                                Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $p->tenor_bulan }} bln</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $badge = [
                                        'menunggu_approval' => 'bg-yellow-100 text-yellow-700',
                                        'aktif'             => 'bg-green-100 text-green-700',
                                        'lunas'             => 'bg-blue-100 text-blue-700',
                                        'ditolak'           => 'bg-red-100 text-red-700',
                                    ][$p->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
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
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL PREVIEW FOTO ===== --}}
<div id="fotoModal"
     class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden flex items-center justify-center p-4"
     onclick="closeModal()">
    <div class="relative max-w-2xl w-full" onclick="event.stopPropagation()">
        <div class="bg-white rounded-xl overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <h4 id="modalTitle" class="font-semibold text-gray-800 text-sm"></h4>
                <button onclick="closeModal()"
                        class="text-gray-400 hover:text-gray-600 transition text-lg leading-none">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="p-4 bg-gray-50 flex items-center justify-center min-h-64">
                <img id="modalImg" src="" alt="Preview"
                     class="max-w-full max-h-96 object-contain rounded">
            </div>
            <div class="px-4 py-3 border-t flex justify-end">
                <a id="modalDownload" href="#" download target="_blank"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-lg transition">
                    <i class="fa fa-download mr-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(src, title) {
    document.getElementById('modalImg').src = src;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDownload').href = src;
    document.getElementById('fotoModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('fotoModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>

@endsection