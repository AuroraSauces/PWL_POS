<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokModel;
use App\Models\BarangModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Data Stok Barang'
        ];

        return view('stok.index', [
            'page'       => (object)['title' => 'Stok Barang'],
            'breadcrumb' => $breadcrumb
        ]);
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $stok = StokModel::with('barang')->select('stok_id', 'barang_id', 'stok_jumlah', 'stok_tanggal');

            return DataTables::of($stok)
                ->addIndexColumn()
                ->addColumn('barang_nama', function ($row) {
                    return $row->barang->barang_nama ?? '-';
                })
                ->addColumn('aksi', function ($row) {
                    // Menggunakan route untuk delete dengan metode DELETE
                    return '<button onclick="deleteData(\''.route('stok.delete', $row->stok_id).'\')" class="btn btn-danger btn-sm">Hapus</button>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function create_ajax()
    {
        $barang = BarangModel::all();
        return view('stok.create_ajax', compact('barang'));
    }

    public function store_ajax(Request $request)
    {
        $rules = [
            'barang_id' => 'required|exists:m_barang,barang_id',
            'stok_jumlah' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        StokModel::create([
            'barang_id'     => $request->barang_id,
            'user_id'       => auth()->id(),
            'stok_tanggal'  => now(),
            'stok_jumlah'   => $request->stok_jumlah
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Stok berhasil ditambahkan'
        ]);
    }

    public function delete_ajax($id)
    {
        // Mencari data stok berdasarkan ID
        $stok = StokModel::find($id);

        // Jika data stok tidak ditemukan, beri respons gagal
        if (!$stok) {
            return response()->json([
                'status'  => false,
                'message' => 'Data stok tidak ditemukan'
            ]);
        }

        try {
            // Coba hapus data stok
            $stok->delete();

            // Jika berhasil, kirimkan respons sukses
            return response()->json([
                'status'  => true,
                'message' => 'Data stok berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan saat penghapusan
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus data stok'
            ]);
        }
    }

    public function confirm_ajax($id)
    {
        $stok = StokModel::with('barang')->find($id);
        return view('stok.confirm_ajax', compact('stok'));
    }
}
