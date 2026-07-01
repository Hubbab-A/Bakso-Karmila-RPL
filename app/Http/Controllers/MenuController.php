<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\BahanBaku;
use App\Models\MenuBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('menuBahan.bahanBaku')->orderBy('kategori')->get();
        return view('menu.index', compact('menus'));
    }

    public function create()
    {
        $bahanBakus = BahanBaku::orderBy('nama')->get();
        return view('menu.create', compact('bahanBakus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:100',
            'kategori'      => 'required|string|max:50',
            'margin_persen' => 'required|numeric|min:0|max:99',
            'foto'          => 'nullable|image|max:2048',
            'bahan'         => 'nullable|array',
        ]);

        $data = $request->only('nama', 'kategori', 'deskripsi', 'margin_persen');
        $data['tersedia'] = true;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('menus', 'public');
        }

        $menu = Menu::create($data);

        $this->simpanResep($menu, $request->bahan ?? []);

        try {
            $menu->hitungHpp();
        } catch (\Exception $e) {}

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        $menu->load('menuBahan.bahanBaku');
        $bahanBakus = BahanBaku::orderBy('nama')->get();
        return view('menu.edit', compact('menu', 'bahanBakus'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama'          => 'required|string|max:100',
            'kategori'      => 'required|string|max:50',
            'margin_persen' => 'required|numeric|min:0|max:99',
            'foto'          => 'nullable|image|max:2048',
            'bahan'         => 'nullable|array',
        ]);

        $data = $request->only('nama', 'kategori', 'deskripsi', 'margin_persen');
        $data['tersedia'] = $request->boolean('tersedia');

        if ($request->hasFile('foto')) {
            if ($menu->foto) Storage::disk('public')->delete($menu->foto);
            $data['foto'] = $request->file('foto')->store('menus', 'public');
        }

        $menu->update($data);

        // Hapus resep lama, simpan yang baru
        $menu->menuBahan()->delete();
        $this->simpanResep($menu, $request->bahan ?? []);

        try {
            $menu->hitungHpp();
        } catch (\Exception $e) {}

        return redirect()->route('menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    // Helper simpan resep bahan
    private function simpanResep(Menu $menu, array $bahans): void
    {
        foreach ($bahans as $bahan) {
            if (empty($bahan['id']) || empty($bahan['jumlah']) || empty($bahan['satuan_porsi'])) continue;

            $bahanBaku = BahanBaku::find($bahan['id']);
            if (!$bahanBaku) continue;

            $biaya = $bahanBaku->hitungBiaya(
                (float) $bahan['jumlah'],
                $bahan['satuan_porsi']
            );

            MenuBahan::create([
                'menu_id'       => $menu->id,
                'bahan_baku_id' => $bahan['id'],
                'jumlah'        => $bahan['jumlah'],
                'satuan_porsi'  => $bahan['satuan_porsi'],
                'biaya'         => $biaya,
            ]);
        }
    }

    public function destroy(Menu $menu)
    {
        if ($menu->foto) Storage::disk('public')->delete($menu->foto);
        $menu->delete();
        return back()->with('success', 'Menu dihapus!');
    }

    public function toggleTersedia(Menu $menu)
    {
        $menu->update(['tersedia' => !$menu->tersedia]);
        return response()->json(['tersedia' => $menu->tersedia]);
    }
}