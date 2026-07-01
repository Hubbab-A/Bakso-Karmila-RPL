<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'nama', 'kategori', 'deskripsi', 'foto',
        'biaya_bahan_baku', 'biaya_tenaga_kerja', 'biaya_overhead',
        'hpp', 'margin_persen', 'harga_jual', 'tersedia',
    ];

    protected $casts = [
        'tersedia' => 'boolean',
        'biaya_bahan_baku' => 'decimal:2',
        'biaya_tenaga_kerja' => 'decimal:2',
        'biaya_overhead' => 'decimal:2',
        'hpp' => 'decimal:2',
        'margin_persen' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    public function bahanBaku(): BelongsToMany
    {
        return $this->belongsToMany(BahanBaku::class, 'menu_bahan')
            ->withPivot(['jumlah', 'biaya'])
            ->withTimestamps();
    }

    public function menuBahan(): HasMany
    {
        return $this->hasMany(MenuBahan::class);
    }

    // Hitung ulang HPP Full Costing
    public function hitungHpp(): void
    {
        $estimasiPorsi = (int) AppSetting::get('estimasi_porsi_bulan', 600);

        // Biaya bahan baku = sum biaya dari semua resep
        $totalBahan = $this->menuBahan()->sum('biaya');

        // Overhead per porsi
        $totalOverhead = OverheadSetting::where('aktif', true)->sum('biaya_per_bulan');
        $overheadPerPorsi = $estimasiPorsi > 0 ? $totalOverhead / $estimasiPorsi : 0;

        // TK per porsi
        $totalTK = TenagaKerjaSetting::where('aktif', true)->sum('gaji_per_bulan');
        $tkPerPorsi = $estimasiPorsi > 0 ? $totalTK / $estimasiPorsi : 0;

        $hpp = $totalBahan + $overheadPerPorsi + $tkPerPorsi;

        // Harga jual: HPP / (1 - margin%)
        $margin = $this->margin_persen / 100;
        $hargaJual = $margin < 1 ? $hpp / (1 - $margin) : $hpp;

        $this->update([
            'biaya_bahan_baku'   => $totalBahan,
            'biaya_overhead'     => $overheadPerPorsi,
            'biaya_tenaga_kerja' => $tkPerPorsi,
            'hpp'                => $hpp,
            'harga_jual'         => ceil($hargaJual / 500) * 500,
        ]);
    }

    public function getHargaJualFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }

    public function getHppFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->hpp, 0, ',', '.');
    }
}
