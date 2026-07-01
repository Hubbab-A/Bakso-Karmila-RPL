<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->decimal('jumlah_per_porsi', 10, 3)->default(0)->after('harga_per_satuan');
            $table->string('satuan_porsi')->default('gram')->after('jumlah_per_porsi');
            $table->decimal('biaya_per_porsi', 12, 2)->default(0)->after('satuan_porsi');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn(['jumlah_per_porsi', 'satuan_porsi', 'biaya_per_porsi']);
        });
    }
};