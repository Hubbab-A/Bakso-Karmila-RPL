<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bahan baku: hanya nama, satuan dasar, harga beli
        Schema::table('bahan_baku', function (Blueprint $table) {
            // Hapus kolom yang tidak perlu
            if (Schema::hasColumn('bahan_baku', 'stok')) {
                $table->dropColumn('stok');
            }
            if (Schema::hasColumn('bahan_baku', 'jumlah_per_porsi')) {
                $table->dropColumn('jumlah_per_porsi');
            }
            if (Schema::hasColumn('bahan_baku', 'satuan_porsi')) {
                $table->dropColumn('satuan_porsi');
            }
            if (Schema::hasColumn('bahan_baku', 'biaya_per_porsi')) {
                $table->dropColumn('biaya_per_porsi');
            }
        });

        // menu_bahan: tambah jumlah & satuan_porsi per resep
        Schema::table('menu_bahan', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_bahan', 'jumlah')) {
                $table->decimal('jumlah', 10, 3)->after('bahan_baku_id');
            }
            if (!Schema::hasColumn('menu_bahan', 'satuan_porsi')) {
                $table->string('satuan_porsi')->default('gram')->after('jumlah');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->decimal('stok', 10, 3)->default(0);
        });
        Schema::table('menu_bahan', function (Blueprint $table) {
            $table->dropColumn(['jumlah', 'satuan_porsi']);
        });
    }
};