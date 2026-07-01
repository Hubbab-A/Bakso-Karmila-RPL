<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota')->unique(); // format: TRX-20240115-001
            $table->foreignId('user_id')->constrained('users'); // kasir yang melayani
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('bayar', 12, 2)->default(0);
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->enum('status', ['selesai', 'batal'])->default('selesai');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();
        });

        Schema::create('transaksi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus');
            $table->string('nama_menu'); // snapshot nama saat transaksi
            $table->decimal('harga_jual', 12, 2); // snapshot harga saat transaksi
            $table->decimal('hpp', 12, 2);         // snapshot HPP untuk analisis laba
            $table->integer('qty');
            $table->decimal('subtotal', 12, 2);    // harga * qty
            $table->decimal('laba', 12, 2);        // (harga - hpp) * qty
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_details');
        Schema::dropIfExists('transaksis');
    }
};
