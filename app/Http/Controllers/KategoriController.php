<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            if ($kategori) {
                Try{
                    $kategori->delete();
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

    // Menampilkan detail kategori via Ajax
    public function show_ajax($id)
    {
        $kategori = KategoriModel::find($id);
        return view('kategori.show_ajax', compact('kategori'));
    }

    public function import()
    {
    return view('kategori.import');
    }


public function import_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {

        $validator = Validator::make($request->all(), [
            'file_kategori' => [
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
            $file = $request->file('file_kategori');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];

            if (count($data) > 1) {
                foreach ($data as $index => $row) {
                    if ($index > 1) {
                        if (!empty($row['A']) && !empty($row['B'])) {
                            $insert[] = [
                                'kategori_kode' => $row['A'],
                                'kategori_nama' => $row['B'],
                                'created_at'  => now(),
                            ];
                        }
                    }
                }

                if (count($insert) > 0) {
                    KategoriModel::insertOrIgnore($insert);

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

public function export_excel()
{
    // Ambil data kategori
    $kategori = KategoriModel::select('kategori_kode', 'kategori_nama')
        ->orderBy('kategori_kode')
        ->get();

    // Load library PhpSpreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set judul kolom (header)
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Kode Kategori');
    $sheet->setCellValue('C1', 'Nama Kategori');
    $sheet->getStyle('A1:C1')->getFont()->setBold(true);

    // Isi data ke baris berikutnya
    $no = 1;
    $baris = 2;
    foreach ($kategori as $value) {
        $sheet->setCellValue('A' . $baris, $no);
        $sheet->setCellValue('B' . $baris, $value->kategori_kode);
        $sheet->setCellValue('C' . $baris, $value->kategori_nama);
        $no++;
        $baris++;
    }

    // Auto size kolom
    foreach (range('A', 'C') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $sheet->setTitle('Data Kategori');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = 'Data Kategori ' . date('Y-m-d H-i-s') . '.xlsx';

    // Set header untuk download file
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header("Cache-Control: max-age=0");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: cache, must-revalidate");
    header("Pragma: public");

    $writer->save('php://output');
    exit;
}

public function export_pdf()
{
    $kategori = KategoriModel::select('kategori_kode', 'kategori_nama')
        ->orderBy('kategori_kode')
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kategori.export_pdf', ['kategori' => $kategori]);
    $pdf->setPaper('a4', 'portrait');
    $pdf->setOption('isRemoteEnabled', true);
    $pdf->render();

    return $pdf->stream('Data Kategori ' . date('Y-m-d H:i:s') . '.pdf');
}


}
