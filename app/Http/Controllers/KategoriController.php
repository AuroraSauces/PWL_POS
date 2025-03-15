<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
        ];

        return view('kategori.index', [
            'page' => (object) ['title' => 'Data Kategori Barang'],
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
                    return '
                        <a href="'.url("kategori/$row->kategori_id/edit").'" class="btn btn-warning btn-sm">Edit</a>
                        <form method="POST" action="'.url("kategori/$row->kategori_id").'" style="display:inline;">
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
            'kategori_kode' => $request->kategori_kode ?? '-',
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
}
