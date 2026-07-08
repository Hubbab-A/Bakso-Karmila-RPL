@extends('layouts.app')
@section('title', 'Kasir')
@section('page-title', 'Kasir / Point of Sale')

@section('content')
<div x-data="posSystem()" class="flex gap-4 h-[calc(100vh-140px)]">

    {{-- ===== PANEL KIRI: MENU ===== --}}
    <div class="flex-1 flex flex-col gap-3 overflow-hidden">

        {{-- Search & Filter Kategori --}}
        <div class="bg-white rounded-xl shadow-sm p-4 flex gap-3 items-center">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Cari menu..."
                    x-model="search"
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button @click="filterKategori = ''"
                    :class="filterKategori === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-3 py-2 rounded-lg text-xs font-medium transition">Semua</button>
                @foreach($menus as $kategori => $items)
                <button @click="filterKategori = '{{ $kategori }}'"
                    :class="filterKategori === '{{ $kategori }}' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-3 py-2 rounded-lg text-xs font-medium capitalize transition">{{ $kategori }}</button>
                @endforeach
            </div>
        </div>

        {{-- Grid Menu --}}
        <div class="flex-1 overflow-y-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 content-start">
            @foreach($menus as $kategori => $items)
                @foreach($items as $menu)
                <div x-show="
                    (filterKategori === '' || filterKategori === '{{ $kategori }}') &&
                    (search === '' || '{{ strtolower($menu->nama) }}'.includes(search.toLowerCase()))
                "
                    @click="tambahItem({{ $menu->id }}, '{{ addslashes($menu->nama) }}', {{ $menu->harga_jual }})"
                    class="bg-white rounded-xl shadow-sm p-4 cursor-pointer hover:shadow-md hover:border-blue-400 border-2 border-transparent transition-all active:scale-95">
                    <div class="w-full h-24 rounded-lg mb-3 overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                        @if($menu->foto)
                            <img src="{{ asset('storage/' . $menu->foto) }}"
                                alt="{{ $menu->nama }}"
                                class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl">
                                {{ str_contains(strtolower($kategori), 'minum') ? '🥤' : '🍜' }}
                            </span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800 text-sm leading-tight">{{ $menu->nama }}</h3>
                    <p class="text-blue-600 font-bold text-sm mt-1">Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}</p>
                    <p class="text-gray-400 text-xs">HPP: Rp {{ number_format($menu->hpp, 0, ',', '.') }}</p>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- ===== PANEL KANAN: KERANJANG ===== --}}
    <div class="w-96 bg-white rounded-xl shadow-sm flex flex-col">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="font-bold text-gray-800"><i class="fas fa-shopping-cart mr-2 text-blue-500"></i>Pesanan</h2>
            <button @click="clearCart()" x-show="keranjang.length > 0"
                class="text-xs text-blue-400 hover:text-blue-600">Kosongkan</button>
        </div>

        {{-- Item Keranjang --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-2">
            <template x-if="keranjang.length === 0">
                <div class="text-center text-gray-400 py-12">
                    <i class="fas fa-bowl-food text-4xl mb-3 block"></i>
                    <p class="text-sm">Pilih menu untuk mulai</p>
                </div>
            </template>

            <template x-for="(item, idx) in keranjang" :key="item.id">
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800" x-text="item.nama"></p>
                        <p class="text-xs text-blue-500 font-semibold" x-text="'Rp ' + formatRupiah(item.harga)"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="kurangiQty(idx)"
                            class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200 transition text-sm">−</button>
                        <span class="text-sm font-bold w-5 text-center" x-text="item.qty"></span>
                        <button @click="tambahQty(idx)"
                            class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200 transition text-sm">+</button>
                    </div>
                    <p class="text-sm font-bold text-gray-700 w-20 text-right" x-text="'Rp ' + formatRupiah(item.harga * item.qty)"></p>
                </div>
            </template>
        </div>

        {{-- Summary & Checkout --}}
        <div class="p-4 border-t space-y-3">
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span x-text="'Rp ' + formatRupiah(subtotal)"></span>
                </div>
                <div class="flex justify-between items-center text-gray-600">
                    <span>Diskon</span>
                    <input type="number" x-model.number="diskon" min="0"
                        class="w-28 text-right border border-gray-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                </div>
                <div class="flex justify-between font-bold text-gray-800 text-base border-t pt-2">
                    <span>Total</span>
                    <span x-text="'Rp ' + formatRupiah(total)" class="text-blue-600"></span>
                </div>
            </div>

            {{-- Metode Bayar --}}
            <div class="grid grid-cols-3 gap-1.5">
                <button @click="metodeBayar = 'tunai'"
                    :class="metodeBayar === 'tunai' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                    class="py-2 rounded-lg text-xs font-medium transition">Tunai</button>
                <button @click="metodeBayar = 'transfer'"
                    :class="metodeBayar === 'transfer' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                    class="py-2 rounded-lg text-xs font-medium transition">Transfer</button>
                <button @click="metodeBayar = 'qris'"
                    :class="metodeBayar === 'qris' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                    class="py-2 rounded-lg text-xs font-medium transition">QRIS</button>
            </div>

            {{-- Bayar --}}
            <div x-show="metodeBayar === 'tunai'" class="space-y-2">
                <label class="text-xs text-gray-500">Uang Bayar</label>
                <input type="number" x-model.number="bayar" :min="total" placeholder="0"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <div class="flex justify-between text-sm" x-show="bayar >= total">
                    <span class="text-gray-600">Kembalian</span>
                    <span class="font-bold text-green-600" x-text="'Rp ' + formatRupiah(bayar - total)"></span>
                </div>
                {{-- Quick amount buttons --}}
                <div class="grid grid-cols-4 gap-1">
                    <template x-for="n in quickAmounts">
                        <button @click="bayar = n" class="py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-medium transition"
                            x-text="'Rp' + formatShort(n)"></button>
                    </template>
                </div>
            </div>

            {{-- Catatan --}}
            <input type="text" x-model="catatan" placeholder="Catatan (opsional)..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">

            <button @click="checkout()"
                :disabled="keranjang.length === 0 || (metodeBayar === 'tunai' && bayar < total) || loading"
                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                <template x-if="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                </template>
                <template x-if="!loading">
                    <i class="fas fa-receipt"></i>
                </template>
                <span x-text="loading ? 'Memproses...' : 'Bayar & Cetak Nota'"></span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posSystem() {
    return {
        keranjang: [],
        search: '',
        filterKategori: '',
        diskon: 0,
        bayar: 0,
        metodeBayar: 'tunai',
        catatan: '',
        loading: false,
        quickAmounts: [5000, 10000, 20000, 50000],

        get subtotal() {
            return this.keranjang.reduce((sum, i) => sum + i.harga * i.qty, 0);
        },
        get total() {
            return Math.max(0, this.subtotal - this.diskon);
        },

        tambahItem(id, nama, harga) {
            const idx = this.keranjang.findIndex(i => i.id === id);
            if (idx >= 0) {
                this.keranjang[idx].qty++;
            } else {
                this.keranjang.push({ id, nama, harga, qty: 1 });
            }
        },
        tambahQty(idx) { this.keranjang[idx].qty++; },
        kurangiQty(idx) {
            if (this.keranjang[idx].qty <= 1) {
                this.keranjang.splice(idx, 1);
            } else {
                this.keranjang[idx].qty--;
            }
        },
        clearCart() {
            if (confirm('Kosongkan semua pesanan?')) {
                this.keranjang = [];
                this.diskon = 0;
                this.bayar = 0;
                this.catatan = '';
            }
        },

        async checkout() {
            if (this.keranjang.length === 0) return;
            this.loading = true;
            try {
                const res = await fetch('{{ route("kasir.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({
                        items: this.keranjang.map(i => ({ id: i.id, qty: i.qty })),
                        diskon: this.diskon,
                        bayar: this.metodeBayar === 'tunai' ? this.bayar : this.total,
                        metode_bayar: this.metodeBayar,
                        catatan: this.catatan,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    // Buka nota di tab baru untuk print
                    window.open(`/kasir/nota/${data.transaksi_id}?print=1`, '_blank');
                    this.keranjang = [];
                    this.diskon = 0;
                    this.bayar = 0;
                    this.catatan = '';
                } else {
                    alert('Gagal: ' + data.message);
                }
            } catch(e) {
                alert('Terjadi kesalahan: ' + e.message);
            }
            this.loading = false;
        },

        formatRupiah(n) {
            return new Intl.NumberFormat('id-ID').format(Math.round(n));
        },
        formatShort(n) {
            if (n >= 1000) return (n/1000) + 'rb';
            return n;
        }
    }
}
</script>
@endpush
