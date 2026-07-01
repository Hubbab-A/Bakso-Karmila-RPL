<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index()
    {
        $bahanBakus = BahanBaku::orderBy('nama')->get();
        return view('bahan-baku.index', compact('bahanBakus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:100',
            'satuan'           => 'required|string|max:20',
            'harga_per_satuan' => 'required|numeric|min:0',
            'jumlah_per_porsi' => 'required|numeric|min:0.001',
            'satuan_porsi'     => 'required|string|max:20',
        ]);

        $bahan = BahanBaku::create($request->only(
            'nama', 'satuan', 'harga_per_satuan',
            'jumlah_per_porsi', 'satuan_porsi'
        ));

        $bahan->hitungBiayaPerPorsi();

        return back()->with('success', 'Bahan baku ditambahkan!');
    }

    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $request->validate([
            'nama'             => 'required|string|max:100',
            'satuan'           => 'required|string|max:20',
            'harga_per_satuan' => 'required|numeric|min:0',
            'jumlah_per_porsi' => 'required|numeric|min:0.001',
            'satuan_porsi'     => 'required|string|max:20',
        ]);

        $bahanBaku->update($request->only(
            'nama', 'satuan', 'harga_per_satuan',
            'jumlah_per_porsi', 'satuan_porsi'
        ));

        $bahanBaku->hitungBiayaPerPorsi();

        return back()->with('success', 'Bahan baku diperbarui!');
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();
        return back()->with('success', 'Bahan baku dihapus!');
    }

    public function create() { return redirect()->route('bahan-baku.index'); }
    public function show($id) { return redirect()->route('bahan-baku.index'); }
    public function edit($id) { return redirect()->route('bahan-baku.index'); }
}