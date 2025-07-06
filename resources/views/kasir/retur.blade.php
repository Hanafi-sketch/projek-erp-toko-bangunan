@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Halaman Retur Barang Penjualan</h2>
@endsection

@section('content')
<div class="bg-white p-6 rounded shadow-md">

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded text-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tab Navigasi --}}
    <div class="flex border-b mb-6">
        <a href="{{ route('kasir.retur', ['tab' => 'input']) }}"
           class="px-4 py-2 -mb-px border-b-2 {{ request('tab') !== 'data' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500' }}">
            Input Retur
        </a>
        <a href="{{ route('kasir.retur', ['tab' => 'data']) }}"
           class="ml-4 px-4 py-2 -mb-px border-b-2 {{ request('tab') === 'data' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500' }}">
            Data Retur
        </a>
    </div>

    {{-- FORM INPUT RETUR --}}
    @if(request('tab') !== 'data')
    <form action="{{ route('kasir.retur.store') }}" method="POST" class="space-y-4">
        @csrf

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Tanggal Retur --}}
            <div>
                <label class="text-sm font-medium">Tanggal Retur</label>
                <input type="date" name="tanggal" class="w-full border rounded px-3 py-2 text-sm" value="{{ date('Y-m-d') }}" required>
            </div>

            {{-- Tipe Retur --}}
            <div>
                <label class="text-sm font-medium">Tipe Retur</label>
                <select name="tipe_retur" class="w-full border rounded px-3 py-2 text-sm" required>
                    <option value="">Pilih</option>
                    <option value="Tukar Barang">Tukar Barang</option>
                    <option value="Pengembalian Uang">Pengembalian Uang</option>
                </select>
            </div>

            {{-- Pilih Penjualan --}}
            <div class="md:col-span-2">
                <label class="text-sm font-medium">Pilih Data Penjualan</label>
                <select name="penjualan_id" id="penjualanSelect"
                    class="w-full border rounded px-3 py-2 text-sm"
                    onchange="isiDataPenjualan()" required>
                    <option value="">-- Pilih --</option>
                    @foreach($penjualans as $p)
                        <option value="{{ $p->id }}"
                            data-barang-id="{{ $p->barang->id }}"
                            data-barang="{{ $p->barang->nama }}"
                            data-varian="{{ $p->varian_nama }}"
                            data-jumlah="{{ $p->jumlah }}"
                            data-pelanggan="{{ $p->pelanggan }}">
                            {{ $p->tanggal }} - {{ $p->pelanggan }} - {{ $p->barang->nama }}{{ $p->varian_nama ? ' ('.$p->varian_nama.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Alasan Retur --}}
            <div class="md:col-span-2">
                <label class="text-sm font-medium">Alasan Retur</label>
                <select id="alasan_select" name="alasan"
                    class="w-full border rounded px-3 py-2 text-sm" required>
                    <option value="">-- Pilih Alasan --</option>
                    <option value="Kemasan Rusak">Kemasan Rusak</option>
                    <option value="Warna Tidak Sesuai">Warna Tidak Sesuai</option>
                    <option value="Ukuran Salah">Ukuran Salah</option>
                    <option value="Barang Cacat">Barang Cacat</option>
                    <option value="Salah Kirim">Salah Kirim</option>
                    <option value="lainnya">Lainnya (Tulis Manual)</option>
                </select>

                <div id="alasan_lainnya_div" class="mt-2 hidden">
                    <input type="text" name="alasan_lainnya"
                        class="w-full border rounded px-3 py-2 text-sm"
                        placeholder="Tulis alasan lainnya...">
                </div>
            </div>
        </div>

        {{-- Hidden --}}
        <input type="hidden" name="barang_id" id="barang_id">
        <input type="hidden" name="pelanggan" id="pelanggan">
        <input type="hidden" name="varian_nama" id="varian_nama">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label>Barang</label>
                <input type="text" id="barangRetur" class="w-full bg-gray-100 rounded px-3 py-2" readonly>
            </div>
            <div>
                <label>Varian</label>
                <input type="text" id="varianRetur" class="w-full bg-gray-100 rounded px-3 py-2" readonly>
            </div>
            <div>
                <label>Jumlah Retur</label>
                <input type="number" name="jumlah" id="jumlahRetur" class="w-full border rounded px-3 py-2" required min="1">
            </div>
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-black font-semibold text-sm px-6 py-2 rounded shadow">
                Simpan Retur
            </button>
        </div>
    </form>
    @endif

    {{-- DATA RETUR --}}
    @if(request('tab') === 'data')
    <div class="bg-white shadow-md rounded p-6 overflow-x-auto mt-6">
        <h3 class="font-semibold mb-4 text-center">Riwayat Retur</h3>
        <table class="min-w-full table-auto border">
            <thead class="bg-gray-100 text-center">
                <tr>
                    <th class="p-2 border">Tanggal</th>
                    <th class="p-2 border">Pelanggan</th>
                    <th class="p-2 border">Barang</th>
                    <th class="p-2 border">Varian</th>
                    <th class="p-2 border">Jumlah</th>
                    <th class="p-2 border">Tipe Retur</th>
                    <th class="p-2 border">Alasan</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @forelse($returs as $r)
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border">{{ $r->tanggal }}</td>
                    <td class="p-2 border">{{ $r->pelanggan }}</td>
                    <td class="p-2 border">{{ $r->barang->nama ?? '-' }}</td>
                    <td class="p-2 border">{{ $r->varian_nama ?? '-' }}</td>
                    <td class="p-2 border">{{ $r->jumlah }}</td>
                    <td class="p-2 border">{{ $r->tipe_retur }}</td>
                    <td class="p-2 border">{{ $r->alasan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-gray-500 py-4">Belum ada data retur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $returs->appends(['tab' => 'data'])->links() }}
        </div>
    </div>
    @endif
</div>

{{-- JS --}}
<script>
function isiDataPenjualan() {
    const select = document.getElementById('penjualanSelect');
    if (!select) return;

    const selected = select.options[select.selectedIndex];
    if (!selected) return;

    document.getElementById('barang_id').value = selected.dataset.barangId || '';
    document.getElementById('barangRetur').value = selected.dataset.barang || '';
    document.getElementById('varianRetur').value = selected.dataset.varian || '';
    document.getElementById('jumlahRetur').value = selected.dataset.jumlah || '';
    document.getElementById('pelanggan').value = selected.dataset.pelanggan || '';
    document.getElementById('varian_nama').value = selected.dataset.varian || '';
}

document.addEventListener('DOMContentLoaded', function () {
    const alasanSelect = document.getElementById('alasan_select');
    const alasanLainnyaDiv = document.getElementById('alasan_lainnya_div');

    if (alasanSelect && alasanLainnyaDiv) {
        alasanSelect.addEventListener('change', function () {
            alasanLainnyaDiv.classList.toggle('hidden', this.value !== 'lainnya');
        });
    }
});
</script>
@endsection
