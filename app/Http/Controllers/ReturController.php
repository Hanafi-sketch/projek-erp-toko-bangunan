<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Penjualan;
use App\Models\Barang;
use Illuminate\Http\Request;

class ReturController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'input');
        $penjualans = Penjualan::with('barang')->latest()->get();
        $returs = Retur::with('barang')->orderBy('created_at', 'desc')->paginate(5);

        return view('kasir.retur', compact('tab', 'penjualans', 'returs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id'   => 'required|exists:penjualans,id',
            'barang_id'      => 'required|exists:barangs,id',
            'pelanggan'      => 'required|string|max:100',
            'varian_nama'    => 'nullable|string|max:100',
            'jumlah'         => 'required|integer|min:1',
            'alasan'         => 'required|string|max:500',
            'alasan_lainnya' => 'nullable|string|max:500',
            'tipe_retur'     => 'required|in:Tukar Barang,Pengembalian Uang',
        ]);

        $penjualan = Penjualan::with('barang')->findOrFail($request->penjualan_id);
        $barang = $penjualan->barang;
        $jumlahRetur = $request->jumlah;
        $varianNama = $request->varian_nama;
        $totalPengembalian = 0;

        // Validasi jumlah retur tidak boleh melebihi jumlah pembelian
        if ($jumlahRetur > $penjualan->jumlah) {
            return back()->withErrors(['jumlah' => 'Jumlah retur melebihi jumlah yang dibeli.']);
        }

        $isVarian = $barang->varian && $varianNama;

        // === TUKAR BARANG ===
        if ($request->tipe_retur === 'Tukar Barang') {
            if ($isVarian) {
                $stokVarian = $barang->getStokVarian($varianNama);
                if ($stokVarian < $jumlahRetur) {
                    return back()->withErrors(['jumlah' => 'Stok varian tidak mencukupi untuk menukar barang.']);
                }
                $barang->kurangiStokVarian($varianNama, $jumlahRetur);
            } else {
                if ($barang->stok < $jumlahRetur) {
                    return back()->withErrors(['jumlah' => 'Stok tidak mencukupi untuk menukar barang.']);
                }
                $barang->stok -= $jumlahRetur;
                $barang->save();
            }
        }

        // === PENGEMBALIAN UANG ===
        if ($request->tipe_retur === 'Pengembalian Uang') {
            $hargaSatuan = $penjualan->total_harga / $penjualan->jumlah;
            $totalPengembalian = $hargaSatuan * $jumlahRetur;

            if ($isVarian) {
                $barang->tambahStokVarian($varianNama, $jumlahRetur);
            } else {
                $barang->stok += $jumlahRetur;
                $barang->save();
            }

            if ($jumlahRetur < $penjualan->jumlah) {
                $penjualan->jumlah -= $jumlahRetur;
                $penjualan->total_harga = $penjualan->jumlah * $hargaSatuan;
                $penjualan->save();
            } else {
                $penjualan->delete();
            }
        }

        // Alasan akhir
        $finalAlasan = ($request->alasan === 'lainnya' && $request->filled('alasan_lainnya'))
            ? $request->alasan_lainnya
            : $request->alasan;

        Retur::create([
            'tanggal'     => $request->tanggal,
            'pelanggan'   => $request->pelanggan,
            'barang_id'   => $request->barang_id,
            'varian_nama' => $varianNama,
            'jumlah'      => $jumlahRetur,
            'total_harga' => $totalPengembalian,
            'alasan'      => $finalAlasan,
            'tipe_retur'  => $request->tipe_retur,
        ]);

        return redirect()->route('kasir.retur', ['tab' => 'data'])->with('success', 'Retur berhasil disimpan.');
    }
}
