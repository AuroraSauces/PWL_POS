<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Level',
        ];

        return view('level.index', [
            'page'       => (object)['title' => 'Data Level'],
            'breadcrumb' => $breadcrumb
        ]);
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = LevelModel::select('level_id', 'level_nama', 'level_kode')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    $btn  = '<button onclick="modalAction(\''.url("level/".$row->level_id."/show_ajax").'\')" class="btn btn-info btn-sm">Detail</button> ';
                    $btn .= '<button onclick="modalAction(\''.url("level/".$row->level_id."/edit_ajax").'\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalAction(\''.url("level/".$row->level_id."/delete_ajax").'\')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    // === Endpoint Konvensional (Fallback) ===

    public function create()
    {
        return view('level.create', [
            'page' => (object)['title' => 'Tambah Level']
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_nama' => 'required|unique:m_level,level_nama',
            'level_kode' => 'nullable|string|unique:m_level,level_kode'
        ]);

        LevelModel::create([
            'level_nama' => $request->level_nama,
            'level_kode' => $request->level_kode ?? '-'
        ]);

        return redirect('/level')->with('success', 'Level berhasil ditambahkan');
    }

    public function show($id)
    {
        $level = LevelModel::findOrFail($id);
        return view('level.show', compact('level'));
    }

    public function edit($id)
    {
        $level = LevelModel::findOrFail($id);
        return view('level.edit', compact('level'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'level_nama' => 'required|unique:m_level,level_nama,'.$id.',level_id',
            'level_kode' => 'nullable|string|unique:m_level,level_kode,'.$id.',level_id'
        ]);

        $level = LevelModel::findOrFail($id);
        $level->update([
            'level_nama' => $request->level_nama,
            'level_kode' => $request->level_kode ?? $level->level_kode,
        ]);

        return redirect('/level')->with('success', 'Level berhasil diperbarui');
    }

    public function destroy($id)
    {
        LevelModel::destroy($id);
        return redirect('/level')->with('success', 'Level berhasil dihapus');
    }

    // === Endpoint AJAX ===

    // Menampilkan form tambah level via Ajax
    public function create_ajax()
{
    return view('level.create_ajax');
}

    // Menyimpan data level via Ajax
    public function store_ajax(Request $request)
{
    $rules = [
        'level_nama' => 'required|unique:m_level,level_nama',
        'level_kode' => 'nullable|string|unique:m_level,level_kode'
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json([
            'status'   => false,
            'message'  => 'Validasi gagal.',
            'msgField' => $validator->errors()
        ]);
    }

    LevelModel::create([
        'level_nama' => $request->level_nama,
        'level_kode' => $request->level_kode ?? '-'
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Data level berhasil disimpan'
    ]);
}


    // Menampilkan form edit level via Ajax
 public function edit_ajax($id)
{
    $level = LevelModel::find($id);

    if (!$level) {
        return response()->json([
            'status'  => false,
            'message' => 'Data level tidak ditemukan'
        ]);
    }

    return view('level.edit_ajax', compact('level'));
}

public function update_ajax(Request $request, $id)
{
    $rules = [
        'level_nama' => 'required|unique:m_level,level_nama,' . $id . ',level_id',
        'level_kode' => 'nullable|string|unique:m_level,level_kode,' . $id . ',level_id'
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json([
            'status'   => false,
            'message'  => 'Validasi gagal.',
            'msgField' => $validator->errors()
        ]);
    }

    $level = LevelModel::find($id);
    if ($level) {
        $level->update([
            'level_nama' => $request->level_nama,
            'level_kode' => $request->level_kode ?? $level->level_kode,
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Data level berhasil diperbarui'
        ]);
    } else {
        return response()->json([
            'status'  => false,
            'message' => 'Data level tidak ditemukan'
        ]);
    }
}

    // Menampilkan modal konfirmasi hapus level via Ajax
    public function confirm_ajax($id)
    {
        $level = LevelModel::find($id);
        return view('level.confirm_ajax', compact('level'));
    }

    // Memproses penghapusan level via Ajax
    public function delete_ajax(Request $request, $id)
    {
        $level = LevelModel::find($id);
        if ($level) {
            Try{
                $level->delete();
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
    }
public function show_ajax($id)
{
    $level = LevelModel::find($id);
    return view('level.show_ajax', compact('level'));
}
}
