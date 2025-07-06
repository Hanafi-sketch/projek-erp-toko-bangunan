<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    // Kolom yang boleh diisi secara mass assignment
    protected $fillable = [
        'nama',
        'varian',
        'harga',
        'stok',
        'gambar',
    ];

    // Secara otomatis cast kolom 'varian' jadi array (asumsikan JSON)
    protected $casts = [
        'varian' => 'array',
    ];
    // app/Models/Barang.php

    public function getStokVarian($namaVarian)
    {
        $varians = json_decode($this->varian, true);

        foreach ($varians as $v) {
            if ($v['nama'] === $namaVarian) {
                return $v['stok'];
            }
        }

        return 0; // tidak ketemu
    }

    public function kurangiStokVarian($namaVarian, $jumlah)
    {
        $varians = json_decode($this->varian, true);

        foreach ($varians as &$v) {
            if ($v['nama'] === $namaVarian) {
                if ($v['stok'] >= $jumlah) {
                    $v['stok'] -= $jumlah;
                }
            }
        }

        $this->varian = json_encode($varians);
        $this->save();
    }
    
    public function tambahStokVarian($namaVarian, $jumlah)
    {
        $varians = json_decode($this->varian, true) ?? [];

        foreach ($varians as &$v) {
            if ($v['nama'] === $namaVarian) {
                $v['stok'] += $jumlah;
                break;
            }
        }

        $this->varian = json_encode($varians);
        $this->save();
    }

}
