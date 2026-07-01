<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    public function index()
    {
        $menus = Menu::where('tersedia', true)->orderBy('kategori')->get()->groupBy('kategori');
        $setting = [
            'nama_warung' => AppSetting::get('nama_warung', 'Warung Bakso'),
        ];
        return view('kasir.index', compact('menus', 'setting'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items'         => 'required|array|min:1',
            'items.*.id'    => 'required|exists:menus,id',
            'items.*.qty'   => 'required|integer|min:1',
            'bayar'         => 'required|numeric|min:0',
            'metode_bayar'  => 'required|in:tunai,transfer,qris',
            'diskon'        => 'nullable|numeric|min:0',
            'catatan'       => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $items      = collect($request->items);
            $menuIds    = $items->pluck('id');
            $menus      = Menu::whereIn('id', $menuIds)->get()->keyBy('id');

            $subtotal = 0;
            $detailData = [];

            foreach ($items as $item) {
                $menu     = $menus[$item['id']];
                $qty      = (int) $item['qty'];
                $harga    = (float) $menu->harga_jual;
                $hpp      = (float) $menu->hpp;
                $sub      = $harga * $qty;
                $laba     = ($harga - $hpp) * $qty;

                $subtotal += $sub;
                $detailData[] = [
                    'menu_id'    => $menu->id,
                    'nama_menu'  => $menu->nama,
                    'harga_jual' => $harga,
                    'hpp'        => $hpp,
                    'qty'        => $qty,
                    'subtotal'   => $sub,
                    'laba'       => $laba,
                ];
            }

            $diskon    = (float) ($request->diskon ?? 0);
            $total     = $subtotal - $diskon;
            $bayar     = (float) $request->bayar;
            $kembalian = $bayar - $total;

            $transaksi = Transaksi::create([
                'no_nota'      => Transaksi::generateNoNota(),
                'user_id'      => Auth::id(),
                'subtotal'     => $subtotal,
                'diskon'       => $diskon,
                'total'        => $total,
                'bayar'        => $bayar,
                'kembalian'    => $kembalian,
                'metode_bayar' => $request->metode_bayar,
                'status'       => 'selesai',
                'catatan'      => $request->catatan,
                'tanggal'      => now(),
            ]);

            foreach ($detailData as &$d) {
                $d['transaksi_id'] = $transaksi->id;
                $d['created_at']   = now();
                $d['updated_at']   = now();
            }
            TransaksiDetail::insert($detailData);

            DB::commit();

            return response()->json([
                'success'     => true,
                'no_nota'     => $transaksi->no_nota,
                'transaksi_id' => $transaksi->id,
                'message'     => 'Transaksi berhasil!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function nota($id)
    {
        $transaksi = Transaksi::with(['details', 'user'])->findOrFail($id);
        $setting = AppSetting::pluck('value', 'key');
        return view('kasir.nota', compact('transaksi', 'setting'));
    }
}
