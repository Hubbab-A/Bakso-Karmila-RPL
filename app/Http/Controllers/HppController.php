<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\BahanBaku;
use App\Models\MenuBahan;
use App\Models\OverheadSetting;
use App\Models\TenagaKerjaSetting;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HppController extends Controller
{
    /**
     * Halaman kalkulator HPP - ringkasan biaya overhead & TK
     */
    public function index()
    {
        $overheads     = OverheadSetting::all();
        $tenagaKerjas  = TenagaKerjaSetting::all();
        $totalOverhead = $overheads->where('aktif', true)->sum('biaya_per_bulan');
        $totalTK       = $tenagaKerjas->where('aktif', true)->sum('gaji_per_bulan');
        $estimasiPorsi = (int) AppSetting::get('estimasi_porsi_bulan', 600);

        $overheadPerPorsi = $estimasiPorsi > 0 ? $totalOverhead / $estimasiPorsi : 0;
        $tkPerPorsi       = $estimasiPorsi > 0 ? $totalTK / $estimasiPorsi : 0;

        $menus = Menu::with('menuBahan.bahanBaku')->get();

        return view('hpp.index', compact(
            'overheads', 'tenagaKerjas',
            'totalOverhead', 'totalTK',
            'estimasiPorsi', 'overheadPerPorsi', 'tkPerPorsi',
            'menus'
        ));
    }

    /**
     * Recalculate HPP semua menu
     */
    public function recalculateAll()
    {
        $menus = Menu::with('menuBahan')->get();
        foreach ($menus as $menu) {
            $menu->hitungHpp();
        }
        return back()->with('success', 'HPP semua menu berhasil dihitung ulang!');
    }

    // ---- Overhead CRUD ----

    public function storeOverhead(Request $request)
    {
        $request->validate(['nama' => 'required|string', 'biaya_per_bulan' => 'required|numeric|min:0']);
        OverheadSetting::create($request->only('nama', 'biaya_per_bulan') + ['aktif' => true]);
        return back()->with('success', 'Biaya overhead ditambahkan!');
    }

    public function updateOverhead(Request $request, OverheadSetting $overhead)
    {
        $request->validate(['nama' => 'required|string', 'biaya_per_bulan' => 'required|numeric|min:0']);
        $overhead->update($request->only('nama', 'biaya_per_bulan', 'aktif'));
        return back()->with('success', 'Overhead diperbarui!');
    }

    public function destroyOverhead(OverheadSetting $overhead)
    {
        $overhead->delete();
        return back()->with('success', 'Overhead dihapus!');
    }

    // ---- TK CRUD ----

    public function storeTK(Request $request)
    {
        $request->validate(['nama' => 'required|string', 'gaji_per_bulan' => 'required|numeric|min:0']);
        TenagaKerjaSetting::create($request->only('nama', 'gaji_per_bulan') + ['aktif' => true]);
        return back()->with('success', 'Data tenaga kerja ditambahkan!');
    }

    public function updateTK(Request $request, TenagaKerjaSetting $tk)
    {
        $request->validate(['nama' => 'required|string', 'gaji_per_bulan' => 'required|numeric|min:0']);
        $tk->update($request->only('nama', 'gaji_per_bulan', 'aktif'));
        return back()->with('success', 'Data TK diperbarui!');
    }

    public function destroyTK(TenagaKerjaSetting $tk)
    {
        $tk->delete();
        return back()->with('success', 'Data TK dihapus!');
    }

    // ---- Estimasi Porsi ----

    public function updateEstimasi(Request $request)
    {
        $request->validate(['estimasi_porsi_bulan' => 'required|integer|min:1']);
        AppSetting::set('estimasi_porsi_bulan', $request->estimasi_porsi_bulan);
        return back()->with('success', 'Estimasi porsi diperbarui!');
    }
}
