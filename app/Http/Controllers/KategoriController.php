<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Kategori',
        ];

        return view('kategori.index', [
            'page'       => (object)['title' => 'Data Kategori Barang'],
            'breadcrumb' => $breadcrumb
        ]);
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = KategoriModel::select('kategori_id', 'kategori_nama', 'kategori_kode')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    $btn  = '<button onclick="modalAction(\''.url("kategori/".$row->kategori_id."/show_ajax").'\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\''.url("kategori/".$row->kategori_id."/edit_ajax").'\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalAction(\''.url("kategori/".$row->kategori_id."/delete_ajax").'\')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    // === Endpoint Konvensional (Fallback) ===

    public function create()
    {
        return view('kategori.create', [
            'page' => (object)['title' => 'Tambah Kategori']
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_nama' => 'required|unique:m_kategori,kategori_nama',
            'kategori_kode' => 'nullable|string|unique:m_kategori,kategori_kode'
        ]);

        KategoriModel::create([
            'kategori_nama' => $request->kategori_nama,
            'kategori_kode' => $request->kategori_kode ?? '-'
        ]);

        return redirect('/kategori')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function show($id)
    {
        $kategori = KategoriModel::findOrFail($id);
        return view('kategori.show', compact('kategori'));
    }

    public function edit($id)
    {
        $kategori = KategoriModel::findOrFail($id);
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_nama' => 'required|unique:m_kategori,kategori_nama,'.$id.',kategori_id',
            'kategori_kode' => 'nullable|string|unique:m_kategori,kategori_kode,'.$id.',kategori_id'
        ]);

        $kategori = KategoriModel::findOrFail($id);
        $kategori->update([
            'kategori_nama' => $request->kategori_nama,
            'kategori_kode' => $request->kategori_kode ?? $kategori->kategori_kode,
        ]);

        return redirect('/kategori')->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        KategoriModel::destroy($id);
        return redirect('/kategori')->with('success', 'Kategori berhasil dihapus');
    }

    // === Endpoint AJAX ===

    // Menampilkan form tambah kategori via Ajax
    public function create_ajax()
    {
        return view('kategori.create_ajax');
    }

    // Menyimpan data kategori via Ajax
    public function store_ajax(Request $request)
    {
        $rules = [
            'kategori_nama' => 'required|unique:m_kategori,kategori_nama',
            'kategori_kode' => 'nullable|string|unique:m_kategori,kategori_kode'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        KategoriModel::create([
            'kategori_nama' => $request->kategori_nama,
            'kategori_kode' => $request->kategori_kode ?? '-'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil ditambahkan'
        ]);
    }

    // Menampilkan form edit kategori via Ajax
    public function edit_ajax($id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'status'  => false,
                'message' => 'Data kategori tidak ditemukan'
            ]);
        }

        return view('kategori.edit_ajax', compact('kategori'));
    }

    public function update_ajax(Request $request, $id)
    {
        $rules = [
            'kategori_nama' => 'required|unique:m_kategori,kategori_nama,' . $id . ',kategori_id',
            'kategori_kode' => 'nullable|string|unique:m_kategori,kategori_kode,' . $id . ',kategori_id'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        $kategori = KategoriModel::find($id);
        if ($kategori) {
            $kategori->update([
                'kategori_nama' => $request->kategori_nama,
                'kategori_kode' => $request->kategori_kode ?? $kategori->kategori_kode,
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Kategori berhasil diperbarui'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Data kategori tidak ditemukan'
            ]);
        }
    }

    // Menampilkan modal konfirmasi hapus kategori via Ajax
    public function confirm_ajax($id)
    {
        $kategori = KategoriModel::find($id);
        return view('kategori.confirm_ajax', compact('kategori'));
    }

    // Memproses penghapusan kategori via Ajax
    public function delete_ajax(Request $request, $id)
    {
        $kategori = KategoriModel::find($id);
        if ($kategori) {
            $kategori->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Data kategori tidak ditemukan'
            ]);
        }
    }

    // Menampilkan detail kategori via Ajax
    public function show_ajax($id)
    {
        $kategori = KategoriModel::find($id);
        return view('kategori.show_ajax', compact('kategori'));
    }
}
