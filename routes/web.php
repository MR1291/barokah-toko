<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BAROKAH POS
|--------------------------------------------------------------------------
*/

// --- RUTE AUTENTIKASI (BISA DIAKSES TANPA LOGIN) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- RUTE UTAMA (WAJIB LOGIN) ---
Route::middleware('auth')->group(function () {
    
    // 1. HALAMAN UTAMA / INVENTARIS BARANG
    Route::get('/', [BarangController::class, 'index'])->name('barang.index');
    
    // 2. HALAMAN POINT OF SALE (POS) KASIR
    Route::get('/pos', [BarangController::class, 'pos'])->name('pos.index');
    
    // 3. HALAMAN LAPORAN TRANSAKSI
    Route::get('/laporan', [BarangController::class, 'laporan'])->name('laporan.index');
    
    // 4. FITUR MANIPULASI DATA BARANG (CRUD & IMPORT)
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy'); // <-- Fix Error barang.destroy
    Route::post('/barang/bulk-delete', [BarangController::class, 'bulkDelete'])->name('barang.bulkDelete');
    Route::post('/import-csv', [BarangController::class, 'importCsv'])->name('barang.import');
    
    // 5. FITUR TRANSAKSI KASIR POS
    Route::post('/transaksi', [BarangController::class, 'transaksi'])->name('barang.transaksi');
    
});