@extends('layouts.app')
@section('title', 'Kalkulator HPP')
@section('page-title', '🧮 Kalkulator HPP — Full Costing')

@section('content')
<div x-data="{ modalOverhead: false, modalTK: false }" class="space-y-6">

    {{-- Banner penjelasan Full Costing --}}
    <div class="bg-gradient-to-r from-blue-600 to-orange-500 rounded-2xl p-6 text-white">
        <h2 class="text-xl font-bold mb-2">Metode Full Costing</h2>
        <p class="text-blue-100 text-sm leading-relaxed">
            HPP dihitung dari <strong>3 komponen biaya</strong>: (1) Biaya Bahan Baku langsung per porsi,
            (2) Biaya Tenaga Kerja dialokasikan per porsi, dan (3) Biaya Overhead tetap dialokasikan per porsi.
            Harga jual = <strong>HPP ÷ (1 − Margin%)</strong>
        </p>
        <div class="mt-4 grid grid-cols-3 gap-4 text-center">
            <div class="bg-white/20 rounded-xl p-3">
                <p class="text-2xl font-bold">Rp {{ number_format($totalOverhead, 0, ',', '.') }}</p>
                <p class="text-xs mt-1">Total Overhead/Bulan</p>
            </div>
            <div class="bg-white/20 rounded-xl p-3">
                <p class="text-2xl font-bold">Rp {{ number_format($totalTK, 0, ',', '.') }}</p>
                <p class="text-xs mt-1">Total Tenaga Kerja/Bulan</p>
            </div>
            <div class="bg-white/20 rounded-xl p-3">
                <p class="text-2xl font-bold">{{ number_format($estimasiPorsi) }}</p>
                <p class="text-xs mt-1">Estimasi Porsi/Bulan</p>
            </div>
        </div>
        <div class="mt-4 bg-white/20 rounded-xl p-4 text-sm grid grid-cols-2 gap-3">
            <div>→ Overhead per porsi: <strong>Rp {{ number_format($overheadPerPorsi, 0, ',', '.') }}</strong></div>
            <div>→ TK per porsi: <strong>Rp {{ number_format($tkPerPorsi, 0, ',', '.') }}</strong></div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-5">

        {{-- Biaya Overhead --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">⚡ Biaya Overhead</h3>
                <button @click="modalOverhead = true" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">+ Tambah</button>
            </div>
            <div class="space-y-2">
                @foreach($overheads as $o)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2.5 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $o->aktif ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                        <span class="{{ $o->aktif ? 'text-gray-700' : 'text-gray-400 line-through' }}">{{ $o->nama }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600 font-medium">Rp {{ number_format($o->biaya_per_bulan, 0, ',', '.') }}</span>
                        <form method="POST" action="{{ route('hpp.overhead.destroy', $o) }}">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus?')" class="text-blue-400 hover:text-blue-600 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t text-sm font-bold flex justify-between text-blue-600">
                <span>Total</span>
                <span>Rp {{ number_format($totalOverhead, 0, ',', '.') }}/bln</span>
            </div>
        </div>

        {{-- Biaya Tenaga Kerja --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">👷 Tenaga Kerja</h3>
                <button @click="modalTK = true" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">+ Tambah</button>
            </div>
            <div class="space-y-2">
                @foreach($tenagaKerjas as $tk)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2.5 text-sm">
                    <span class="text-gray-700">{{ $tk->nama }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600 font-medium">Rp {{ number_format($tk->gaji_per_bulan, 0, ',', '.') }}</span>
                        <form method="POST" action="{{ route('hpp.tk.destroy', $tk) }}">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus?')" class="text-blue-400 hover:text-blue-600 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t text-sm font-bold flex justify-between text-blue-600">
                <span>Total</span>
                <span>Rp {{ number_format($totalTK, 0, ',', '.') }}/bln</span>
            </div>
        </div>

        {{-- Estimasi Porsi --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">📊 Estimasi Porsi</h3>
            <form method="POST" action="{{ route('hpp.estimasi') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm text-gray-600 block mb-2">Total porsi terjual per bulan</label>
                    <input type="number" name="estimasi_porsi_bulan" value="{{ $estimasiPorsi }}" min="1"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <p class="text-xs text-gray-400 mt-1">Digunakan untuk alokasi biaya tetap per porsi</p>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Simpan Estimasi
                </button>
            </form>

            <div class="mt-4 pt-4 border-t">
                <form method="POST" action="{{ route('hpp.recalculate') }}">
                    @csrf
                    <button type="submit" class="w-full bg-orange-500 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-orange-600 transition">
                        <i class="fas fa-sync mr-2"></i>Hitung Ulang HPP Semua Menu
                    </button>
                    <p class="text-xs text-gray-400 mt-2 text-center">Update HPP & harga jual berdasarkan data terkini</p>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel HPP per Menu --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-5 border-b">
            <h3 class="font-bold text-gray-800">📋 HPP per Menu (Full Costing)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="text-left px-5 py-3">Menu</th>
                        <th class="text-right px-4 py-3">Biaya Bahan</th>
                        <th class="text-right px-4 py-3">Biaya TK</th>
                        <th class="text-right px-4 py-3">Biaya Overhead</th>
                        <th class="text-right px-4 py-3 font-bold text-gray-700">HPP</th>
                        <th class="text-right px-4 py-3">Margin</th>
                        <th class="text-right px-5 py-3 font-bold text-blue-600">Harga Jual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($menus as $menu)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800">{{ $menu->nama }}</p>
                            <p class="text-xs text-gray-400 capitalize">{{ $menu->kategori }}</p>
                        </td>
                        <td class="text-right px-4 py-4 text-gray-600">Rp {{ number_format($menu->biaya_bahan_baku, 0, ',', '.') }}</td>
                        <td class="text-right px-4 py-4 text-gray-600">Rp {{ number_format($menu->biaya_tenaga_kerja, 0, ',', '.') }}</td>
                        <td class="text-right px-4 py-4 text-gray-600">Rp {{ number_format($menu->biaya_overhead, 0, ',', '.') }}</td>
                        <td class="text-right px-4 py-4 font-bold text-gray-800">Rp {{ number_format($menu->hpp, 0, ',', '.') }}</td>
                        <td class="text-right px-4 py-4">
                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs">{{ $menu->margin_persen }}%</span>
                        </td>
                        <td class="text-right px-5 py-4 font-bold text-blue-600 text-base">Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah Overhead --}}
<div x-show="modalOverhead" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm" @click.outside="modalOverhead = false">
        <h3 class="font-bold text-gray-800 mb-4">Tambah Biaya Overhead</h3>
        <form method="POST" action="{{ route('hpp.overhead.store') }}" class="space-y-3">
            @csrf
            <input name="nama" placeholder="Nama biaya (misal: Listrik)" required
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="number" name="biaya_per_bulan" placeholder="Biaya per bulan (Rp)" required min="0"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <div class="flex gap-2 pt-2">
                <button type="button" @click="modalOverhead = false" class="flex-1 bg-gray-100 py-2.5 rounded-lg text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium">Tambah</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah TK --}}
<div x-show="modalTK" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm" @click.outside="modalTK = false">
        <h3 class="font-bold text-gray-800 mb-4">Tambah Tenaga Kerja</h3>
        <form method="POST" action="{{ route('hpp.tk.store') }}" class="space-y-3">
            @csrf
            <input name="nama" placeholder="Nama/jabatan (misal: Koki)" required
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="number" name="gaji_per_bulan" placeholder="Gaji per bulan (Rp)" required min="0"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <div class="flex gap-2 pt-2">
                <button type="button" @click="modalTK = false" class="flex-1 bg-gray-100 py-2.5 rounded-lg text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium">Tambah</button>
            </div>
        </form>
    </div>
</div>

@endsection
