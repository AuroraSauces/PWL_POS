<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierModel;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index()
{
    $breadcrumb = (object) [
        'title' => 'Daftar Supplier',
    ];

    return view('supplier.index', [
        'page' => (object) ['title' => 'Data Supplier'],
        'breadcrumb' => $breadcrumb
    ]);
}


    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = SupplierModel::select(
                'supplier_id',
                'supplier_kode',
                'supplier_nama',
                'supplier_alamat',
                'supplier_kontak'
            )->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    return '
                        <a href="'.url("supplier/$row->supplier_id/edit").'" class="btn btn-warning btn-sm">Edit</a>
                        <form method="POST" action="'.url("supplier/$row->supplier_id").'" style="display:inline;">
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
        return view('supplier.create', [
            'page' => (object)['title' => 'Tambah Supplier']
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_kode' => 'required|unique:m_supplier,supplier_kode',
            'supplier_nama' => 'required|string|max:255',
            'supplier_alamat' => 'nullable|string',
            'supplier_kontak' => 'nullable|string|max:255'
        ]);

        SupplierModel::create([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat,
            'supplier_kontak' => $request->supplier_kontak
        ]);

        return redirect('/supplier')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function show($id)
    {
        $supplier = SupplierModel::findOrFail($id);
        return view('supplier.show', compact('supplier'));
    }

    public function edit($id)
    {
        $supplier = SupplierModel::findOrFail($id);
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_kode' => 'required|unique:m_supplier,supplier_kode,'.$id.',supplier_id',
            'supplier_nama' => 'required|string|max:255',
            'supplier_alamat' => 'nullable|string',
            'supplier_kontak' => 'nullable|string|max:255'
        ]);

        $supplier = SupplierModel::findOrFail($id);
        $supplier->update([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat,
            'supplier_kontak' => $request->supplier_kontak
        ]);

        return redirect('/supplier')->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy($id)
    {
        SupplierModel::destroy($id);
        return redirect('/supplier')->with('success', 'Supplier berhasil dihapus');
    }
}
