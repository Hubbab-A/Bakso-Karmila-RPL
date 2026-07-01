<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenagaKerjaSetting extends Model
{
    protected $table = 'tenaga_kerja_settings';
    protected $fillable = ['nama', 'gaji_per_bulan', 'aktif'];

    protected $casts = [
        'aktif' => 'boolean',
        'gaji_per_bulan' => 'decimal:2',
    ];
}