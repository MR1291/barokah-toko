<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

// --- RUTE AUTENTIKASI ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE UTAMA (WAJIB LOGIN) ---
Route::middleware('auth')->group(function () {
    
    Route::get('/', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/pos', [BarangController::class, 'pos'])->name('barang.pos');
    Route::get('/laporan', [BarangController::class, 'laporan'])->name('barang.laporan');
    
    // Fitur Import CSV & Transaksi POS
    Route::post('/import-csv', [BarangController::class, 'importCsv'])->name('barang.import');
    Route::post('/transaksi', [BarangController::class, 'transaksi'])->name('barang.transaksi');
    Route::post('/barang/bulk-delete', [BarangController::class, 'bulkDelete'])->name('barang.bulkDelete');
    
});