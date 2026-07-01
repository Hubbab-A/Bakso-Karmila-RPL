<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel biaya overhead tetap bulanan (Full Costing)
        Schema::create('overhead_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Listrik, Gas, Sewa, dll
            $table->decimal('biaya_per_bulan', 12, 2);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Tabel biaya TK bulanan
        Schema::create('tenaga_kerja_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama karyawan / jabatan
            $table->decimal('gaji_per_bulan', 12, 2);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Estimasi porsi per bulan (untuk alokasi overhead)
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('tenaga_kerja_settings');
        Schema::dropIfExists('overhead_settings');
    }
};
