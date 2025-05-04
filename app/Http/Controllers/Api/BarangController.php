<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangModel;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index()
    {
        return BarangModel::with('kategori')->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kategori_id' => 'required|integer|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|min:3|max:20|unique:m_barang,barang_kode',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $validatedData['image'] = $image->hashName();

            // Store the image
            $image->store('public/barang');
        }

        $barang = BarangModel::create($validatedData);
        return response()->json($barang, 201);
    }

    public function show($id)
    {
        $barang = BarangModel::with('kategori')->find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        return response()->json($barang);
    }

    public function update(Request $request, $id)
    {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        $validatedData = $request->validate([
            'kategori_id' => 'sometimes|integer|exists:m_kategori,kategori_id',
            'barang_kode' => 'sometimes|string|min:3|max:20|unique:m_barang,barang_kode,' . $barang->barang_id . ',barang_id',
            'barang_nama' => 'sometimes|string|max:100',
            'harga_beli' => 'sometimes|numeric',
            'harga_jual' => 'sometimes|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image
            if ($barang->image) {
                Storage::delete('public/barang/' . $barang->image);
            }

            $image = $request->file('image');
            $validatedData['image'] = $image->hashName();

            // Store the new image
            $image->store('public/barang');
        }

        $barang->update($validatedData);
        return response()->json($barang);
    }

    public function destroy($id)
    {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        try {
            // Delete image if exists
            if ($barang->image) {
                Storage::delete('public/barang/' . $barang->image);
            }

            $barang->delete();
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak dapat dihapus karena masih digunakan',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
