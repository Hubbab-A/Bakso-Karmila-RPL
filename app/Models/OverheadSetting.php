<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverheadSetting extends Model
{
    protected $table = 'overhead_settings';
    protected $fillable = ['nama', 'biaya_per_bulan', 'aktif'];

    protected $casts = [
        'aktif' => 'boolean',
        'biaya_per_bulan' => 'decimal:2',
    ];
}