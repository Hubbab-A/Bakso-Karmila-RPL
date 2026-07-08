@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')
<div class="space-y-6">

    {{-- Filter Bulan --}}
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-sm text-gray-600 font-medium">Periode:</label>
            <input type="month" name="bulan" value="{{ $bulan }}"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                Tampilkan
            </button>
        </form>
        <div class="flex gap-2">
            @foreach($bulanList->take(6) as $b)
            <a href="?bulan={{ $b }}" class="text-xs px-3 py-1.5 rounded-lg transition {{ $b === $bulan ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $b)->isoFormat('MMM YY') }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($summary['total_transaksi']) }}</p>
            <p class="text-xs text-gray-400 mt-1">transaksi selesai</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Total Omzet</p>
            <p class="text-2xl font-bold text-blue-600 mt-2">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">rata² Rp {{ number_format($summary['rata_per_hari'], 0, ',', '.') }}/hari</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Total Laba</p>
            <p class="text-2xl font-bold text-green-600 mt-2">Rp {{ number_format($summary['total_laba'], 0, ',', '.') }}</p>
            @if($summary['total_omzet'] > 0)
            <p class="text-xs text-gray-400 mt-1">margin {{ number_format(($summary['total_laba']/$summary['total_omzet'])*100, 1) }}%</p>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Total Diskon</p>
            <p class="text-2xl font-bold text-orange-500 mt-2">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">diberikan bulan ini</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5">
        {{-- Grafik Harian --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">Omzet Harian</h3>
            @if($harian->count() > 0)
            <div class="relative h-40">
                @php
                    $maxOmzet = $harian->max('omzet') ?: 1;
                @endphp
                <div class="flex items-end gap-1 h-32">
                    @foreach($harian as $h)
                    @php $pct = ($h->omzet / $maxOmzet) * 100; @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group relative">
                        <div class="w-full bg-blue-500 hover:bg-blue-600 rounded-t transition cursor-pointer"
                            style="height: {{ max(4, $pct) }}%"
                            title="{{ $h->tgl }}: Rp {{ number_format($h->omzet, 0, ',', '.') }}"></div>
                        <span class="text-xs text-gray-400 rotate-45 origin-left mt-1"
                            style="font-size:9px">{{ \Carbon\Carbon::parse($h->tgl)->format('d') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <p class="text-center text-gray-400 py-8">Belum ada data</p>
            @endif
        </div>

        {{-- Menu Terlaris --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">Menu Terlaris</h3>
            <div class="space-y-3">
                @forelse($perMenu->take(8) as $i => $m)
                @php $pct = $perMenu->first()->total_qty > 0 ? ($m->total_qty / $perMenu->first()->total_qty) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">
                            @if($i === 0) @elseif($i === 1) @elseif($i === 2) @else {{ $i+1 }}. @endif
                            {{ $m->nama_menu }}
                        </span>
                        <span class="text-gray-500">{{ $m->total_qty }} porsi</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full">
                        <div class="h-2 bg-gradient-to-r from-blue-500 to-orange-400 rounded-full transition-all"
                            style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 py-6">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabel Rekap Harian --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-5 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Rincian Harian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="text-left px-5 py-3">Tanggal</th>
                        <th class="text-right px-4 py-3">Transaksi</th>
                        <th class="text-right px-4 py-3">Diskon</th>
                        <th class="text-right px-5 py-3">Omzet</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($harian as $h)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3.5 font-medium text-gray-800">
                            {{ \Carbon\Carbon::parse($h->tgl)->isoFormat('dddd, D MMMM Y') }}
                        </td>
                        <td class="text-right px-4 py-3.5 text-gray-600">{{ $h->jml_transaksi }}x</td>
                        <td class="text-right px-4 py-3.5 text-orange-500">Rp {{ number_format($h->total_diskon, 0, ',', '.') }}</td>
                        <td class="text-right px-5 py-3.5 font-bold text-gray-800">Rp {{ number_format($h->omzet, 0, ',', '.') }}</td>
                        <td class="text-center px-4 py-3.5">
                            <a href="{{ route('laporan.detail', ['tanggal' => $h->tgl]) }}"
                                class="text-xs text-blue-500 hover:text-blue-700 font-medium">
                                Lihat Detail →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400">
                            <i class="fas fa-chart-bar text-3xl mb-2 block"></i>
                            Belum ada transaksi pada periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($harian->count() > 0)
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td class="px-5 py-3 text-gray-700">Total</td>
                        <td class="text-right px-4 py-3 text-gray-700">{{ $summary['total_transaksi'] }}x</td>
                        <td class="text-right px-4 py-3 text-orange-600">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</td>
                        <td class="text-right px-5 py-3 text-blue-600 text-base">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
