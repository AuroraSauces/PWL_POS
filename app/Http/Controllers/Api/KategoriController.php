<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriModel;

class KategoriController extends Controller
{
    public function index()
    {
        return KategoriModel::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kategori_nama' => 'required|string|max:255|unique:m_kategori,kategori_nama',
            'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
        ]);

        $kategori = KategoriModel::create($validatedData);
        return response()->json($kategori, 201);
    }

    public function show($id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        return response()->json($kategori);
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $validatedData = $request->validate([
            'kategori_nama' => 'sometimes|string|max:255|unique:m_kategori,kategori_nama,' . $kategori->kategori_id . ',kategori_id',
            'kategori_kode' => 'sometimes|string|max:10|unique:m_kategori,kategori_kode,' . $kategori->kategori_id . ',kategori_id',
        ]);

        $kategori->update($validatedData);
        return response()->json($kategori);
    }

    public function destroy($id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        try {
            $kategori->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
