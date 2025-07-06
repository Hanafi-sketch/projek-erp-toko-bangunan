@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- 🔔 Alert Tanggal -->
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-2 rounded">
        Menampilkan data untuk tanggal <strong>{{ $tanggal->translatedFormat('d F Y') }}</strong>
    </div>

    <!-- 🔍 Filter Tanggal -->
    <form method="GET" action="{{ route('dashboard') }}" class="mb-4 mt-4 flex items-center gap-4">
        <div>
            <label for="tanggal" class="text-gray-600 text-sm">Pilih Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal"
                   value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}"
                   class="border rounded px-2 py-1">
        </div>
        <div class="pt-5">
            <button type="submit"
                class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold px-5 py-2 rounded-lg shadow-lg">
                🔍 Tampilkan
            </button>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-4">
            <h4 class="text-gray-600 font-semibold">Pendapatan</h4>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <h4 class="text-gray-600 font-semibold">Jumlah Barang Terjual</h4>
            <p class="text-2xl font-bold text-blue-600">{{ $jumlahTerjual }} pcs</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <h4 class="text-gray-600 font-semibold">Jumlah Retur</h4>
            <p class="text-2xl font-bold text-red-600">{{ $jumlahReturHariIni }} item</p>
        </div>
    </div>

    <!-- Bar Chart - Penjualan Harian -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-lg p-4">
            <h4 class="text-gray-600 font-semibold mb-2">Grafik Penjualan (7 Hari)</h4>
            <canvas id="barChart" class="w-full h-64"></canvas>
        </div>

        <!-- Line Chart - Pendapatan Bulanan -->
        <div class="bg-white shadow rounded-lg p-4">
            <h4 class="text-gray-600 font-semibold mb-2">Pendapatan Bulanan</h4>
            <canvas id="lineChart" class="w-full h-64"></canvas>
        </div>

        <!-- Barang Terlaris -->
        <div class="md:col-span-2 bg-white shadow rounded-lg p-4">
            <h4 class="text-gray-600 font-semibold mb-2">5 Barang Paling Laku</h4>
            <canvas id="barangLarisChart" class="w-full h-64"></canvas>
        </div>

        <!-- Pie Chart Retur -->
        <div class="md:col-span-2 flex justify-center">
            <div class="bg-white shadow rounded-lg p-4 w-full max-w-md">
                <h4 class="text-gray-600 font-semibold mb-2 text-center">Distribusi Barang Retur</h4>
                <canvas id="returChart" class="w-full h-64"></canvas>
            </div>
        </div>
    </div>

    <!-- Barang Stok Menipis -->
    <div class="bg-white shadow rounded-lg p-4">
        <h4 class="text-gray-600 font-semibold mb-4">Barang dengan Stok Menipis</h4>
        <table class="min-w-full table-auto border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border text-left">Nama Barang</th>
                    <th class="p-2 border text-left">Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stokMenipis as $barang)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 border">{{ $barang->nama }}</td>
                        <td class="p-2 border">
                            @if(!is_null($barang->stok))
                                {{ $barang->stok }}
                            @else
                                @php
                                    $varians = json_decode($barang->varian, true);
                                    $stokTerkecil = is_array($varians) ? collect($varians)->min('stok') : '-';
                                @endphp
                                {{ $stokTerkecil ?? '-' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center p-4 text-gray-500">Tidak ada barang dengan stok menipis.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($penjualanHarianLabels) !!},
            datasets: [{
                label: 'Penjualan (pcs)',
                data: {!! json_encode($penjualanHarianData) !!},
                backgroundColor: '#3b82f6'
            }]
        }
    });

    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($pendapatanBulananLabels) !!},
            datasets: [{
                label: 'Pendapatan',
                data: {!! json_encode($pendapatanBulananData) !!},
                borderColor: '#10b981',
                fill: false,
                tension: 0.3
            }]
        }
    });

    new Chart(document.getElementById('barangLarisChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($barangLabels) !!},
            datasets: [{
                label: 'Jumlah Terjual',
                data: {!! json_encode($barangData) !!},
                backgroundColor: '#f97316'
            }]
        }
    });

    new Chart(document.getElementById('returChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($returBarangLabels) !!},
            datasets: [{
                data: {!! json_encode($returBarangData) !!},
                backgroundColor: [
                    '#f87171', '#fb923c', '#facc15', '#4ade80',
                    '#60a5fa', '#c084fc', '#f472b6'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection
