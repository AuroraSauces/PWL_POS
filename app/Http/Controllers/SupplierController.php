<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    // === Endpoint Konvensional (Fallback) ===

    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Supplier',
        ];

        return view('supplier.index', [
            'page'       => (object)['title' => 'Data Supplier'],
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
                    $btn  = '<button onclick="modalAction(\''.url("supplier/".$row->supplier_id."/show_ajax").'\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\''.url("supplier/".$row->supplier_id."/edit_ajax").'\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalAction(\''.url("supplier/".$row->supplier_id."/delete_ajax").'\')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btn;
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
            'supplier_kode'  => 'required|unique:m_supplier,supplier_kode',
            'supplier_nama'  => 'required|string|max:255',
            'supplier_alamat'=> 'nullable|string',
            'supplier_kontak'=> 'nullable|string|max:255'
        ]);

        SupplierModel::create([
            'supplier_kode'  => $request->supplier_kode,
            'supplier_nama'  => $request->supplier_nama,
            'supplier_alamat'=> $request->supplier_alamat,
            'supplier_kontak'=> $request->supplier_kontak
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
            'supplier_kode'  => 'required|unique:m_supplier,supplier_kode,'.$id.',supplier_id',
            'supplier_nama'  => 'required|string|max:255',
            'supplier_alamat'=> 'nullable|string',
            'supplier_kontak'=> 'nullable|string|max:255'
        ]);

        $supplier = SupplierModel::findOrFail($id);
        $supplier->update([
            'supplier_kode'  => $request->supplier_kode,
            'supplier_nama'  => $request->supplier_nama,
            'supplier_alamat'=> $request->supplier_alamat,
            'supplier_kontak'=> $request->supplier_kontak
        ]);

        return redirect('/supplier')->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy($id)
    {
        SupplierModel::destroy($id);
        return redirect('/supplier')->with('success', 'Supplier berhasil dihapus');
    }

    // === Endpoint AJAX ===

    // Menampilkan form tambah supplier via Ajax
    public function create_ajax()
    {
        return view('supplier.create_ajax');
    }

    // Menyimpan data supplier via Ajax
    public function store_ajax(Request $request)
    {
        $rules = [
            'supplier_kode'  => 'required|unique:m_supplier,supplier_kode',
            'supplier_nama'  => 'required|string|max:255',
            'supplier_alamat'=> 'nullable|string',
            'supplier_kontak'=> 'nullable|string|max:255'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        SupplierModel::create([
            'supplier_kode'  => $request->supplier_kode,
            'supplier_nama'  => $request->supplier_nama,
            'supplier_alamat'=> $request->supplier_alamat,
            'supplier_kontak'=> $request->supplier_kontak
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Supplier berhasil ditambahkan'
        ]);
    }

    // Menampilkan form edit supplier via Ajax
    public function edit_ajax($id)
    {
        $supplier = SupplierModel::find($id);
        if (!$supplier) {
            return response()->json([
                'status'  => false,
                'message' => 'Data supplier tidak ditemukan'
            ]);
        }
        return view('supplier.edit_ajax', compact('supplier'));
    }

    // Memproses update supplier via Ajax
    public function update_ajax(Request $request, $id)
    {
        $rules = [
            'supplier_kode'  => 'required|unique:m_supplier,supplier_kode,'.$id.',supplier_id',
            'supplier_nama'  => 'required|string|max:255',
            'supplier_alamat'=> 'nullable|string',
            'supplier_kontak'=> 'nullable|string|max:255'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        $supplier = SupplierModel::find($id);
        if ($supplier) {
            $supplier->update([
                'supplier_kode'  => $request->supplier_kode,
                'supplier_nama'  => $request->supplier_nama,
                'supplier_alamat'=> $request->supplier_alamat,
                'supplier_kontak'=> $request->supplier_kontak
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Supplier berhasil diperbarui'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Data supplier tidak ditemukan'
            ]);
        }
    }

    // Menampilkan modal konfirmasi hapus supplier via Ajax
    public function confirm_ajax($id)
    {
        $supplier = SupplierModel::find($id);
        return view('supplier.confirm_ajax', compact('supplier'));
    }

    // Memproses penghapusan supplier via Ajax
    public function delete_ajax(Request $request, $id)
    {
        $supplier = SupplierModel::find($id);
        if ($supplier) {
            if ($supplier) {
                Try{
                    $supplier->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch(\illuminate\Database\QueryException $e){
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data tidak bisa dihapus karena masih berhubungan'
                    ]);
                }} else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data level tidak ditemukan'
                ]);
            }
        }}

    // Menampilkan detail supplier via Ajax (jika diperlukan)
    public function show_ajax($id)
    {
        $supplier = SupplierModel::find($id);
        return view('supplier.show_ajax', compact('supplier'));
    }
}
