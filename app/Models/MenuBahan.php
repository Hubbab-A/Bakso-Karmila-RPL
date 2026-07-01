<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuBahan extends Model
{
    protected $table = 'menu_bahan';
    protected $fillable = [
        'menu_id', 'bahan_baku_id', 'jumlah', 'satuan_porsi', 'biaya'
    ];

    protected $casts = [
        'jumlah' => 'decimal:3',
        'biaya'  => 'decimal:2',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}