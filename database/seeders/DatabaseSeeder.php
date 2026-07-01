<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        DB::table('users')->insert([
            ['name' => 'Admin', 'email' => 'admin@bakso.com', 'password' => Hash::make('password'), 'role' => 'admin', 'created_at' => now()],
            ['name' => 'Kasir 1', 'email' => 'kasir@bakso.com', 'password' => Hash::make('password'), 'role' => 'kasir', 'created_at' => now()],
        ]);

        // App Settings
        DB::table('app_settings')->insert([
            ['key' => 'nama_warung', 'value' => 'Warung Bakso Pak Budi', 'label' => 'Nama Warung', 'created_at' => now()],
            ['key' => 'alamat', 'value' => 'Jl. Sudirman No. 10, Semarang', 'label' => 'Alamat', 'created_at' => now()],
            ['key' => 'telp', 'value' => '0812-3456-7890', 'label' => 'Telepon', 'created_at' => now()],
            ['key' => 'estimasi_porsi_bulan', 'value' => '600', 'label' => 'Estimasi Porsi / Bulan', 'created_at' => now()],
            ['key' => 'footer_nota', 'value' => 'Terima kasih telah berkunjung!', 'label' => 'Footer Nota', 'created_at' => now()],
        ]);

        // Overhead contoh
        DB::table('overhead_settings')->insert([
            ['nama' => 'Listrik', 'biaya_per_bulan' => 300000, 'aktif' => true, 'created_at' => now()],
            ['nama' => 'Gas LPG', 'biaya_per_bulan' => 150000, 'aktif' => true, 'created_at' => now()],
            ['nama' => 'Sewa Tempat', 'biaya_per_bulan' => 500000, 'aktif' => true, 'created_at' => now()],
            ['nama' => 'Air', 'biaya_per_bulan' => 75000, 'aktif' => true, 'created_at' => now()],
        ]);

        // Tenaga kerja contoh
        DB::table('tenaga_kerja_settings')->insert([
            ['nama' => 'Koki', 'gaji_per_bulan' => 1500000, 'aktif' => true, 'created_at' => now()],
            ['nama' => 'Pelayan', 'gaji_per_bulan' => 1000000, 'aktif' => true, 'created_at' => now()],
        ]);

        // Bahan baku contoh
        DB::table('bahan_baku')->insert([
            ['nama' => 'Daging Sapi', 'satuan' => 'kg', 'harga_per_satuan' => 130000, 'stok' => 5, 'created_at' => now()],
            ['nama' => 'Tepung Tapioka', 'satuan' => 'kg', 'harga_per_satuan' => 15000, 'stok' => 10, 'created_at' => now()],
            ['nama' => 'Mie Telur', 'satuan' => 'kg', 'harga_per_satuan' => 18000, 'stok' => 8, 'created_at' => now()],
            ['nama' => 'Tahu', 'satuan' => 'buah', 'harga_per_satuan' => 1000, 'stok' => 50, 'created_at' => now()],
            ['nama' => 'Tulang Sapi (Kuah)', 'satuan' => 'kg', 'harga_per_satuan' => 40000, 'stok' => 3, 'created_at' => now()],
            ['nama' => 'Ayam Kampung', 'satuan' => 'kg', 'harga_per_satuan' => 45000, 'stok' => 4, 'created_at' => now()],
            ['nama' => 'Bumbu (Bawang, dll)', 'satuan' => 'paket', 'harga_per_satuan' => 5000, 'stok' => 30, 'created_at' => now()],
            ['nama' => 'Mangkok Styrofoam', 'satuan' => 'buah', 'harga_per_satuan' => 500, 'stok' => 200, 'created_at' => now()],
        ]);
    }
}
