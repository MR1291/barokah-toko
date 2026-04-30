<?php
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BarangController::class, 'index']);
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

Route::get('/laporan', [BarangController::class, 'laporan']);
Route::post('/transaksi', [BarangController::class, 'transaksi']);
