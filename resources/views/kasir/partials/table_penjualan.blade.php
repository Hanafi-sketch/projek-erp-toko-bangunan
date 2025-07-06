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
                <button 
                    type="button" 
                    onclick='editPenjualan(@json($p))'
                    class="text-indigo-600 hover:underline text-sm">Edit</button>
                <form action="{{ route('kasir.penjualan.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach

    @if($penjualans->isEmpty())
        <tr>
            <td colspan="7" class="text-center text-gray-500 p-4">Belum ada data penjualan.</td>
        </tr>
    @endif
</tbody>

<!-- Pagination -->
<div class="mt-4">
    {{ $penjualans->appends(['tab' => 'data'])->links() }}
</div>
