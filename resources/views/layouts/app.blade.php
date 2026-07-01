<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') — {{ \App\Models\AppSetting::get('nama_warung', 'Warung Bakso') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active { background: rgba(255,255,255,0.15); }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<div class="flex h-screen overflow-hidden">
    {{-- SIDEBAR --}}
    <aside class="w-64 text-white flex flex-col no-print" x-data>

        {{-- Header Logo --}}
        <div class="bg-blue-900 p-5 border-b border-blue-600">
            <!-- <div class="flex items-center gap-3"> -->
                <div class="w-25 h-25   flex items-center justify-center overflow-hidden">
                    <img src="images/logoo.png" alt="Logo" class="w-full h-full object-cover">
                </div>
                <!-- <div>
                    <p class="font-bold text-sm leading-tight">{{ \App\Models\AppSetting::get('nama_warung', 'Warung Bakso') }}</p>
                    <p class="text-blue-200 text-xs">Sistem Kasir</p>
                </div> -->
            <!-- </div> -->
        </div>

        {{-- Isi Sidebar --}}
        <div class="bg-blue-700 flex-1 flex flex-col">

            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/15 transition {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie w-5 text-center"></i>
                    <span class="text-sm">Dashboard</span>
                </a>

                <a href="{{ route('kasir.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/15 transition {{ request()->routeIs('kasir*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register w-5 text-center"></i>
                    <span class="text-sm">Kasir / POS</span>
                </a>

                <a href="{{ route('laporan.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/15 transition {{ request()->routeIs('laporan*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span class="text-sm">Laporan Penjualan</span>
                </a>

                @if(auth()->user()->role === 'admin')
                <div class="pt-3 pb-1">
                    <p class="text-blue-300 text-xs font-semibold uppercase tracking-wider px-3">Admin</p>
                </div>

                <a href="{{ route('menu.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/15 transition {{ request()->routeIs('menu*') ? 'active' : '' }}">
                    <i class="fas fa-bowl-food w-5 text-center"></i>
                    <span class="text-sm">Kelola Menu</span>
                </a>

                <a href="{{ route('hpp.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/15 transition {{ request()->routeIs('hpp*') ? 'active' : '' }}">
                    <i class="fas fa-calculator w-5 text-center"></i>
                    <span class="text-sm">Kalkulator HPP</span>
                </a>

                <a href="{{ route('bahan-baku.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/15 transition {{ request()->routeIs('bahan-baku*') ? 'active' : '' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center"></i>
                    <span class="text-sm">Bahan Baku</span>
                </a>
                @endif
            </nav>

            <div class="p-4 border-t border-blue-600">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-300 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left flex items-center gap-2 text-blue-200 hover:text-white text-sm transition">
                        <i class="fas fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 overflow-y-auto">
        {{-- Header --}}
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between no-print">
            <div>
                <h1 class="text-lg font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    <span id="live-clock"></span>
                </span>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2 no-print" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg flex items-center gap-2 no-print">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        <div class="p-6">
            @yield('content')
        </div>
    </main>
</div>

<script>
    // Live clock
    function updateClock() {
        const el = document.getElementById('live-clock');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
@stack('scripts')
</body>
</html>
