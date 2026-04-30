<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    // Tambahkan 'stok' di sini agar bisa di-update manual/lewat form
    protected $fillable = [
        'kode_barang', 
        'nama_barang', 
        'harga1', 
        'stok', 
        'kategori'
    ];

    // Relasi: Satu barang bisa punya banyak data penjualan
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }
}