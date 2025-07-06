<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'supplier',
        'barang_id',
        'gambar',
        'varian_nama',
        'jumlah',
        'total',
    ];

    /**
     * Relasi ke tabel barangs
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
