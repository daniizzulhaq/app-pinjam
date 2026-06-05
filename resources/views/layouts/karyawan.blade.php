<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Karyawan Pinjaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        .sidebar-link.active { background: rgba(255,255,255,0.15); border-left: 3px solid #fff; }
        .sidebar-link:hover  { background: rgba(255,255,255,0.10); }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="w-64 bg-emerald-700 text-white flex flex-col flex-shrink-0">

        <div class="px-6 py-5 border-b border-emerald-600">
            <h1 class="text-xl font-bold tracking-wide">💰 Pinjamin</h1>
            <p class="text-emerald-300 text-xs mt-1">Portal Karyawan</p>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <a href="{{ route('karyawan.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                <i class="fa fa-home w-5"></i> Dashboard
            </a>

            <p class="text-emerald-400 text-xs uppercase font-semibold px-3 pt-4 pb-1">Nasabah</p>

            <a href="{{ route('karyawan.nasabah.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('karyawan.nasabah.*') ? 'active' : '' }}">
                <i class="fa fa-users w-5"></i> Data Nasabah
            </a>

            <a href="{{ route('karyawan.nasabah.create') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm">
                <i class="fa fa-user-plus w-5"></i> Tambah Nasabah
            </a>

            <p class="text-emerald-400 text-xs uppercase font-semibold px-3 pt-4 pb-1">Pinjaman</p>

            <a href="{{ route('karyawan.pinjaman.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('karyawan.pinjaman.*') ? 'active' : '' }}">
                <i class="fa fa-file-invoice-dollar w-5"></i> Data Pinjaman
            </a>

            <a href="{{ route('karyawan.pinjaman.create') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm">
                <i class="fa fa-plus-circle w-5"></i> Ajukan Pinjaman
            </a>

            {{-- ===== DENDA SECTION ===== --}}
            @php
                $pinjamanTerlambat = \App\Models\Pinjaman::where('user_id', auth()->id())
                    ->where('status', 'aktif')
                    ->where('tanggal_jatuh_tempo', '<', now()->setTime(17, 0, 0))
                    ->count();
            @endphp

            <p class="text-emerald-400 text-xs uppercase font-semibold px-3 pt-4 pb-1">Denda</p>

            {{-- Link ke halaman denda --}}
            <a href="{{ route('karyawan.denda.index') }}"
               class="sidebar-link flex items-center justify-between px-3 py-2 rounded text-sm
                      {{ request()->routeIs('karyawan.denda.*') ? 'active' : '' }}">
                <span class="flex items-center gap-3">
                    <i class="fa fa-triangle-exclamation w-5"></i>
                    Pinjaman Kena Denda
                </span>
                @if($pinjamanTerlambat > 0)
                    <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full
                                 min-w-[20px] text-center leading-tight animate-pulse">
                        {{ $pinjamanTerlambat }}
                    </span>
                @endif
            </a>

            {{-- Notif Denda (hanya tampil jika ada denda) --}}
            @if($pinjamanTerlambat > 0)
            <div class="mx-1 mt-2 bg-red-500/20 border border-red-400/30 rounded-lg px-3 py-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa fa-triangle-exclamation text-red-300 text-xs"></i>
                    <span class="text-red-200 text-xs font-semibold">Denda Aktif</span>
                </div>
                <p class="text-red-200 text-xs leading-relaxed">
                    <strong class="text-white">{{ $pinjamanTerlambat }} pinjaman</strong>
                    terkena denda Rp 50.000/hari
                </p>
                <a href="{{ route('karyawan.denda.index') }}"
                   class="mt-2 block text-center text-xs bg-red-500 hover:bg-red-400
                          text-white py-1 rounded transition font-medium">
                    Lihat Detail Denda →
                </a>
            </div>
            @endif

        </nav>

        <div class="px-4 py-3 border-t border-emerald-600 text-sm">
            <p class="font-semibold truncate">{{ auth()->user()->name }}</p>
            <p class="text-emerald-300 text-xs">Karyawan</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button class="text-emerald-300 hover:text-white text-xs">
                    <i class="fa fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN ===== --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between flex-shrink-0">
            <h2 class="text-lg font-semibold text-gray-700">@yield('page-title', 'Dashboard')</h2>
            <div class="flex items-center gap-3">
                @if($pinjamanTerlambat > 0)
                <a href="{{ route('karyawan.denda.index') }}"
                   class="flex items-center gap-1.5 bg-red-50 border border-red-200
                          text-red-600 text-xs px-3 py-1.5 rounded-full font-medium
                          hover:bg-red-100 transition">
                    <i class="fa fa-bell animate-bounce"></i>
                    {{ $pinjamanTerlambat }} pinjaman kena denda
                </a>
                @endif
                <span class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </header>

        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-3 text-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-3 text-sm">
                    ❌ {{ session('error') }}
                </div>
            @endif
        </div>

        <main class="flex-1 overflow-y-auto px-6 pb-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>