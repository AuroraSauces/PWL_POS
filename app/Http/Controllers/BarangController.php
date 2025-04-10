<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $activeMenu = 'barang';
        $breadcrumb = (object)[
            'title' => 'Data Barang',
            'list' => ['Home', 'Barang']
        ];
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('barang.index', compact('activeMenu', 'breadcrumb', 'kategori'));
    }

    public function list(Request $request)
    {
        $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')->with('kategori');

        if ($request->filled('filter_kategori')) {
            $barang->where('kategori_id', $request->input('filter_kategori'));
        }

        return DataTables::of($barang)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                $btn  = '<button onclick="modalAction(\''.url("/barang/{$barang->barang_id}/show_ajax").'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\''.url("/barang/{$barang->barang_id}/edit_ajax").'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url("/barang/{$barang->barang_id}/delete_ajax").'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        return view('barang.create_ajax', compact('kategori'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'kategori_id' => ['required', 'integer', 'exists:m_kategori,kategori_id'],
                'barang_kode' => ['required', 'min:3', 'max:20', 'unique:m_barang,barang_kode'],
                'barang_nama' => ['required', 'string', 'max:100'],
                'harga_beli'  => ['required', 'numeric'],
                'harga_jual'  => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            BarangModel::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan'
            ]);
        }

        return redirect('/');
    }

    public function edit_ajax($id)
    {
        $barang = BarangModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('barang.edit_ajax', compact('barang', 'level'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'kategori_id' => ['required', 'integer', 'exists:m_kategori,kategori_id'],
                'barang_kode' => ['required', 'min:3', 'max:20', 'unique:m_barang,barang_kode,' . $id . ',barang_id'],
                'barang_nama' => ['required', 'string', 'max:100'],
                'harga_beli'  => ['required', 'numeric'],
                'harga_jual'  => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->update($request->all());

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function confirm_ajax($id)
    {
        $barang = BarangModel::find($id);
        return view('barang.confirm_ajax', compact('barang'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }

    public function show_ajax($id)
    {
        $barang = BarangModel::with('kategori')->find($id);
        return view('barang.show_ajax', compact('barang'));
    }

    public function import()
    {
        return view('barang.import');
    }

    public function import_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {

        $validator = Validator::make($request->all(), [
            'file_barang' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:2048'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            $file = $request->file('file_barang');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];

            if (count($data) > 1) {
                foreach ($data as $index => $row) {
                    if ($index > 1) {
                        if (!empty($row['B']) && !empty($row['C'])) {
                            $insert[] = [
                                'kategori_id' => $row['A'] ?? null,
                                'barang_kode' => $row['B'],
                                'barang_nama' => $row['C'],
                                'harga_beli'  => $row['D'] ?? 0,
                                'harga_jual'  => $row['E'] ?? 0,
                                'created_at'  => now(),
                            ];
                        }
                    }
                }

                if (count($insert) > 0) {
                    BarangModel::insertOrIgnore($insert);

                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil diimport'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Tidak ada data valid untuk diimport'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Sheet kosong atau tidak sesuai format'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membaca file',
                'debug' => $e->getMessage()
            ]);
        }
    }

    return redirect('/');
}

}
