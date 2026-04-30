<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id', 
        'jumlah', 
        'total_harga', 
        'tanggal_jual'
    ];

    // Relasi balik: Penjualan ini milik barang yang mana?
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}