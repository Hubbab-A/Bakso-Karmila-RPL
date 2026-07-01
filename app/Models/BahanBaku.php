<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';
    protected $fillable = [
        'nama', 'satuan', 'harga_per_satuan',
    ];

    protected $casts = [
        'harga_per_satuan' => 'decimal:2',
    ];

    // Faktor konversi ke satuan dasar
    public static function konversiFaktor(string $satuan): float
    {
        return match (strtolower($satuan)) {
            'kg', 'liter', 'buah', 'paket', 'sachet' => 1,
            'gram', 'gr'  => 0.001,
            'ons'         => 0.1,
            'ml'          => 0.001,
            'sdm'         => 0.015,
            'sdt'         => 0.005,
            'siung'       => 1,
            'lembar'      => 1,
            default       => 1,
        };
    }

    // Hitung biaya berdasarkan jumlah & satuan porsi
    public function hitungBiaya(float $jumlah, string $satuanPorsi): float
    {
        $faktor = self::konversiFaktor($satuanPorsi);
        $jumlahDasar = $jumlah * $faktor;
        return $this->harga_per_satuan * $jumlahDasar;
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_bahan')
            ->withPivot(['jumlah', 'satuan_porsi', 'biaya'])
            ->withTimestamps();
    }
}