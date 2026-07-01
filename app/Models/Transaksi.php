<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaksi extends Model
{
    protected $fillable = [
        'no_nota', 'user_id', 'subtotal', 'diskon', 'total',
        'bayar', 'kembalian', 'metode_bayar', 'status', 'catatan', 'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon'   => 'decimal:2',
        'total'    => 'decimal:2',
        'bayar'    => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    // Generate nomor nota otomatis
    public static function generateNoNota(): string
    {
        $prefix = 'TRX-' . now()->format('Ymd') . '-';
        $last = self::where('no_nota', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('no_nota');

        $lastNum = $last ? (int) substr($last, -3) : 0;
        return $prefix . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
    }

    // Total laba dari semua detail
    public function getTotalLabaAttribute(): float
    {
        return $this->details->sum('laba');
    }
}
