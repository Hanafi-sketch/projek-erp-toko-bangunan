<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $barang = new Barang();
        $editMode = false;

        if ($request->has('edit')) {
            $barang = Barang::findOrFail($request->edit);
            $editMode = true;
        }

        $query = Barang::query();

        // 🔍 Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('harga', 'like', '%' . $search . '%')
                    ->orWhere('varian', 'like', '%' . $search . '%');
            });
        }

        $barangs = $query->latest()->paginate(5)->withQueryString();

        // 🔄 Jika AJAX, kirim partial view saja
        if ($request->ajax()) {
            return view('kasir.partials.table_barang', compact('barangs'))->render();
        }

        return view('kasir.barang', compact('barangs', 'barang', 'editMode'));
    }

    public function create()
    {
        return view('kasir.form', ['barang' => new Barang()]);
    }

    public function store(Request $request)
    {
        $punyaVarian = $request->has('varian') && collect($request->varian)->filter()->isNotEmpty();

        $request->validate([
            'nama' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'harga' => $punyaVarian ? 'nullable' : 'required|integer|min:0',
            'stok' => $punyaVarian ? 'nullable' : 'required|integer|min:0',
            'varian.*' => $punyaVarian ? 'required|string' : 'nullable',
            'harga_varian.*' => $punyaVarian ? 'required|integer|min:0' : 'nullable',
            'stok_varian.*' => $punyaVarian ? 'required|integer|min:0' : 'nullable',
        ]);

        $barangData = [
            'nama' => $request->nama,
        ];

        if ($request->hasFile('gambar')) {
            $barangData['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        if ($punyaVarian) {
            $varians = [];
            foreach ($request->varian as $i => $v) {
                if (isset($request->harga_varian[$i], $request->stok_varian[$i])) {
                    $varians[] = [
                        'nama' => $v,
                        'harga' => $request->harga_varian[$i],
                        'stok' => $request->stok_varian[$i],
                    ];
                }
            }
            $barangData['varian'] = json_encode($varians);
        } else {
            $barangData['harga'] = $request->harga;
            $barangData['stok'] = $request->stok;
        }

        Barang::create($barangData);

        return redirect()->route('kasir.barang')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return view('kasir.form', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $punyaVarian = $request->has('varian') && collect($request->varian)->filter()->isNotEmpty();

        $request->validate([
            'nama' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'harga' => $punyaVarian ? 'nullable' : 'required|integer|min:0',
            'stok' => $punyaVarian ? 'nullable' : 'required|integer|min:0',
            'varian.*' => $punyaVarian ? 'required|string' : 'nullable',
            'harga_varian.*' => $punyaVarian ? 'required|integer|min:0' : 'nullable',
            'stok_varian.*' => $punyaVarian ? 'required|integer|min:0' : 'nullable',
        ]);

        $barangData = [
            'nama' => $request->nama,
        ];

        if ($request->hasFile('gambar')) {
            if ($barang->gambar && Storage::disk('public')->exists($barang->gambar)) {
                Storage::disk('public')->delete($barang->gambar);
            }
            $barangData['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        if ($punyaVarian) {
            $varians = [];
            foreach ($request->varian as $i => $v) {
                if (isset($request->harga_varian[$i], $request->stok_varian[$i])) {
                    $varians[] = [
                        'nama' => $v,
                        'harga' => $request->harga_varian[$i],
                        'stok' => $request->stok_varian[$i],
                    ];
                }
            }
            $barangData['varian'] = json_encode($varians);
            $barangData['harga'] = null;
            $barangData['stok'] = null;
        } else {
            $barangData['harga'] = $request->harga;
            $barangData['stok'] = $request->stok;
            $barangData['varian'] = null;
        }

        $barang->update($barangData);

        return redirect()->route('kasir.barang', ['tab' => 'data'])
                        ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->gambar && Storage::disk('public')->exists($barang->gambar)) {
            Storage::disk('public')->delete($barang->gambar);
        }

        $barang->delete();
        return redirect()->route('kasir.barang')->with('success', 'Barang berhasil dihapus.');
    }
}
