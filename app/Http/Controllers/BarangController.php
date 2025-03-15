<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Barang',
        ];

        return view('barang.index', [
            'page'       => (object)['title' => 'Data Stok Barang'],
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
                    $btn  = '<button onclick="modalAction(\''.url("barang/".$row->barang_id."/show_ajax").'\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\''.url("barang/".$row->barang_id."/edit_ajax").'\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalAction(\''.url("barang/".$row->barang_id."/delete_ajax").'\')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    // === Endpoint Konvensional (Fallback) ===

    public function create()
    {
        $kategoris = KategoriModel::all();

        // Generate auto-increment kode barang
        $lastBarang = BarangModel::orderBy('barang_id', 'desc')->first();
        if ($lastBarang) {
            $lastCode = $lastBarang->barang_kode;
            if (preg_match('/^BRG(\d+)$/', $lastCode, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
                $nextCode = 'BRG' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            } else {
                $nextCode = 'BRG001';
            }
        } else {
            $nextCode = 'BRG001';
        }

        return view('barang.create', [
            'page'       => (object)['title' => 'Tambah Barang'],
            'kategoris'  => $kategoris,
            'next_code'  => $nextCode
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_kode' => 'required|unique:m_barang,barang_kode',
            'barang_nama' => 'required|string',
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'harga_beli'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0'
        ]);

        BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'kategori_id' => $request->kategori_id,
            'harga_beli'  => $request->harga_beli,
            'harga_jual'  => $request->harga_jual,
            'created_at'  => now()
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
            'harga_beli'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0'
        ]);

        $barang = BarangModel::findOrFail($id);
        $barang->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'kategori_id' => $request->kategori_id,
            'harga_beli'  => $request->harga_beli,
            'harga_jual'  => $request->harga_jual
        ]);

        return redirect('/barang')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        BarangModel::destroy($id);
        return redirect('/barang')->with('success', 'Barang berhasil dihapus');
    }

    // === Endpoint AJAX ===

    // Menampilkan form tambah barang via Ajax
    public function create_ajax()
    {
        $kategoris = KategoriModel::all();

        // Generate auto-increment kode barang seperti di create()
        $lastBarang = BarangModel::orderBy('barang_id', 'desc')->first();
        if ($lastBarang) {
            $lastCode = $lastBarang->barang_kode;
            if (preg_match('/^BRG(\d+)$/', $lastCode, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
                $nextCode = 'BRG' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            } else {
                $nextCode = 'BRG016';
            }
        } else {
            $nextCode = 'BRG016';
        }

        return view('barang.create_ajax', [
            'kategoris' => $kategoris,
            'next_code' => $nextCode
        ]);
    }

    // Menyimpan data barang via Ajax
    public function store_ajax(Request $request)
    {
        $rules = [
            'barang_kode' => 'required|unique:m_barang,barang_kode',
            'barang_nama' => 'required|string',
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'harga_beli'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'kategori_id' => $request->kategori_id,
            'harga_beli'  => $request->harga_beli,
            'harga_jual'  => $request->harga_jual,
            'created_at'  => now()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Barang berhasil ditambahkan'
        ]);
    }

    // Menampilkan form edit barang via Ajax
    public function edit_ajax($id)
    {
        $barang = BarangModel::find($id);
        if (!$barang) {
            return response()->json([
                'status'  => false,
                'message' => 'Data barang tidak ditemukan'
            ]);
        }
        $kategoris = KategoriModel::all();
        return view('barang.edit_ajax', compact('barang', 'kategoris'));
    }

    // Memproses update barang via Ajax
    public function update_ajax(Request $request, $id)
    {
        $rules = [
            'barang_kode' => 'required|unique:m_barang,barang_kode,'.$id.',barang_id',
            'barang_nama' => 'required|string',
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'harga_beli'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        $barang = BarangModel::find($id);
        if ($barang) {
            $barang->update([
                'barang_kode' => $request->barang_kode,
                'barang_nama' => $request->barang_nama,
                'kategori_id' => $request->kategori_id,
                'harga_beli'  => $request->harga_beli,
                'harga_jual'  => $request->harga_jual
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Barang berhasil diperbarui'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Data barang tidak ditemukan'
            ]);
        }
    }

    // Menampilkan modal konfirmasi hapus barang via Ajax
    public function confirm_ajax($id)
    {
        $barang = BarangModel::find($id);
        return view('barang.confirm_ajax', compact('barang'));
    }

    // Memproses penghapusan barang via Ajax
    public function delete_ajax(Request $request, $id)
    {
        $barang = BarangModel::find($id);
        if ($barang) {
            $barang->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Barang berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Data barang tidak ditemukan'
            ]);
        }
    }

    // Menampilkan detail barang via Ajax
    public function show_ajax($id)
    {
        $barang = BarangModel::with('kategori')->find($id);
        return view('barang.show_ajax', compact('barang'));
    }
}
