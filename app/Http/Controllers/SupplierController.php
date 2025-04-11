<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

    public function import()
    {
    return view('supplier.import');
    }


public function import_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {

        $validator = Validator::make($request->all(), [
            'file_supplier' => [
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
            $file = $request->file('file_supplier');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];

            if (count($data) > 1) {
                foreach ($data as $index => $row) {
                    if ($index > 1) {
                        if (!empty($row['A']) && !empty($row['B'])) {
                            $insert[] = [
                                'supplier_kode' => $row['A'],
                                'supplier_nama' => $row['B'],
                                'supplier_alamat' => $row['C'],
                                'supplier_kontak' => $row['D'],
                                'created_at'  => now(),
                            ];
                        }
                    }
                }

                if (count($insert) > 0) {
                    SupplierModel::insertOrIgnore($insert);

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
    // Ambil data supplier
    $supplier = SupplierModel::select('supplier_kode', 'supplier_nama', 'supplier_alamat', 'supplier_kontak')
        ->orderBy('supplier_kode')
        ->get();

    // Load library PhpSpreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set judul kolom (header)
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Kode Supplier');
    $sheet->setCellValue('C1', 'Nama Supplier');
    $sheet->setCellValue('D1', 'Alamat');
    $sheet->setCellValue('E1', 'Kontak');
    $sheet->getStyle('A1:E1')->getFont()->setBold(true);

    // Isi data ke baris berikutnya
    $no = 1;
    $baris = 2;
    foreach ($supplier as $value) {
        $sheet->setCellValue('A' . $baris, $no);
        $sheet->setCellValue('B' . $baris, $value->supplier_kode);
        $sheet->setCellValue('C' . $baris, $value->supplier_nama);
        $sheet->setCellValue('D' . $baris, $value->supplier_alamat);
        $sheet->setCellValue('E' . $baris, $value->supplier_kontak);
        $no++;
        $baris++;
    }

    // Auto size kolom
    foreach (range('A', 'E') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $sheet->setTitle('Data Supplier');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = 'Data Supplier ' . date('Y-m-d H-i-s') . '.xlsx';

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
    $supplier = SupplierModel::select('supplier_kode', 'supplier_nama', 'supplier_alamat', 'supplier_kontak')
        ->orderBy('supplier_kode')
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('supplier.export_pdf', ['supplier' => $supplier]);
    $pdf->setPaper('a4', 'portrait'); // Ukuran dan orientasi kertas
    $pdf->setOption('isRemoteEnabled', true); // Aktifkan jika ada gambar URL
    $pdf->render();

    return $pdf->stream('Data Supplier ' . date('Y-m-d H:i:s') . '.pdf');
}


}
