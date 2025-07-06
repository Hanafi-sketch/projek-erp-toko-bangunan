@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">Manajemen Barang</h2>
@endsection

@section('content')
<div class="py-6 max-w-7xl mx-auto">

    {{-- ✅ Flash Message --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- ✅ Tabs -->
    <div class="mb-4 border-b border-gray-200">
        <nav class="flex space-x-4" id="tabs">
            <button onclick="showTab('form')" id="tab-form" class="tab-active">Tambah Barang</button>
            <button onclick="showTab('data')" id="tab-data" class="tab-inactive">Data Barang</button>
        </nav>
    </div>

    <!-- ✅ Tab: Form Tambah Barang -->
    <div id="tab-content-form" class="tab-content">
        <form id="formBarang" action="{{ route('kasir.barang.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="id" id="barangId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label>Nama Barang</label>
                    <input type="text" name="nama" class="input-field" required>
                </div>
                <div id="form-harga-stok-utama">
                    <div>
                        <label>Harga</label>
                        <input type="number" name="harga" id="hargaInput" class="input-field">
                    </div>
                    <div>
                        <label>Stok</label>
                        <input type="number" name="stok" class="input-field">
                    </div>
                </div>
                <div>
                    <label>Gambar</label>
                    <input type="file" name="gambar" class="input-field">
                </div>
            </div>

            <div class="mt-4">
                <label class="block mb-2">Varian, Harga, dan Stok</label>
                <div id="varian-container"></div>
                <button type="button" onclick="tambahVarian()" class="mt-2 text-sm text-blue-600 hover:underline">+ Tambah Varian</button>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn-primary" id="submitButton">Simpan</button>
                <button type="button" onclick="resetForm()" class="ml-2 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Batal</button>
            </div>
        </form>
    </div>

    <!-- ✅ Tab: Data Barang -->
    <div id="tab-content-data" class="tab-content hidden">
        <div class="bg-white shadow-md rounded p-6 overflow-x-auto">
            <h3 class="text-lg font-semibold mb-4">Daftar Barang</h3>

            <form id="searchForm" class="mb-4">
                <input type="text" name="search" id="searchInput" placeholder="Cari nama barang..." value="{{ request('search') }}"
                    class="border border-gray-300 px-3 py-1 rounded">
                <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Cari</button>
                <button type="button" id="resetSearch" class="ml-2 text-sm text-gray-600 underline">Reset</button>
            </form>

            <div id="tableBarang">
                @include('kasir.partials.table_barang')
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function showTab(tab) {
        document.getElementById('tab-content-form').classList.add('hidden');
        document.getElementById('tab-content-data').classList.add('hidden');
        document.getElementById('tab-form').classList.remove('tab-active');
        document.getElementById('tab-data').classList.remove('tab-active');

        document.getElementById('tab-content-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).classList.add('tab-active');

        if (tab === 'form') resetForm();
    }

    function tambahVarian() {
        const container = document.getElementById('varian-container');
        document.getElementById('form-harga-stok-utama').style.display = 'none';

        const div = document.createElement('div');
        div.className = "flex gap-2 mb-2 items-center";
        div.innerHTML = `
            <input type="text" name="varian[]" class="input-field w-full" placeholder="Varian" required>
            <input type="number" name="harga_varian[]" class="input-field w-full" placeholder="Harga" required>
            <input type="number" name="stok_varian[]" class="input-field w-full" placeholder="Stok" required>
            <button type="button" onclick="this.parentElement.remove(); cekVarianKosong()" class="text-red-600 text-sm hover:underline">Hapus</button>
        `;
        container.appendChild(div);
    }

    function cekVarianKosong() {
        const container = document.getElementById('varian-container');
        if (container.children.length === 0) {
            document.getElementById('form-harga-stok-utama').style.display = 'block';
        }
    }

    function resetForm() {
        const form = document.getElementById('formBarang');
        form.reset();
        form.action = "{{ route('kasir.barang.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('submitButton').innerText = 'Simpan';
        document.getElementById('barangId').value = '';
        document.getElementById('varian-container').innerHTML = '';
        document.getElementById('form-harga-stok-utama').style.display = 'block';
    }

    function editBarang(barang) {
        showTab('form');

        const form = document.getElementById('formBarang');
    	form.action = "{{ url('kasir/barang') }}/" + barang.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('submitButton').innerText = 'Update';
        document.getElementById('barangId').value = barang.id;

        document.querySelector('input[name="nama"]').value = barang.nama;
        document.getElementById('hargaInput').value = barang.harga ?? '';
        document.querySelector('input[name="stok"]').value = barang.stok ?? '';
        document.querySelector('input[name="gambar"]').value = null;

        document.getElementById('varian-container').innerHTML = '';
        if (barang.varian) {
            const varianList = JSON.parse(barang.varian);
            if (Array.isArray(varianList) && varianList.length > 0) {
                document.getElementById('form-harga-stok-utama').style.display = 'none';
                varianList.forEach(v => {
                    const div = document.createElement('div');
                    div.className = "flex gap-2 mb-2 items-center";
                    div.innerHTML = `
                        <input type="text" name="varian[]" class="input-field w-full" placeholder="Varian" value="${v.nama ?? ''}" required>
                        <input type="number" name="harga_varian[]" class="input-field w-full" placeholder="Harga" value="${v.harga ?? ''}" required>
                        <input type="number" name="stok_varian[]" class="input-field w-full" placeholder="Stok" value="${v.stok ?? ''}" required>
                        <button type="button" onclick="this.parentElement.remove(); cekVarianKosong()" class="text-red-600 text-sm hover:underline">Hapus</button>
                    `;
                    document.getElementById('varian-container').appendChild(div);
                });
            } else {
                document.getElementById('form-harga-stok-utama').style.display = 'block';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'data') {
            showTab('data');
        } else {
            showTab('form');
        }
    });

    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        const query = $('#searchInput').val();

        $.ajax({
            url: "{{ route('kasir.barang') }}",
            type: "GET",
            data: { search: query },
            success: function (data) {
                $('#tableBarang').html(data);
            }
        });
    });

    $('#resetSearch').on('click', function () {
        $('#searchInput').val('');
        $('#searchForm').trigger('submit');
    });
</script>
@endsection
