<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penjualan;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('barang');

        if ($request->filled('search')) {
            $query->where('pelanggan', 'like', '%' . $request->input('search') . '%');
        }

        $penjualans = $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();
        $barangs = Barang::all();

        return view('kasir.penjualan', compact('barangs', 'penjualans'));
    }

    public function store(Request $request)
    {
        if ($request->filled('penjualan_id')) {
            $penjualan = Penjualan::findOrFail($request->penjualan_id);
            return $this->update($request, $penjualan);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'pelanggan' => 'required|string|max:100',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barangs,id',
            'varian_nama' => 'nullable|array',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::findOrFail($barangId);
            $jumlah = $request->jumlah[$index];
            $varianNama = $request->varian_nama[$index] ?? null;
            $harga = $barang->harga;
            $varianHarga = 0;

            if ($varianNama && $barang->varian) {
                $varians = json_decode($barang->varian, true);
                foreach ($varians as &$v) {
                    if ($v['nama'] === $varianNama) {
                        if ($v['stok'] < $jumlah) {
                            return back()->withErrors(['jumlah' => 'Stok varian tidak cukup untuk ' . $barang->nama]);
                        }
                        $varianHarga = $v['harga'];
                        $v['stok'] -= $jumlah;
                        break;
                    }
                }
                $barang->varian = json_encode($varians);
            } else {
                if ($barang->stok < $jumlah) {
                    return back()->withErrors(['jumlah' => 'Stok tidak cukup untuk ' . $barang->nama]);
                }
                $barang->stok -= $jumlah;
            }

            $barang->save();

            $totalHarga = ($varianHarga ?: $harga) * $jumlah;

            Penjualan::create([
                'tanggal' => $request->tanggal,
                'pelanggan' => $request->pelanggan,
                'barang_id' => $barangId,
                'varian_nama' => $varianNama,
                'jumlah' => $jumlah,
                'total_harga' => $totalHarga,
            ]);
        }

        return redirect()->route('kasir.penjualan.index')->with('success', 'Data penjualan berhasil disimpan.');
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pelanggan' => 'required|string|max:100',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barangs,id',
            'varian_nama' => 'nullable|array',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        // Untuk kasus edit satu item, ambil index pertama
        $barangId = $request->barang_id[0];
        $varianNama = $request->varian_nama[0] ?? null;
        $jumlahBaru = $request->jumlah[0];

        $this->kembalikanStok($penjualan);

        $barang = Barang::findOrFail($barangId);
        $harga = $barang->harga;

        if ($varianNama && $barang->varian) {
            $varians = json_decode($barang->varian, true);
            foreach ($varians as &$v) {
                if ($v['nama'] === $varianNama) {
                    if ($v['stok'] < $jumlahBaru) {
                        return back()->withErrors(['jumlah' => 'Stok varian tidak mencukupi.']);
                    }
                    $harga = $v['harga'];
                    $v['stok'] -= $jumlahBaru;
                    break;
                }
            }
            $barang->varian = json_encode($varians);
        } else {
            if ($barang->stok < $jumlahBaru) {
                return back()->withErrors(['jumlah' => 'Stok tidak mencukupi.']);
            }
            $barang->stok -= $jumlahBaru;
        }

        $barang->save();

        $penjualan->update([
            'tanggal' => $request->tanggal,
            'pelanggan' => $request->pelanggan,
            'barang_id' => $barangId,
            'varian_nama' => $varianNama,
            'jumlah' => $jumlahBaru,
            'total_harga' => $harga * $jumlahBaru,
        ]);

        return redirect()->route('kasir.penjualan.index', ['tab' => 'data'])->with('success', 'Data penjualan berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan)
    {
        $this->kembalikanStok($penjualan);
        $penjualan->delete();

        return redirect()->route('kasir.penjualan.index')->with('success', 'Data penjualan berhasil dihapus.');
    }

    public function kurangiPenjualanKarenaRetur(Penjualan $penjualan, int $jumlahRetur)
    {
        if ($jumlahRetur >= $penjualan->jumlah) {
            $this->kembalikanStok($penjualan);
            $penjualan->delete();
        } else {
            $hargaSatuan = $penjualan->total_harga / $penjualan->jumlah;
            $penjualan->jumlah -= $jumlahRetur;
            $penjualan->total_harga -= $hargaSatuan * $jumlahRetur;
            $penjualan->save();
        }
    }

    private function kembalikanStok(Penjualan $penjualan)
    {
        $barang = Barang::find($penjualan->barang_id);
        if (!$barang) return;

        if ($penjualan->varian_nama && $barang->varian) {
            $varians = json_decode($barang->varian, true);
            foreach ($varians as &$v) {
                if ($v['nama'] === $penjualan->varian_nama) {
                    $v['stok'] += $penjualan->jumlah;
                    break;
                }
            }
            $barang->varian = json_encode($varians);
        } else {
            $barang->stok += $penjualan->jumlah;
        }

        $barang->save();
    }
}
