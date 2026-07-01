<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Menu;
use App\Models\TransaksiDetail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $omzetHariIni = Transaksi::where('status', 'selesai')
            ->whereDate('tanggal', $today)
            ->sum('total');

        $transaksiHariIni = Transaksi::where('status', 'selesai')
            ->whereDate('tanggal', $today)
            ->count();

        $labaHariIni = TransaksiDetail::whereHas('transaksi', function ($q) use ($today) {
            $q->where('status', 'selesai')->whereDate('tanggal', $today);
        })->sum('laba');

        $omzetBulanIni = Transaksi::where('status', 'selesai')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total');

        $menuTerlaris = TransaksiDetail::whereHas('transaksi', function ($q) use ($today) {
                $q->where('status', 'selesai')->whereDate('tanggal', $today);
            })
            ->selectRaw('nama_menu, SUM(qty) as total_qty')
            ->groupBy('nama_menu')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $transaksiTerbaru = Transaksi::with('details')
            ->where('status', 'selesai')
            ->orderBy('tanggal', 'desc')
            ->take(8)
            ->get();

        $totalMenu = Menu::where('tersedia', true)->count();

        return view('dashboard', compact(
            'omzetHariIni', 'transaksiHariIni', 'labaHariIni',
            'omzetBulanIni', 'menuTerlaris', 'transaksiTerbaru', 'totalMenu'
        ));
    }
}
