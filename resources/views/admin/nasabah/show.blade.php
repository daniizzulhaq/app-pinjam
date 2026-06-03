@extends('layouts.admin')

@section('title', 'Detail Nasabah')
@section('page-title', 'Detail Nasabah')

@section('content')
<div class="py-4">

    <div class="mb-4">
        <a href="{{ route('admin.nasabah.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fa fa-arrow-left"></i> Kembali ke daftar nasabah
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
        <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- ===== KIRI ===== --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Profile Card --}}
            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg font-bold">
                            {{ strtoupper(substr($nasabah->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm">{{ $nasabah->nama_lengkap }}</div>
                            <div class="text-xs text-gray-400">Nasabah sejak {{ $nasabah->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.nasabah.edit', $nasabah) }}"
                       class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded transition">
                        <i class="fa fa-pencil mr-1"></i>Edit
                    </a>
                </div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-400">No KTP</dt>
                        <dd><code class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">{{ $nasabah->no_ktp }}</code></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Tempat, Tgl Lahir</dt>
                        <dd class="text-gray-700">
                            {{ $nasabah->tempat_lahir }},
                            {{ \Carbon\Carbon::parse($nasabah->tanggal_lahir)->format('d M Y') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Jenis Kelamin</dt>
                        <dd>
                            @if($nasabah->jenis_kelamin == 'L')
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">Laki-laki</span>
                            @else
                                <span class="bg-pink-100 text-pink-700 text-xs px-2 py-0.5 rounded-full">Perempuan</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">No HP</dt>
                        <dd class="text-gray-700">{{ $nasabah->no_telepon }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Pekerjaan</dt>
                        <dd class="text-gray-700">{{ $nasabah->pekerjaan ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Kota / Provinsi</dt>
                        <dd class="text-gray-700">{{ $nasabah->kota }} / {{ $nasabah->provinsi }}</dd>
                    </div>
                    <div class="flex justify-between items-start">
                        <dt class="text-gray-400">Marketing</dt>
                        <dd class="text-right">
                            <span class="font-medium text-gray-800">
                                {{ $nasabah->karyawan->name ?? $nasabah->karyawan->nama ?? '-' }}
                            </span>
                        </dd>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <dt class="text-gray-400 mb-1">Alamat</dt>
                        <dd class="text-gray-700 leading-relaxed">{{ $nasabah->alamat }}</dd>
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

                {{-- Footer info --}}
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center gap-2">
                    <i class="fa fa-info-circle text-gray-400 text-xs"></i>
                    <span class="text-xs text-gray-400">Klik gambar untuk memperbesar &bull; Format: JPG, PNG</span>
                </div>

            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow p-4 flex gap-2">
                <a href="{{ route('admin.nasabah.edit', $nasabah) }}"
                   class="flex-1 text-center bg-yellow-400 hover:bg-yellow-500 text-white text-sm py-2 rounded-lg font-medium transition">
                    <i class="fa fa-pencil mr-1"></i> Edit
                </a>
                <form method="POST" action="{{ route('admin.nasabah.destroy', $nasabah) }}"
                      class="flex-1" onsubmit="return confirm('Hapus nasabah ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full bg-red-500 hover:bg-red-600 text-white text-sm py-2 rounded-lg font-medium transition">
                        <i class="fa fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>

        </div>

        {{-- ===== KANAN: RIWAYAT PINJAMAN ===== --}}
        <div class="lg:col-span-3 space-y-4">

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

            {{-- Riwayat Pinjaman --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800">📋 Riwayat Pinjaman</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Total {{ $nasabah->pinjaman->count() }} pinjaman</p>
                </div>

                @forelse($nasabah->pinjaman as $pinjaman)
                @php
                    $totalBayar = $pinjaman->pembayaran->sum('jumlah_bayar');
                    $persen = $pinjaman->jumlah_pinjaman > 0
                        ? min(100, round($totalBayar / $pinjaman->jumlah_pinjaman * 100))
                        : 0;
                    $statusColor = match($pinjaman->status ?? 'aktif') {
                        'lunas'              => 'bg-blue-100 text-blue-700',
                        'aktif'              => 'bg-green-100 text-green-700',
                        'menunggu_approval'  => 'bg-yellow-100 text-yellow-700',
                        'ditolak'            => 'bg-red-100 text-red-700',
                        default              => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <div class="px-5 py-4 border-b border-gray-50 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="font-mono text-xs text-blue-600 mb-0.5">{{ $pinjaman->no_pinjaman }}</div>
                            <div class="font-semibold text-gray-800 text-sm">
                                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d M Y') }}
                                &bull; {{ $pinjaman->tenor_bulan }} bulan
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $statusColor }}">
                            {{ str_replace('_', ' ', ucfirst($pinjaman->status)) }}
                        </span>
                    </div>

                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Rp {{ number_format($totalBayar, 0, ',', '.') }} terbayar</span>
                        <span>{{ $persen }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ $persen }}%"></div>
                    </div>

                    <div class="text-xs text-gray-400">
                        {{ $pinjaman->pembayaran->count() }} pembayaran
                        @if($pinjaman->pembayaran->count() > 0)
                            &mdash; Terakhir {{ $pinjaman->pembayaran->sortByDesc('created_at')->first()->created_at->format('d M Y') }}
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400">
                    <i class="fa fa-folder-open text-4xl mb-2 block"></i>
                    <p class="text-sm">Belum ada riwayat pinjaman.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- Modal Preview Foto --}}
<div id="fotoModal"
     class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden flex items-center justify-center p-4"
     onclick="closeModal()">
    <div class="relative max-w-2xl w-full" onclick="event.stopPropagation()">
        <div class="bg-white rounded-xl overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <h4 id="modalTitle" class="font-semibold text-gray-800 text-sm"></h4>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">
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
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>

@endsection