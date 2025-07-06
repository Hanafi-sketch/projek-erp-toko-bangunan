<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Retur;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari request, jika tidak ada pakai hari ini
        $tanggal = $request->input('tanggal') 
            ? Carbon::parse($request->input('tanggal')) 
            : Carbon::today();

        // Total pendapatan
        $pendapatanHariIni = Penjualan::whereDate('tanggal', $tanggal)->sum('total_harga');

        // Jumlah barang terjual
        $jumlahTerjual = Penjualan::whereDate('tanggal', $tanggal)->sum('jumlah');

        // Jumlah retur
        $jumlahReturHariIni = Retur::whereDate('tanggal', $tanggal)->count();

        // Penjualan harian (7 hari terakhir termasuk tanggal ini)
        $penjualanPerHari = Penjualan::selectRaw('DATE(tanggal) as tanggal, SUM(jumlah) as total')
            ->whereBetween('tanggal', [$tanggal->copy()->subDays(6), $tanggal])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $penjualanHarianLabels = $penjualanPerHari->pluck('tanggal')->map(fn($date) =>
            Carbon::parse($date)->format('d M')
        );
        $penjualanHarianData = $penjualanPerHari->pluck('total');

        // Pendapatan bulanan
        $pendapatanBulanan = Penjualan::selectRaw('MONTH(tanggal) as bulan, SUM(total_harga) as total')
            ->whereYear('tanggal', $tanggal->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $pendapatanBulananLabels = $pendapatanBulanan->pluck('bulan')->map(fn($bulan) =>
            Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM')
        );
        $pendapatanBulananData = $pendapatanBulanan->pluck('total');

        // Barang terlaris (pada tanggal tersebut)
        $barangTerlaris = DB::table('penjualans')
            ->join('barangs', 'penjualans.barang_id', '=', 'barangs.id')
            ->select('barangs.nama', DB::raw('SUM(penjualans.jumlah) as total_terjual'))
            ->whereDate('penjualans.tanggal', $tanggal)
            ->groupBy('barangs.nama')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $barangLabels = $barangTerlaris->pluck('nama');
        $barangData = $barangTerlaris->pluck('total_terjual');

        // Barang dengan stok menipis
        $semuaBarang = Barang::all();

        $stokMenipis = $semuaBarang->filter(function ($barang) {
            // Jika pakai kolom stok biasa
            if (!is_null($barang->stok) && $barang->stok <= 5) {
                return true;
            }

            // Jika pakai kolom varian (format JSON)
            if ($barang->varian) {
                $varians = json_decode($barang->varian, true);
                if (is_array($varians)) {
                    foreach ($varians as $v) {
                        if (isset($v['stok']) && $v['stok'] <= 5) {
                            return true;
                        }
                    }
                }
            }

            return false;
        });

        // Distribusi retur (pada tanggal tersebut)
        $returData = Retur::with('barang')
            ->select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->whereDate('tanggal', $tanggal)
            ->groupBy('barang_id')
            ->get();

        $returBarangLabels = $returData->pluck('barang.nama');
        $returBarangData = $returData->pluck('total');

        return view('kasir.dashboard', compact(
            'tanggal',
            'pendapatanHariIni',
            'jumlahTerjual',
            'jumlahReturHariIni',
            'penjualanHarianLabels',
            'penjualanHarianData',
            'pendapatanBulananLabels',
            'pendapatanBulananData',
            'barangLabels',
            'barangData',
            'stokMenipis',
            'returBarangLabels',
            'returBarangData'
        ));
    }
}
