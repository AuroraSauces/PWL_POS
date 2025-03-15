<?php
// app/Http/Controllers/BarangController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
{
    $breadcrumb = (object) [
        'title' => 'Daftar Barang',
    ];

    return view('barang.index', [
        'page' => (object) ['title' => 'Data Stok Barang'],
        'breadcrumb' => $breadcrumb
    ]);
}

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = BarangModel::with('kategori')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kategori_nama', function($row) {
                    return $row->kategori->kategori_nama ?? '-';
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <a href="'.url("barang/$row->barang_id").'" class="btn btn-info btn-sm">Detail</a>
                        <a href="'.url("barang/$row->barang_id/edit").'" class="btn btn-warning btn-sm">Edit</a>
                        <form method="POST" action="'.url("barang/$row->barang_id").'" style="display:inline;">
                            '.csrf_field().method_field("DELETE").'
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Hapus data ini?\')">Hapus</button>
                        </form>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function create()
    {
        $kategoris = KategoriModel::all();

        // Generate auto-increment kode barang
        $lastBarang = BarangModel::orderBy('barang_id', 'desc')->first();

        if ($lastBarang) {
            // Jika format kode berupa prefix + angka (misalnya BRG001)
            $lastCode = $lastBarang->barang_kode;
            if (preg_match('/^BRG(\d+)$/', $lastCode, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
                $nextCode = 'BRG' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            } else {
                // Jika format tidak sesuai, mulai dengan BRG001
                $nextCode = 'BRG001';
            }
        } else {
            // Jika belum ada data, mulai dengan BRG001
            $nextCode = 'BRG001';
        }

        return view('barang.create', [
            'page' => (object)['title' => 'Tambah Barang'],
            'kategoris' => $kategoris,
            'next_code' => $nextCode
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_kode' => 'required|unique:m_barang,barang_kode',
            'barang_nama' => 'required|string',
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0'
        ]);

        BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'kategori_id' => $request->kategori_id,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'created_at' => now()
        ]);

        return redirect('/barang')->with('success', 'Barang berhasil ditambahkan');
    }

    public function show($id)
    {
        $barang = BarangModel::with('kategori')->findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = BarangModel::findOrFail($id);
        $kategoris = KategoriModel::all();
        return view('barang.edit', compact('barang', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'barang_kode' => 'required|unique:m_barang,barang_kode,'.$id.',barang_id',
            'barang_nama' => 'required|string',
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0'
        ]);

        $barang = BarangModel::findOrFail($id);
        $barang->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'kategori_id' => $request->kategori_id,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual
        ]);

        return redirect('/barang')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        BarangModel::destroy($id);
        return redirect('/barang')->with('success', 'Barang berhasil dihapus');
    }
}
