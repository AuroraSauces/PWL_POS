<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index()
    {
        $activeMenu = 'stok';
        $stok = StokModel::with('barang')->latest()->get();

        return view('stok.index', compact('stok', 'activeMenu'));
    }

    public function create()
    {
        $barang = BarangModel::all();
        return view('stok.create', compact('barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:m_barang,barang_id',
            'stok_jumlah' => 'required|integer|min:1',
        ]);

        StokModel::create([
            'barang_id' => $request->barang_id,
            'user_id' => auth()->id(), // pastikan user login
            'stok_tanggal' => now(),
            'stok_jumlah' => $request->stok_jumlah,
        ]);

        return redirect()->route('stok.index')->with('success', 'Stok berhasil ditambahkan');
    }

    // Method untuk halaman edit stok
    public function edit($id)
    {
        // Ambil data stok yang akan di-edit berdasarkan id
        $stok = StokModel::findOrFail($id);
        $barang = BarangModel::all();  // Menyediakan list barang untuk dipilih saat edit

        return view('stok.edit', compact('stok', 'barang'));
    }

    // Method untuk meng-update stok setelah di-edit
    public function update(Request $request, $id)
    {
        $request->validate([
            'barang_id' => 'required|exists:m_barang,barang_id',
            'stok_jumlah' => 'required|integer|min:1',
        ]);

        // Cari stok berdasarkan id
        $stok = StokModel::findOrFail($id);

        // Update data stok
        $stok->update([
            'barang_id' => $request->barang_id,
            'stok_jumlah' => $request->stok_jumlah,
            'stok_tanggal' => now(),  // Tanggal stok bisa disesuaikan jika perlu
        ]);

        return redirect()->route('stok.index')->with('success', 'Stok berhasil diperbarui');
    }

    // (Optional) Method untuk menghapus stok
    public function destroy($id)
    {
        $stok = StokModel::findOrFail($id);
        $stok->delete();

        return redirect()->route('stok.index')->with('success', 'Stok berhasil dihapus');
    }
}
