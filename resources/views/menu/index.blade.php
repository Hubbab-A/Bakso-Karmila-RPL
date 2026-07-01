@extends('layouts.app')
@section('title', 'Kelola Menu')
@section('page-title', '🍜 Kelola Menu')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $menus->count() }} menu terdaftar</p>
        <a href="{{ route('menu.create') }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Menu
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <th class="text-left px-5 py-3">Menu</th>
                    <th class="text-left px-4 py-3">Kategori</th>
                    <th class="text-right px-4 py-3">HPP</th>
                    <th class="text-right px-4 py-3">Margin</th>
                    <th class="text-right px-4 py-3">Harga Jual</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-center px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($menus as $menu)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-lg">
                                {{ str_contains(strtolower($menu->kategori), 'minum') ? '🥤' : '🍜' }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $menu->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $menu->menuBahan->count() }} bahan</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full text-xs capitalize">{{ $menu->kategori }}</span>
                    </td>
                    <td class="text-right px-4 py-4 text-gray-600">Rp {{ number_format($menu->hpp, 0, ',', '.') }}</td>
                    <td class="text-right px-4 py-4">
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs">{{ $menu->margin_persen }}%</span>
                    </td>
                    <td class="text-right px-4 py-4 font-bold text-blue-600">Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center px-4 py-4">
                        <button x-data
                            @click="
                                fetch('{{ route('menu.toggle', $menu) }}', {method:'PATCH',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
                                    .then(r=>r.json())
                                    .then(d=>{$el.className = d.tersedia ? 'bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium' : 'bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-medium'; $el.textContent = d.tersedia ? 'Tersedia' : 'Habis'})
                            "
                            class="{{ $menu->tersedia ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} px-3 py-1 rounded-full text-xs font-medium cursor-pointer">
                            {{ $menu->tersedia ? 'Tersedia' : 'Habis' }}
                        </button>
                    </td>
                    <td class="text-center px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('menu.edit', $menu) }}" class="text-blue-500 hover:text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-50 text-xs transition">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('menu.destroy', $menu) }}" onsubmit="return confirm('Hapus menu {{ $menu->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-blue-400 hover:text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-50 text-xs transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-16 text-gray-400">
                        <i class="fas fa-bowl-food text-4xl mb-3 block"></i>
                        <p>Belum ada menu. <a href="{{ route('menu.create') }}" class="text-blue-500 hover:underline">Tambah sekarang</a></p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
