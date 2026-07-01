<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori')->default('makanan'); // makanan, minuman, dll
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();

            // Full Costing HPP
            $table->decimal('biaya_bahan_baku', 12, 2)->default(0);       // total biaya bahan per porsi
            $table->decimal('biaya_tenaga_kerja', 12, 2)->default(0);     // alokasi TK per porsi
            $table->decimal('biaya_overhead', 12, 2)->default(0);         // listrik, gas, sewa, dll per porsi
            $table->decimal('hpp', 12, 2)->default(0);                    // = bahan + TK + overhead
            $table->decimal('margin_persen', 5, 2)->default(30);          // % margin keuntungan
            $table->decimal('harga_jual', 12, 2)->default(0);             // = HPP / (1 - margin%)

            $table->boolean('tersedia')->default(true);
            $table->timestamps();
        });

        // Tabel pivot bahan per menu (rincian resep)
        Schema::create('menu_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->decimal('jumlah', 10, 3); // jumlah bahan yang dipakai per porsi
            $table->decimal('biaya', 12, 2);  // harga_per_satuan * jumlah (auto-hitung)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_bahan');
        Schema::dropIfExists('menus');
    }
};
