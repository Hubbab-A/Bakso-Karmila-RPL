@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('page-title', '📋 Detail Transaksi Harian')

@section('content')
<div class="space-y-4">

    {{-- Filter Tanggal --}}
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-sm text-gray-600 font-medium">Tanggal:</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                Tampilkan
            </button>
        </form>
        <a href="{{ route('laporan.index') }}" class="text-sm text-blue-500 hover:text-blue-700">
            ← Kembali ke Rekap Bulanan
        </a>
    </div>

    {{-- Summary Harian --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Jumlah Transaksi</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $transaksis->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Total Omzet</p>
            <p class="text-2xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wider">Total Laba</p>
            <p class="text-2xl font-bold text-green-600 mt-2">Rp {{ number_format($totalLaba, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- List Transaksi --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-5 border-b">
            <h3 class="font-bold text-gray-800">
                Transaksi — {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}
            </h3>
        </div>

        @forelse($transaksis as $t)
        <div class="border-b last:border-0 p-5 hover:bg-gray-50 transition">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-lg mt-0.5">🧾</div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-gray-800">{{ $t->no_nota }}</p>
                            <span class="bg-{{ $t->metode_bayar === 'tunai' ? 'green' : ($t->metode_bayar === 'qris' ? 'purple' : 'blue') }}-100 text-{{ $t->metode_bayar === 'tunai' ? 'green' : ($t->metode_bayar === 'qris' ? 'purple' : 'blue') }}-700 px-2 py-0.5 rounded-full text-xs capitalize">
                                {{ $t->metode_bayar }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $t->tanggal->format('H:i:s') }} · Kasir: {{ $t->user->name }}
                        </p>
                        {{-- Item detail --}}
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($t->details as $d)
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs">
                                {{ $d->nama_menu }} ×{{ $d->qty }}
                            </span>
                            @endforeach
                        </div>
                        @if($t->catatan)
                        <p class="text-xs text-gray-400 mt-1 italic">📝 {{ $t->catatan }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right ml-6">
                    <p class="font-bold text-gray-800 text-lg">Rp {{ number_format($t->total, 0, ',', '.') }}</p>
                    @if($t->diskon > 0)
                    <p class="text-xs text-orange-500">Diskon: Rp {{ number_format($t->diskon, 0, ',', '.') }}</p>
                    @endif
                    <p class="text-xs text-green-600 mt-0.5">Laba: Rp {{ number_format($t->total_laba, 0, ',', '.') }}</p>
                    <div class="flex gap-2 mt-2 justify-end">
                        <a href="{{ route('laporan.nota', $t->id) }}" target="_blank"
                           class="text-xs text-blue-500 hover:text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg flex items-center gap-1">
                            <i class="fas fa-print"></i> Cetak Ulang
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-receipt text-4xl mb-3 block"></i>
            <p>Tidak ada transaksi pada tanggal ini</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
