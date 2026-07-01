<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $fillable = [
        'transaksi_id', 'menu_id', 'nama_menu',
        'harga_jual', 'hpp', 'qty', 'subtotal', 'laba'
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'hpp'        => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'laba'       => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
