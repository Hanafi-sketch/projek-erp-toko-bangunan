@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">Manajemen Penjualan</h2>
@endsection

@section('content')
<div class="py-6 max-w-7xl mx-auto">
    <div class="mb-4 border-b border-gray-200">
        <nav class="flex space-x-4" id="tabs">
            <button onclick="showTab('form')" id="tab-form" class="tab-active">Input Penjualan</button>
            <button onclick="showTab('data')" id="tab-data" class="tab-inactive">Data Penjualan</button>
        </nav>
    </div>

    <!-- Form Penjualan -->
    <div id="tab-content-form" class="tab-content">
        <form action="{{ route('kasir.penjualan.store') }}" method="POST" class="bg-white p-6 rounded shadow" id="penjualan-form">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="penjualan_id" id="penjualanId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="input-field" required>
                </div>
                <div>
                    <label>Nama Pelanggan</label>
                    <input type="text" name="pelanggan" class="input-field" required>
                </div>
            </div>

            <div id="barang-wrapper" class="mt-4 space-y-4">
            </div>

            <div class="mt-4">
                <button type="button" onclick="tambahBarang()" class="btn-secondary">+ Tambah Barang</button>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" onclick="resetForm()" class="btn-gray">Batal Edit</button>
                <button type="submit" class="btn-primary">Simpan Penjualan</button>
            </div>
        </form>
    </div>

    <div id="tab-content-data" class="tab-content hidden">
        <div class="bg-white shadow-md rounded p-6 overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-left">Pelanggan</th>
                        <th class="p-2 text-left">Barang</th>
                        <th class="p-2 text-left">Varian</th>
                        <th class="p-2 text-left">Jumlah</th>
                        <th class="p-2 text-left">Total Harga</th>
                        <th class="p-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualans as $p)
                        <tr class="border-b">
                            <td class="p-2">{{ $p->tanggal }}</td>
                            <td class="p-2">{{ $p->pelanggan }}</td>
                            <td class="p-2">{{ $p->barang->nama }}</td>
                            <td class="p-2">{{ $p->varian_nama ?? '-' }}</td>
                            <td class="p-2">{{ $p->jumlah }}</td>
                            <td class="p-2">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                            <td class="p-2 space-x-2">
                                <button type="button" onclick='editPenjualan(@json($p))' class="text-indigo-600 hover:underline text-sm">Edit</button>
                                <form action="{{ route('kasir.penjualan.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $penjualans->appends(['tab' => 'data'])->links() }}
            </div>
        </div>
    </div>
</div>

<template id="barang-template">
    <div class="barang-item border p-4 rounded bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label>Barang</label>
                <select name="barang_id[]" class="input-field" onchange="loadVarian(this)" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}" data-varian='@json(json_decode($barang->varian ?? "[]"))'>
                            {{ $barang->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Varian (jika ada)</label>
                <select name="varian_nama[]" class="input-field">
                    <option value="">-- Tidak Ada Varian --</option>
                </select>
            </div>
            <div>
                <label>Jumlah</label>
                <input type="number" name="jumlah[]" class="input-field" required>
            </div>
        </div>
    </div>
</template>

<script>
function showTab(tab) {
    document.getElementById('tab-content-form').classList.add('hidden');
    document.getElementById('tab-content-data').classList.add('hidden');
    document.getElementById('tab-form').classList.remove('tab-active');
    document.getElementById('tab-data').classList.remove('tab-active');

    document.getElementById('tab-content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('tab-active');
}

function tambahBarang() {
    const wrapper = document.getElementById('barang-wrapper');
    const template = document.getElementById('barang-template');
    wrapper.appendChild(template.content.cloneNode(true));
}

function loadVarian(select) {
    const varianData = JSON.parse(select.selectedOptions[0].getAttribute('data-varian') || '[]');
    const varianSelect = select.closest('.barang-item').querySelector('select[name="varian_nama[]"]');
    varianSelect.innerHTML = '<option value="">-- Tidak Ada Varian --</option>';
    varianData.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.nama;
        opt.text = `${v.nama} (Rp ${parseInt(v.harga).toLocaleString()}, stok: ${v.stok})`;
        varianSelect.appendChild(opt);
    });
}

function editPenjualan(data) {
    showTab('form');
    const form = document.getElementById('penjualan-form');
	form.action = "{{ url('kasir/penjualan') }}/" + data.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('penjualanId').value = data.id;
    form.querySelector('input[name="tanggal"]').value = data.tanggal;
    form.querySelector('input[name="pelanggan"]').value = data.pelanggan;

    const wrapper = document.getElementById('barang-wrapper');
    wrapper.innerHTML = '';
    tambahBarang();
    const item = wrapper.querySelector('.barang-item');

    const barangSelect = item.querySelector('select[name="barang_id[]"]');
    barangSelect.value = data.barang_id;
    loadVarian(barangSelect);

    setTimeout(() => {
        item.querySelector('select[name="varian_nama[]"]').value = data.varian_nama ?? '';
    }, 200);

    item.querySelector('input[name="jumlah[]"]').value = data.jumlah;
}

function resetForm() {
    const form = document.getElementById('penjualan-form');
    form.action = "{{ route('kasir.penjualan.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('penjualanId').value = '';
    form.reset();
    const wrapper = document.getElementById('barang-wrapper');
    wrapper.innerHTML = '';
    tambahBarang();
}

document.addEventListener('DOMContentLoaded', () => {
    const tab = new URLSearchParams(window.location.search).get('tab');
    showTab(tab === 'data' ? 'data' : 'form');
    tambahBarang(); // Awal form 1 barang
});
</script>
@endsection
