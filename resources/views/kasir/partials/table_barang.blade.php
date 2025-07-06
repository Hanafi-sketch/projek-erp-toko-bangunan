<table class="min-w-full table-auto">
    <thead class="bg-gray-100">
        <tr class="text-left">
            <th class="p-2">Gambar</th>
            <th class="p-2">Nama</th>
            <th class="p-2">Varian</th>
            <th class="p-2">Harga</th>
            <th class="p-2">Stok</th>
            <th class="p-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($barangs as $b)
            <tr class="border-b align-top">
                <td class="p-2">
                    @if($b->gambar)
                        <img src="{{ asset('storage/' . $b->gambar) }}" class="w-16 h-16 object-cover rounded">
                    @else
                        <span class="text-gray-400 italic">-</span>
                    @endif
                </td>
                <td class="p-2">{{ $b->nama }}</td>
                <td class="p-2">
                    @php $varians = json_decode($b->varian ?? '[]'); @endphp
                    @foreach($varians as $v)
                        <div class="text-sm">• {{ $v->nama ?? '-' }}</div>
                    @endforeach
                </td>
                <td class="p-2">
                    @if(!empty($varians))
                        @foreach($varians as $v)
                            <div class="text-sm">Rp {{ number_format($v->harga ?? 0, 0, ',', '.') }}</div>
                        @endforeach
                    @else
                        <div class="text-sm">Rp {{ number_format($b->harga ?? 0, 0, ',', '.') }}</div>
                    @endif
                </td>
                <td class="p-2">
                    @if(!empty($varians))
                        @foreach($varians as $v)
                            <div class="text-sm">{{ $v->stok ?? 0 }}</div>
                        @endforeach
                    @else
                        <div class="text-sm">{{ $b->stok }}</div>
                    @endif
                </td>
                <td class="p-2 space-x-2">
                    <button type="button" class="text-indigo-600 hover:underline" onclick='editBarang(@json($b))'>Edit</button>
                    <form action="{{ route('kasir.barang.destroy', $b) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach

        @if($barangs->isEmpty())
            <tr>
                <td colspan="6" class="text-center text-gray-500 p-4">Belum ada data.</td>
            </tr>
        @endif
    </tbody>
</table>

<!-- Navigasi Pagination -->
<div class="mt-4">
    {{ $barangs->appends(['tab' => 'data', 'search' => request('search')])->links() }}
</div>
