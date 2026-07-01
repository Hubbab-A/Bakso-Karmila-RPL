@extends('layouts.app')
@section('title', isset($menu) ? 'Edit Menu' : 'Tambah Menu')
@section('page-title', isset($menu) ? '✏️ Edit Menu' : '➕ Tambah Menu Baru')

@section('content')
<div class="max-w-3xl" x-data="menuForm()">
    <div class="bg-white rounded-2xl shadow-sm p-6 space-y-6">

        <form method="POST" action="{{ isset($menu) ? route('menu.update', $menu) : route('menu.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($menu)) @method('PUT') @endif

            {{-- Info Dasar --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Menu <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $menu->nama ?? '') }}" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Bakso Polos, Mie Ayam, dll">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="kategori" value="{{ old('kategori', $menu->kategori ?? 'makanan') }}" required list="kategori-list"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <datalist id="kategori-list">
                        <option value="makanan">
                        <option value="minuman">
                        <option value="snack">
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Keterangan singkat menu">{{ old('deskripsi', $menu->deskripsi ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Margin Keuntungan (%)</label>
                    <input type="number" name="margin_persen"
                        value="{{ old('margin_persen', $menu->margin_persen ?? 30) }}"
                        required min="0" max="99" step="0.5"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <p class="text-xs text-gray-400 mt-1">Harga Jual = HPP ÷ (1 − Margin%)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto (opsional)</label>
                    <input type="file" name="foto" accept="image/*"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            {{-- Status (hanya saat edit) --}}
            @if(isset($menu))
            <div class="flex items-center gap-3">
                <input type="hidden" name="tersedia" value="0">
                <input type="checkbox" id="tersedia" name="tersedia" value="1"
                    {{ old('tersedia', $menu->tersedia) ? 'checked' : '' }}
                    class="w-4 h-4 rounded focus:ring-blue-400">
                <label for="tersedia" class="text-sm text-gray-700">Menu tersedia (tampil di kasir)</label>
            </div>
            @endif

            {{-- ===== RESEP BAHAN BAKU ===== --}}
            <div>
                <div class="flex justify-between items-center mb-3">
                    <label class="text-sm font-medium text-gray-700">🧂 Resep Bahan Baku (per porsi)</label>
                    <button type="button" onclick="tambahBahanBaru()"
                        class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                        + Tambah Bahan
                    </button>
                </div>

                <div id="bahan-container" class="space-y-2">

                    {{-- Bahan existing saat edit --}}
                    @if(isset($menu) && $menu->menuBahan->count() > 0)
                        @foreach($menu->menuBahan as $index => $mb)
                        <div class="bahan-row grid grid-cols-12 gap-2 items-center bg-gray-50 rounded-lg p-3">
                            {{-- Pilih Bahan --}}
                            <select name="bahan[{{ $index }}][id]"
                                class="col-span-5 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
                                onchange="updateBiayaRow(this)">
                                <option value="">— Pilih Bahan —</option>
                                @foreach($bahanBakus as $b)
                                <option value="{{ $b->id }}"
                                    data-harga="{{ $b->harga_per_satuan }}"
                                    data-satuan="{{ $b->satuan }}"
                                    {{ $b->id == $mb->bahan_baku_id ? 'selected' : '' }}>
                                    {{ $b->nama }} (Rp {{ number_format($b->harga_per_satuan,0,',','.') }}/{{ $b->satuan }})
                                </option>
                                @endforeach
                            </select>
                            {{-- Jumlah --}}
                            <input type="number"
                                name="bahan[{{ $index }}][jumlah]"
                                value="{{ $mb->jumlah }}"
                                step="0.001" min="0.001"
                                placeholder="Jumlah"
                                class="col-span-2 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
                                onchange="updateBiayaRow(this.closest('.bahan-row').querySelector('select'))">
                            {{-- Satuan Porsi --}}
                            <select name="bahan[{{ $index }}][satuan_porsi]"
                                class="col-span-2 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
                                onchange="updateBiayaRow(this.closest('.bahan-row').querySelector('select'))">
                                @foreach(['gram','kg','ml','liter','sdm','sdt','buah','siung','lembar','paket','sachet'] as $s)
                                <option value="{{ $s }}" {{ $mb->satuan_porsi === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            {{-- Biaya --}}
                            <span class="col-span-2 biaya-display text-xs font-bold text-blue-600 text-right">
                                Rp {{ number_format($mb->biaya, 0, ',', '.') }}
                            </span>
                            {{-- Hapus --}}
                            <button type="button" onclick="this.closest('.bahan-row').remove()"
                                class="col-span-1 text-red-400 hover:text-red-600 text-xl font-bold text-center">×</button>
                        </div>
                        @endforeach
                    @endif

                </div>

                <p class="text-xs text-gray-400 mt-2">
                    💡 Setiap menu bisa punya takaran bahan yang berbeda. Contoh: Bakso Jumbo = 100gr daging, Bakso Original = 50gr daging.
                </p>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('menu.index') }}"
                    class="flex-1 text-center bg-gray-100 text-gray-700 py-3 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-xl text-sm font-bold hover:bg-blue-700 transition">
                    {{ isset($menu) ? '💾 Simpan Perubahan' : '✅ Tambah Menu & Hitung HPP' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let bahanIndex = {{ isset($menu) ? $menu->menuBahan->count() : 0 }};

// Data semua bahan baku
const semuaBahan = {
    @foreach($bahanBakus as $b)
    {{ $b->id }}: {
        nama: '{{ addslashes($b->nama) }}',
        harga: {{ $b->harga_per_satuan }},
        satuan: '{{ $b->satuan }}'
    },
    @endforeach
};

const satuanOptions = ['gram','kg','ml','liter','sdm','sdt','buah','siung','lembar','paket','sachet'];

// Faktor konversi ke satuan dasar
const faktorKonversi = {
    kg: 1, liter: 1, buah: 1, paket: 1, sachet: 1, siung: 1, lembar: 1,
    gram: 0.001, gr: 0.001, ml: 0.001,
    ons: 0.1, sdm: 0.015, sdt: 0.005,
};

function tambahBahanBaru() {
    const container = document.getElementById('bahan-container');
    const idx = bahanIndex++;

    const satuanOpts = satuanOptions
        .map(s => `<option value="${s}">${s}</option>`)
        .join('');

    const bahanOpts = Object.entries(semuaBahan)
        .map(([id, b]) => `<option value="${id}" data-harga="${b.harga}" data-satuan="${b.satuan}">${b.nama} (Rp ${Number(b.harga).toLocaleString('id-ID')}/${b.satuan})</option>`)
        .join('');

    const row = document.createElement('div');
    row.className = 'bahan-row grid grid-cols-12 gap-2 items-center bg-gray-50 rounded-lg p-3';
    row.innerHTML = `
        <select name="bahan[${idx}][id]"
            class="col-span-5 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
            onchange="updateBiayaRow(this)">
            <option value="">— Pilih Bahan —</option>
            ${bahanOpts}
        </select>
        <input type="number"
            name="bahan[${idx}][jumlah]"
            step="0.001" min="0.001"
            placeholder="Jumlah"
            class="col-span-2 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
            onchange="updateBiayaRow(this.closest('.bahan-row').querySelector('select'))">
        <select name="bahan[${idx}][satuan_porsi]"
            class="col-span-2 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"
            onchange="updateBiayaRow(this.closest('.bahan-row').querySelector('select[name*=\\'[id]\\']'))">
            ${satuanOpts}
        </select>
        <span class="col-span-2 biaya-display text-xs font-bold text-blue-600 text-right">Rp 0</span>
        <button type="button" onclick="this.closest('.bahan-row').remove()"
            class="col-span-1 text-red-400 hover:text-red-600 text-xl font-bold text-center">×</button>
    `;
    container.appendChild(row);
}

function updateBiayaRow(selectEl) {
    const row = selectEl.closest('.bahan-row');
    const selected = selectEl.options[selectEl.selectedIndex];
    const harga = parseFloat(selected?.dataset?.harga || 0);

    const jumlahInput = row.querySelector('input[type=number]');
    const jumlah = parseFloat(jumlahInput?.value || 0);

    const satuanSelect = row.querySelector('select[name*="[satuan_porsi]"]');
    const satuan = satuanSelect ? satuanSelect.value : 'gram';

    const faktor = faktorKonversi[satuan] ?? 1;
    const biaya = harga * jumlah * faktor;

    row.querySelector('.biaya-display').textContent =
        'Rp ' + Math.round(biaya).toLocaleString('id-ID');
}
</script>
@endpush
@endsection