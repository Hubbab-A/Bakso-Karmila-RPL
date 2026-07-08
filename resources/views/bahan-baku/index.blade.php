@extends('layouts.app')
@section('title', 'Bahan Baku')
@section('page-title', 'Master Bahan Baku')

@section('content')
<div x-data="{ modalTambah: false }" class="space-y-4">

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">Daftar bahan baku beserta harga satuan dasar</p>
        <button @click="modalTambah = true"
            class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Tambah Bahan Baku
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <th class="text-left px-5 py-3">Nama Bahan</th>
                    <th class="text-center px-4 py-3">Satuan Dasar</th>
                    <th class="text-right px-4 py-3">Harga / Satuan</th>
                    <th class="text-center px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bahanBakus as $b)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5 font-medium text-gray-800">{{ $b->nama }}</td>
                    <td class="text-center px-4 py-3.5">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">{{ $b->satuan }}</span>
                    </td>
                    <td class="text-right px-4 py-3.5 font-semibold text-gray-700">
                        Rp {{ number_format($b->harga_per_satuan, 0, ',', '.') }}/{{ $b->satuan }}
                    </td>
                    <td class="text-center px-5 py-3.5">
                        <div class="flex items-center justify-center gap-3">
                            <button @click="
                                modalTambah = true;
                                $nextTick(() => {
                                    document.getElementById('form-bahan').action = '/bahan-baku/{{ $b->id }}';
                                    document.getElementById('method-field').value = 'PUT';
                                    document.getElementById('modal-title').textContent = 'Edit Bahan Baku';
                                    document.getElementById('nama').value = '{{ addslashes($b->nama) }}';
                                    document.getElementById('satuan').value = '{{ $b->satuan }}';
                                    document.getElementById('harga_per_satuan').value = '{{ $b->harga_per_satuan }}';
                                })"
                                class="text-blue-500 hover:text-blue-700 text-xs font-medium">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('bahan-baku.destroy', $b) }}"
                                onsubmit="return confirm('Hapus {{ $b->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-12 text-gray-400">
                        <i class="fas fa-boxes-stacked text-3xl mb-2 block"></i>
                        Belum ada bahan baku
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah/Edit --}}
    <div x-show="modalTambah" x-cloak
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-sm" @click.outside="modalTambah = false; resetForm()">
            <h3 id="modal-title" class="font-bold text-gray-800 mb-4">Tambah Bahan Baku</h3>
            <form id="form-bahan" method="POST" action="{{ route('bahan-baku.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" id="method-field" name="_method" value="POST">

                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Nama Bahan</label>
                    <input id="nama" name="nama" placeholder="Contoh: Daging Sapi" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Satuan Dasar</label>
                        <select id="satuan" name="satuan" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="kg">kg</option>
                            <option value="liter">liter</option>
                            <option value="buah">buah</option>
                            <option value="paket">paket</option>
                            <option value="sachet">sachet</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Harga per Satuan (Rp)</label>
                        <input type="number" id="harga_per_satuan" name="harga_per_satuan"
                            placeholder="Contoh: 130000" required min="0"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <p class="text-xs text-blue-500 bg-blue-50 rounded-lg px-3 py-2">
                    💡 Jumlah per porsi diatur di masing-masing menu saat tambah/edit menu
                </p>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="modalTambah = false; resetForm()"
                        class="flex-1 bg-gray-100 py-2.5 rounded-lg text-sm">Batal</button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('form-bahan').reset();
    document.getElementById('form-bahan').action = "{{ route('bahan-baku.store') }}";
    document.getElementById('method-field').value = 'POST';
    document.getElementById('modal-title').textContent = 'Tambah Bahan Baku';
}
</script>
@endsection