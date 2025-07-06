<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\UserController;

// ================= HALAMAN UTAMA =================
Route::get('/', fn() => redirect()->route('dashboard'));

// ================= DASHBOARD =================

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ================= SEMUA ROUTE YANG MEMERLUKAN LOGIN =================
Route::middleware(['auth'])->group(function () {

    // ================= RUTE KASIR =================
    Route::prefix('kasir')->name('kasir.')->group(function () {

        // ===== BARANG =====
        Route::get('/barang', [BarangController::class, 'index'])->name('barang');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

        // ===== PENJUALAN =====
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
        Route::put('/penjualan/{penjualan}', [PenjualanController::class, 'update'])->name('penjualan.update');
        Route::delete('/penjualan/{penjualan}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');

        // ===== RETUR =====
        Route::get('/retur', [ReturController::class, 'index'])->name('retur');
        Route::post('/retur', [ReturController::class, 'store'])->name('retur.store');

        // ===== USERS =====
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

// ================= AUTH (LOGIN, REGISTER, LOGOUT) =================
require __DIR__.'/auth.php';
