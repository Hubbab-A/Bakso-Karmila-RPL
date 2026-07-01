<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\HppController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// ---- Auth ----
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---- Protected ----
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Kasir / POS
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');
    Route::get('/kasir/nota/{id}', [KasirController::class, 'nota'])->name('kasir.nota');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/detail', [LaporanController::class, 'detail'])->name('laporan.detail');
    Route::get('/laporan/nota/{id}', [LaporanController::class, 'nota'])->name('laporan.nota');

    // Menu (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('menu', MenuController::class);
        Route::patch('/menu/{menu}/toggle', [MenuController::class, 'toggleTersedia'])->name('menu.toggle');

        // HPP & Full Costing
        Route::get('/hpp', [HppController::class, 'index'])->name('hpp.index');
        Route::post('/hpp/recalculate', [HppController::class, 'recalculateAll'])->name('hpp.recalculate');
        Route::post('/hpp/estimasi', [HppController::class, 'updateEstimasi'])->name('hpp.estimasi');

        Route::post('/hpp/overhead', [HppController::class, 'storeOverhead'])->name('hpp.overhead.store');
        Route::put('/hpp/overhead/{overhead}', [HppController::class, 'updateOverhead'])->name('hpp.overhead.update');
        Route::delete('/hpp/overhead/{overhead}', [HppController::class, 'destroyOverhead'])->name('hpp.overhead.destroy');

        Route::post('/hpp/tk', [HppController::class, 'storeTK'])->name('hpp.tk.store');
        Route::put('/hpp/tk/{tk}', [HppController::class, 'updateTK'])->name('hpp.tk.update');
        Route::delete('/hpp/tk/{tk}', [HppController::class, 'destroyTK'])->name('hpp.tk.destroy');

        // Bahan Baku
        Route::resource('bahan-baku', BahanBakuController::class);
    });
});
