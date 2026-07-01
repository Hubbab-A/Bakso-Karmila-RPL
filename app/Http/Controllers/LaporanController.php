<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        $startDate = Carbon::create($tahun, $bln, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        // Rekap harian dalam bulan
        $harian = Transaksi::where('status', 'selesai')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('DATE(tanggal) as tgl, COUNT(*) as jml_transaksi, SUM(total) as omzet, SUM(diskon) as total_diskon')
            ->groupByRaw('DATE(tanggal)')
            ->orderBy('tgl')
            ->get();

        // Rekap per menu bulan ini
        $perMenu = TransaksiDetail::whereHas('transaksi', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'selesai')->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->selectRaw('nama_menu, SUM(qty) as total_qty, SUM(subtotal) as total_omzet, SUM(laba) as total_laba')
            ->groupBy('nama_menu')
            ->orderByDesc('total_qty')
            ->get();

        // Summary
        $summary = [
            'total_transaksi' => $harian->sum('jml_transaksi'),
            'total_omzet'     => $harian->sum('omzet'),
            'total_laba'      => $perMenu->sum('total_laba'),
            'total_diskon'    => $harian->sum('total_diskon'),
            'rata_per_hari'   => $harian->count() > 0 ? $harian->avg('omzet') : 0,
        ];

        // Daftar bulan tersedia untuk filter
        $bulanList = Transaksi::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->groupByRaw("DATE_FORMAT(tanggal, '%Y-%m')")
            ->orderBy('bulan', 'desc')
            ->pluck('bulan');

        return view('laporan.index', compact('harian', 'perMenu', 'summary', 'bulan', 'bulanList'));
    }

    public function detail(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());

        $transaksis = Transaksi::with(['details', 'user'])
            ->where('status', 'selesai')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalOmzet = $transaksis->sum('total');
        $totalLaba  = $transaksis->sum('total_laba');

        return view('laporan.detail', compact('transaksis', 'tanggal', 'totalOmzet', 'totalLaba'));
    }

    public function nota($id)
    {
        $transaksi = Transaksi::with(['details', 'user'])->findOrFail($id);
        $setting   = AppSetting::pluck('value', 'key');
        return view('kasir.nota', compact('transaksi', 'setting'));
    }
}
