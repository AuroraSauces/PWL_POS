<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list'  => ['Home', 'User']
        ];

        $page = (object) [
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user';
        $level = LevelModel::all();

        return view('user.index', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }

    public function list(Request $request)
{
    {
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
            ->with('level');
        // Filter data user berdasarkan level_id
        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }
        return DataTables::of($users)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addColumn('aksi', function ($user) {

                $btn = '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }
}


    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list'  => ['Home', 'User', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah user baru'
        ];

        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('user.create', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama'     => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer'
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id
        ]);

        return redirect('/user')->with('success', 'Data user berhasil disimpan');
    }

    public function show(string $id)
    {
        $user = UserModel::with('level')->find($id);

        if (!$user) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list'  => ['Home', 'User', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail user'
        ];

        $activeMenu = 'user';

        return view('user.show', compact('breadcrumb', 'page', 'user', 'activeMenu'));
    }

    public function edit(string $id)
    {
        $user = UserModel::find($id);
        if (!$user) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }

        $level = LevelModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list'  => ['Home', 'User', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit user'
        ];

        $activeMenu = 'user';

        return view('user.edit', compact('breadcrumb', 'page', 'user', 'level', 'activeMenu'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'username'  => "required|string|min:3|unique:m_user,username,$id,user_id",
            'nama'      => 'required|string|max:100',
            'password'  => 'nullable|min:5',
            'level_id'  => 'required|integer'
        ]);

        $user = UserModel::find($id);
        if (!$user) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }

        $user->update([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'level_id' => $request->level_id
        ]);

        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }

    public function destroy(string $id)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }

        try {
            UserModel::destroy($id);
            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
    public function create_ajax()
{
    $level = LevelModel::select('level_id', 'level_nama')->get();
    return view('user.create_ajax')->with('level', $level);
}

public function store_ajax(Request $request)
{
    $request->validate([
        'username' => 'required|string|min:3|unique:m_user,username',
        'nama'     => 'required|string|max:100',
        'password' => 'required|min:5',
        'level_id' => 'required|integer'
    ]);

    $user = UserModel::create([
        'username' => $request->username,
        'nama'     => $request->nama,
        'password' => bcrypt($request->password),
        'level_id' => $request->level_id
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Data user berhasil disimpan'
    ]);
}

public function edit_ajax(string $id)
{
    $user = UserModel::find($id);
    $level = LevelModel::select('level_id', 'level_nama')->get();
    return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
}

public function update_ajax(Request $request, $id)
{
    // cek apakah request dari ajax
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'level_id' => 'required|integer',
            'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
            'nama'     => 'required|max:100',
            'password' => 'nullable|min:6|max:20'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }

        $check = UserModel::find($id);
        if ($check) {
            if (!$request->filled('password')) {
                // jika password tidak diisi, maka hapus dari request
                $request->request->remove('password');
            }
            $check->update($request->all());
            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil diupdate'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
    return redirect('/user');
}

public function confirm_ajax(string $id)
{
    $user = UserModel::find($id);
    return view('user.confirm_ajax', ['user' => $user]);
}
public function delete_ajax(Request $request, $id)
{
    // cek apakah request dari ajax
    if ($request->ajax() || $request->wantsJson()) {
        $user = UserModel::find($id);
        if ($user) {
            Try{
                $user->delete();
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } catch(\illuminate\Database\QueryException $e){
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak bisa dihapus karena masih berhubungan'
                ]);
            }
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
    return redirect('/user');
}

public function import()
{
    return view('user.import');
}


public function import_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {

        $validator = Validator::make($request->all(), [
            'file_user' => [
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
            $file = $request->file('file_user');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];

            if (count($data) > 1) {
                foreach ($data as $index => $row) {
                    if ($index > 1) {
                        if (!empty($row['A']) && !empty($row['B']) && !empty($row['D'])) {
                            $insert[] = [
                                'username'   => $row['A'],
                                'nama'       => $row['B'],
                                'level_id'   => $row['C'],
                                'password'   => bcrypt($row['D']), // kolom D sebagai password
                                'created_at' => now(),
                            ];
                        }
                    }
                }

                if (count($insert) > 0) {
                    UserModel::insertOrIgnore($insert);

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
    // Ambil data user beserta nama level
    $user = UserModel::select('username', 'nama', 'level_id')
        ->with('level')
        ->orderBy('level_id')
        ->get();

    // Load library PhpSpreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set judul kolom (header)
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Username');
    $sheet->setCellValue('C1', 'Nama');
    $sheet->setCellValue('D1', 'Level');
    $sheet->getStyle('A1:D1')->getFont()->setBold(true);

    // Isi data ke baris berikutnya
    $no = 1;
    $baris = 2;
    foreach ($user as $value) {
        $sheet->setCellValue('A' . $baris, $no);
        $sheet->setCellValue('B' . $baris, $value->username);
        $sheet->setCellValue('C' . $baris, $value->nama);
        $sheet->setCellValue('D' . $baris, $value->level->level_nama ?? '-');
        $no++;
        $baris++;
    }

    // Auto size kolom
    foreach (range('A', 'D') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $sheet->setTitle('Data User');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = 'Data User ' . date('Y-m-d H-i-s') . '.xlsx';

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
    $user = UserModel::select('username', 'nama', 'level_id')
        ->with('level')
        ->orderBy('level_id')
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.export_pdf', ['user' => $user]);
    $pdf->setPaper('a4', 'portrait');
    $pdf->setOption('isRemoteEnabled', true);
    $pdf->render();

    return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
}

}
