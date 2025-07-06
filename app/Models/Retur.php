<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'pelanggan',
        'barang_id',
        'varian_nama',
        'jumlah',
        'total_harga',
        'alasan',
        'tipe_retur',
    ];
    // app/Models/Barang.php
    protected $casts = [
        'varian' => 'array',
    ];

    // Relasi ke model Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
