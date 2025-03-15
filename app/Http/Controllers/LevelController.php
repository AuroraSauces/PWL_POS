<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
        ];
        return view('level.index', [
            'page' => (object)['title' => 'Data Level'],
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
                return '
                    <a href="'.url("level/$row->level_id/edit").'" class="btn btn-warning btn-sm">Edit</a>
                    <form method="POST" action="'.url("level/$row->level_id").'" style="display:inline;">
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
            'level_kode' => $request->level_kode ?? '-',
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
}
