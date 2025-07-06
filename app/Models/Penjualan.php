<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';

    protected $fillable = [
        'tanggal',
        'pelanggan',
        'barang_id',
        'varian_nama',
        'jumlah',
        'total_harga',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
