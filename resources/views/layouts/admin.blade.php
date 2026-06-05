<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Admin Pinjaman</title>
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
    <aside class="w-64 bg-blue-800 text-white flex flex-col flex-shrink-0">

        {{-- Logo Lembaga (dari ProfilAdmin) --}}
        @php $profil = \App\Models\ProfilAdmin::profil(); @endphp
        <div class="px-6 py-5 border-b border-blue-700 flex items-center gap-3">
            <img src="{{ $profil->logo_url }}"
                 alt="Logo"
                 class="w-9 h-9 rounded-lg object-cover flex-shrink-0 border border-blue-600">
            <div>
                <h1 class="text-base font-bold tracking-wide leading-tight">{{ $profil->nama_lembaga }}</h1>
                <p class="text-blue-300 text-xs mt-0.5">Panel Administrator</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa fa-chart-line w-5"></i> Dashboard
            </a>

            <p class="text-blue-400 text-xs uppercase font-semibold px-3 pt-4 pb-1">Manajemen</p>

            <a href="{{ route('admin.karyawan.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}">
                <i class="fa fa-users w-5"></i> Karyawan
            </a>

            <a href="{{ route('admin.nasabah.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.nasabah.*') ? 'active' : '' }}">
                <i class="fa fa-user w-5"></i> Nasabah
            </a>

            <a href="{{ route('admin.bunga.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.bunga.*') ? 'active' : '' }}">
                <i class="fa fa-percent w-5"></i> Bunga
            </a>

            <a href="{{ route('admin.tenor.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.tenor.*') ? 'active' : '' }}">
                <i class="fa fa-calendar w-5"></i> Tenor
            </a>

            <p class="text-blue-400 text-xs uppercase font-semibold px-3 pt-4 pb-1">Pinjaman</p>

            <a href="{{ route('admin.pinjaman.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.pinjaman.index') ? 'active' : '' }}">
                <i class="fa fa-file-invoice-dollar w-5"></i> Semua Pinjaman
            </a>

            <a href="{{ route('admin.pinjaman.index', ['status' => 'menunggu_approval']) }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm">
                <i class="fa fa-clock w-5"></i> Menunggu Approval
            </a>

            <a href="{{ route('admin.pinjaman.jatuh-tempo') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.pinjaman.jatuh-tempo') ? 'active' : '' }}">
                <i class="fa fa-exclamation-triangle w-5"></i> Jatuh Tempo
            </a>

            <p class="text-blue-400 text-xs uppercase font-semibold px-3 pt-4 pb-1">Monitoring</p>

            <a href="{{ route('admin.pembayaran.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.pembayaran.index') ? 'active' : '' }}">
                <i class="fa fa-money-bill w-5"></i> Pembayaran
            </a>

            <a href="{{ route('admin.pembayaran.denda') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.pembayaran.denda') ? 'active' : '' }}">
                <i class="fa fa-ban w-5"></i> Denda
            </a>

            <p class="text-blue-400 text-xs uppercase font-semibold px-3 pt-4 pb-1">Laporan</p>

            <a href="{{ route('admin.laporan.pinjaman') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.laporan.pinjaman') ? 'active' : '' }}">
                <i class="fa fa-print w-5"></i> Lap. Pinjaman
            </a>

            <a href="{{ route('admin.laporan.pembayaran') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.laporan.pembayaran') ? 'active' : '' }}">
                <i class="fa fa-print w-5"></i> Lap. Pembayaran
            </a>

            {{-- ===== PROFIL ===== --}}
            <p class="text-blue-400 text-xs uppercase font-semibold px-3 pt-4 pb-1">Pengaturan</p>

            <a href="{{ route('admin.profil.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2 rounded text-sm {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}">
                <i class="fa fa-id-card w-5"></i> Profil Lembaga
            </a>

        </nav>

        {{-- User Info --}}
        <div class="px-4 py-3 border-t border-blue-700">
            <a href="{{ route('admin.profil.index') }}"
               class="flex items-center gap-3 group hover:bg-blue-700/50 rounded-lg px-2 py-1.5 transition -mx-2">
                <img src="{{ $profil->logo_url }}"
                     alt="Logo"
                     class="w-8 h-8 rounded-full object-cover flex-shrink-0 border border-blue-500
                            group-hover:border-white transition">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
                    <p class="text-blue-300 text-xs">Administrator</p>
                </div>
                <i class="fa fa-cog text-blue-400 group-hover:text-white text-xs transition"></i>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-2 px-2">
                @csrf
                <button class="text-blue-300 hover:text-white text-xs transition">
                    <i class="fa fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between flex-shrink-0">
            <h2 class="text-lg font-semibold text-gray-700">@yield('page-title', 'Dashboard')</h2>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
                {{-- Shortcut profil di topbar --}}
                <a href="{{ route('admin.profil.index') }}"
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition
                          {{ request()->routeIs('admin.profil.*') ? 'text-blue-600 font-medium' : '' }}">
                    <img src="{{ $profil->logo_url }}"
                         alt="Logo"
                         class="w-7 h-7 rounded-full object-cover border border-gray-200">
                    <span class="hidden sm:inline">{{ $profil->nama_lembaga }}</span>
                    <i class="fa fa-chevron-down text-xs text-gray-400"></i>
                </a>
            </div>
        </header>

        {{-- Alerts --}}
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

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto px-6 pb-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>