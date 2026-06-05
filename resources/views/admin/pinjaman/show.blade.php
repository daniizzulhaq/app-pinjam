{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/pinjaman/show.blade.php        --}}
{{-- ============================================================ --}}
@extends('layouts.admin')
@section('title', 'Detail Pinjaman')
@section('page-title', 'Detail Pinjaman')

@section('content')
@php
    $tipe     = $pinjaman->tenor_tipe ?? 'bulanan';
    $isHarian = $tipe === 'harian';
    $satuan   = $isHarian ? 'Hari' : 'Bulan';

    $statusLabel = [
        'menunggu_approval'          => ['label' => 'Menunggu Approval',           'class' => 'bg-yellow-100 text-yellow-700'],
        'menunggu_transfer_karyawan' => ['label' => 'Menunggu Transfer Karyawan',  'class' => 'bg-blue-100 text-blue-700'],
        'menunggu_konfirmasi'        => ['label' => 'Menunggu Konfirmasi Admin',   'class' => 'bg-purple-100 text-purple-700'],
        'aktif'                      => ['label' => 'Aktif',                       'class' => 'bg-green-100 text-green-700'],
        'lunas'                      => ['label' => 'Lunas',                       'class' => 'bg-emerald-100 text-emerald-700'],
        'ditolak'                    => ['label' => 'Ditolak',                     'class' => 'bg-red-100 text-red-700'],
    ];
    $badge = $statusLabel[$pinjaman->status] ?? ['label' => $pinjaman->status, 'class' => 'bg-gray-100 text-gray-600'];
@endphp

<div class="py-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Kiri: Info + Alur Bukti Transfer --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Info Pinjaman --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700 text-base">📄 Info Pinjaman</h3>
                <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-500">No. Pinjaman</dt>
                    <dd class="font-mono font-bold">{{ $pinjaman->no_pinjaman }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tgl. Pengajuan</dt>
                    <dd>{{ $pinjaman->tanggal_pengajuan->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Nasabah</dt>
                    <dd class="font-medium">{{ $pinjaman->nasabah->nama_lengkap }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Karyawan</dt>
                    <dd>{{ $pinjaman->karyawan->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Jumlah Pinjaman</dt>
                    <dd class="font-bold text-blue-600">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Total (+ bunga)</dt>
                    <dd class="font-bold">Rp {{ number_format($pinjaman->total_pinjaman, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Bunga</dt>
                    <dd>{{ $pinjaman->bunga_persen }}%
                        @if($isHarian)
                            <span class="text-xs text-gray-400">(≈ {{ number_format($pinjaman->bunga_persen / 30, 4) }}%/hari)</span>
                        @else
                            <span class="text-xs text-gray-400">/bulan</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tenor</dt>
                    <dd class="flex items-center gap-1.5">
                        {{ $pinjaman->tenor_bulan }} {{ $satuan }}
                        @if($isHarian)
                            <span class="bg-orange-100 text-orange-700 text-xs px-1.5 py-0.5 rounded-full">Harian</span>
                        @else
                            <span class="bg-blue-100 text-blue-700 text-xs px-1.5 py-0.5 rounded-full">Bulanan</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Cicilan / {{ $satuan }}</dt>
                    <dd class="font-bold">Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}</dd>
                </div>
                @if($pinjaman->tanggal_mulai)
                <div>
                    <dt class="text-gray-500">Tgl. Mulai</dt>
                    <dd>{{ $pinjaman->tanggal_mulai->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Jatuh Tempo</dt>
                    <dd class="text-red-600 font-medium">{{ $pinjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- ============================================================ --}}
        {{-- ALUR BUKTI TRANSFER                                          --}}
        {{-- ============================================================ --}}

        {{-- STEP 1: Admin upload bukti transfer ke karyawan --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ in_array($pinjaman->status, ['menunggu_transfer_karyawan','menunggu_konfirmasi','aktif','lunas'])
                        ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">1</div>
                <h3 class="font-semibold text-gray-700 text-base">Transfer Dana ke Karyawan</h3>
                @if($pinjaman->bukti_transfer_admin)
                    <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                        <i class="fa fa-check mr-1"></i>Sudah diupload
                    </span>
                @endif
            </div>

            @if($pinjaman->bukti_transfer_admin)
                {{-- Preview bukti yang sudah ada --}}
                <div class="mb-3">
                    <p class="text-xs text-gray-400 mb-2">
                        Diupload {{ $pinjaman->tgl_transfer_admin?->format('d/m/Y H:i') }}
                    </p>
                    <a href="{{ asset('storage/'.$pinjaman->bukti_transfer_admin) }}" target="_blank">
                        <img src="{{ asset('storage/'.$pinjaman->bukti_transfer_admin) }}"
                             alt="Bukti Transfer Admin"
                             class="w-48 h-48 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition cursor-pointer">
                    </a>
                    <p class="text-xs text-blue-500 mt-1">Klik gambar untuk lihat penuh</p>
                </div>
            @endif

            @if($pinjaman->status === 'menunggu_approval')
                <form method="POST"
                      action="{{ route('admin.pinjaman.upload-bukti-admin', $pinjaman) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center mb-3"
                         id="dropzone-admin">
                        <input type="file" name="bukti_transfer_admin" id="bukti_admin"
                               accept="image/*" class="hidden"
                               onchange="previewImage(this, 'preview-admin')">
                        <label for="bukti_admin" class="cursor-pointer">
                            <i class="fa fa-cloud-upload text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-sm text-gray-500">Klik untuk pilih bukti transfer</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — maks 2MB</p>
                        </label>
                        <img id="preview-admin" class="hidden mx-auto mt-3 w-40 h-40 object-cover rounded-lg border">
                    </div>
                    @error('bukti_transfer_admin')
                        <p class="text-red-500 text-xs mb-2"><i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fa fa-paper-plane mr-1"></i> Kirim ke Karyawan
                    </button>
                </form>
            @elseif(!$pinjaman->bukti_transfer_admin)
                <p class="text-sm text-gray-400 italic">Menunggu pengajuan diproses.</p>
            @endif
        </div>

        {{-- STEP 2: Bukti transfer karyawan ke nasabah (view only untuk admin) --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ in_array($pinjaman->status, ['menunggu_konfirmasi','aktif','lunas'])
                        ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-500' }}">2</div>
                <h3 class="font-semibold text-gray-700 text-base">Bukti Transfer Karyawan ke Nasabah</h3>
                @if($pinjaman->bukti_transfer_karyawan)
                    <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                        <i class="fa fa-check mr-1"></i>Sudah diupload
                    </span>
                @endif
            </div>

            @if($pinjaman->bukti_transfer_karyawan)
                <div class="mb-3">
                    <p class="text-xs text-gray-400 mb-2">
                        Diupload oleh karyawan {{ $pinjaman->tgl_transfer_karyawan?->format('d/m/Y H:i') }}
                    </p>
                    <a href="{{ asset('storage/'.$pinjaman->bukti_transfer_karyawan) }}" target="_blank">
                        <img src="{{ asset('storage/'.$pinjaman->bukti_transfer_karyawan) }}"
                             alt="Bukti Transfer Karyawan"
                             class="w-48 h-48 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition cursor-pointer">
                    </a>
                    <p class="text-xs text-blue-500 mt-1">Klik gambar untuk lihat penuh</p>
                </div>
            @else
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 text-sm text-gray-500">
                    <i class="fa fa-clock text-gray-300 text-xl"></i>
                    <span>Menunggu karyawan upload bukti transfer ke nasabah.</span>
                </div>
            @endif
        </div>

        {{-- STEP 3: Form Approval --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ in_array($pinjaman->status, ['aktif','lunas']) ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">3</div>
                <h3 class="font-semibold text-gray-700 text-base">Konfirmasi & Approval</h3>
            </div>

            @if($pinjaman->status === 'menunggu_konfirmasi')
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 mb-4 text-xs text-emerald-700">
                    <i class="fa fa-info-circle mr-1"></i>
                    Karyawan sudah upload bukti transfer ke nasabah. Silakan verifikasi dan lakukan approval.
                </div>
                <form method="POST" action="{{ route('admin.pinjaman.approval', $pinjaman) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan_approval" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                  placeholder="Catatan approval (opsional)..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" name="action" value="disetujui"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                            <i class="fa fa-check mr-1"></i> Setujui & Aktifkan
                        </button>
                        <button type="submit" name="action" value="ditolak"
                                onclick="return confirm('Tolak pinjaman ini?')"
                                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg text-sm font-medium">
                            <i class="fa fa-times mr-1"></i> Tolak
                        </button>
                    </div>
                </form>
            @elseif($pinjaman->status === 'aktif')
                <div class="flex items-center gap-3 bg-green-50 rounded-lg p-4 text-sm text-green-700">
                    <i class="fa fa-check-circle text-green-500 text-xl"></i>
                    <div>
                        <p class="font-semibold">Pinjaman telah disetujui dan aktif</p>
                        <p class="text-xs text-green-600 mt-0.5">
                            Disetujui {{ $pinjaman->tanggal_approval?->format('d/m/Y') }}
                            @if($pinjaman->catatan_approval) · {{ $pinjaman->catatan_approval }} @endif
                        </p>
                    </div>
                </div>
            @elseif($pinjaman->status === 'ditolak')
                <div class="flex items-center gap-3 bg-red-50 rounded-lg p-4 text-sm text-red-700">
                    <i class="fa fa-times-circle text-red-400 text-xl"></i>
                    <div>
                        <p class="font-semibold">Pinjaman ditolak</p>
                        @if($pinjaman->catatan_approval)
                            <p class="text-xs mt-0.5">{{ $pinjaman->catatan_approval }}</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 text-sm text-gray-500">
                    <i class="fa fa-lock text-gray-300 text-xl"></i>
                    <span>Tersedia setelah karyawan upload bukti transfer ke nasabah.</span>
                </div>
            @endif
        </div>

        {{-- Riwayat Pembayaran --}}
        @if(in_array($pinjaman->status, ['aktif', 'lunas']))
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">💳 Riwayat Pembayaran</h3>
            @if($pinjaman->pembayaran->isEmpty())
                <p class="text-gray-400 text-sm">Belum ada pembayaran.</p>
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Ke-</th>
                        <th class="px-3 py-2 text-left">Tgl Bayar</th>
                        <th class="px-3 py-2 text-right">Dibayar</th>
                        <th class="px-3 py-2 text-center">Denda</th>
                        <th class="px-3 py-2 text-center">Jenis</th>
                        <th class="px-3 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pinjaman->pembayaran as $bayar)
                    <tr>
                        <td class="px-3 py-2">{{ $bayar->angsuran_ke }}</td>
                        <td class="px-3 py-2">{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($bayar->jumlah_dibayar, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-center text-red-500">
                            {{ $bayar->denda > 0 ? 'Rp '.number_format($bayar->denda, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-3 py-2 text-center text-xs">{{ str_replace('_', ' ', $bayar->jenis_pembayaran) }}</td>
                        <td class="px-3 py-2 text-center">
                            @if($bayar->status === 'lunas')
                                <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded-full">Lunas</span>
                            @else
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full">Sebagian</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

        {{-- Info Nasabah --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">👤 Info Nasabah</h3>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-gray-500 text-xs">No. KTP</dt><dd class="font-mono">{{ $pinjaman->nasabah->no_ktp }}</dd></div>
                <div><dt class="text-gray-500 text-xs">Nama</dt><dd class="font-medium">{{ $pinjaman->nasabah->nama_lengkap }}</dd></div>
                <div><dt class="text-gray-500 text-xs">Telepon</dt><dd>{{ $pinjaman->nasabah->no_telepon }}</dd></div>
                <div><dt class="text-gray-500 text-xs">Alamat</dt><dd>{{ $pinjaman->nasabah->alamat }}</dd></div>
            </dl>
        </div>

        {{-- Ringkasan Tenor --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">📊 Ringkasan Tenor</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Tipe</dt>
                    <dd>
                        @if($isHarian)
                            <span class="bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full">Harian</span>
                        @else
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">Bulanan</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Periode</dt>
                    <dd class="font-semibold">{{ $pinjaman->tenor_bulan }} {{ $satuan }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Cicilan</dt>
                    <dd class="font-semibold text-emerald-600">
                        Rp {{ number_format($pinjaman->cicilan_per_bulan, 0, ',', '.') }}
                        <span class="text-xs text-gray-400">/ {{ strtolower($satuan) }}</span>
                    </dd>
                </div>
                @if(in_array($pinjaman->status, ['aktif','lunas']))
                @php
                    $totalSudahBayar = $pinjaman->pembayaran->sum('jumlah_dibayar');
                    $sisaLunas       = $pinjaman->total_pinjaman - $totalSudahBayar;
                    $progressPersen  = $pinjaman->total_pinjaman > 0
                        ? min(100, round($totalSudahBayar / $pinjaman->total_pinjaman * 100))
                        : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Progress Lunas</span>
                        <span>{{ $progressPersen }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $progressPersen >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}"
                             style="width: {{ $progressPersen }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs mt-1">
                        <span class="text-emerald-600">Bayar: Rp {{ number_format($totalSudahBayar, 0, ',', '.') }}</span>
                        <span class="text-red-500">Sisa: Rp {{ number_format($sisaLunas, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif
            </dl>
        </div>

        {{-- Timeline Status --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-base">🔄 Alur Status</h3>
            @php
                $steps = [
                    ['status' => 'menunggu_approval',          'label' => 'Pengajuan masuk'],
                    ['status' => 'menunggu_transfer_karyawan', 'label' => 'Admin kirim bukti transfer'],
                    ['status' => 'menunggu_konfirmasi',        'label' => 'Karyawan transfer ke nasabah'],
                    ['status' => 'aktif',                      'label' => 'Admin approval → Aktif'],
                ];
                $currentIndex = array_search($pinjaman->status, array_column($steps, 'status'));
                if ($pinjaman->status === 'lunas') $currentIndex = 3;
            @endphp
            <ol class="space-y-3">
                @foreach($steps as $i => $step)
                @php
                    $done    = $currentIndex !== false && $i <= $currentIndex;
                    $current = $currentIndex !== false && $i === $currentIndex;
                @endphp
                <li class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs flex-shrink-0
                        {{ $done ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        @if($done) <i class="fa fa-check text-[10px]"></i>
                        @else {{ $i + 1 }}
                        @endif
                    </div>
                    <span class="text-sm {{ $current ? 'font-semibold text-gray-800' : ($done ? 'text-gray-600' : 'text-gray-400') }}">
                        {{ $step['label'] }}
                    </span>
                </li>
                @endforeach
            </ol>
        </div>

    </div>

</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection