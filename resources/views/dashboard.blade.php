@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-5 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-100 text-xs uppercase tracking-wider">Omzet Hari Ini</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">💰</div>
            </div>
            <p class="text-blue-100 text-xs mt-3">{{ $transaksiHariIni }} transaksi selesai</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-5 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-green-100 text-xs uppercase tracking-wider">Laba Hari Ini</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($labaHariIni, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">📈</div>
            </div>
            @if($omzetHariIni > 0)
            <p class="text-green-100 text-xs mt-3">Margin {{ number_format(($labaHariIni/$omzetHariIni)*100, 1) }}% dari omzet</p>
            @else
            <p class="text-green-100 text-xs mt-3">Belum ada penjualan</p>
            @endif
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-5 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-100 text-xs uppercase tracking-wider">Omzet Bulan Ini</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">📅</div>
            </div>
            <p class="text-blue-100 text-xs mt-3">{{ now()->isoFormat('MMMM Y') }}</p>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl p-5 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-orange-100 text-xs uppercase tracking-wider">Menu Aktif</p>
                    <p class="text-2xl font-bold mt-2">{{ $totalMenu }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">🍜</div>
            </div>
            <a href="{{ route('kasir.index') }}" class="text-orange-100 text-xs mt-3 block hover:text-white">Buka kasir →</a>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-5">

        {{-- Shortcut --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">⚡ Akses Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('kasir.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition group">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-lg">🧾</div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 group-hover:text-blue-600">Buka Kasir</p>
                        <p class="text-xs text-gray-400">Proses transaksi baru</p>
                    </div>
                </a>
                <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition group">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-lg">📊</div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 group-hover:text-blue-600">Lihat Laporan</p>
                        <p class="text-xs text-gray-400">Rekap penjualan bulanan</p>
                    </div>
                </a>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('hpp.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-orange-50 transition group">
                    <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center text-lg">🧮</div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 group-hover:text-orange-600">Kalkulator HPP</p>
                        <p class="text-xs text-gray-400">Full Costing & harga jual</p>
                    </div>
                </a>
                @endif
            </div>
        </div>

        {{-- Menu Terlaris Hari Ini --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">🏆 Terlaris Hari Ini</h3>
            @forelse($menuTerlaris as $i => $m)
            <div class="flex items-center gap-3 mb-3">
                <span class="text-base">{{ ['🥇','🥈','🥉','4️⃣','5️⃣'][$i] ?? ($i+1) }}</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $m->nama_menu }}</p>
                </div>
                <span class="text-sm font-bold text-blue-600">{{ $m->total_qty }} porsi</span>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm">Belum ada penjualan hari ini</p>
            </div>
            @endforelse
        </div>

        {{-- Transaksi Terbaru --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">🕐 Transaksi Terbaru</h3>
            <div class="space-y-2">
                @forelse($transaksiTerbaru as $t)
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $t->no_nota }}</p>
                        <p class="text-xs text-gray-400">{{ $t->tanggal->format('H:i') }} · {{ $t->details->count() }} item</p>
                    </div>
                    <p class="text-sm font-bold text-gray-700">Rp {{ number_format($t->total, 0, ',', '.') }}</p>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm py-6">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
