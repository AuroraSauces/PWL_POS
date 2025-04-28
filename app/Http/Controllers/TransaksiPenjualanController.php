<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPenjualanModel;
use App\Models\TransaksiPenjualanDetailModel;
use App\Models\BarangModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Import Auth untuk mendapatkan pengguna yang sedang login

class TransaksiPenjualanController extends Controller
{
    public function index()
    {
        $activeMenu = 'penjualan';
        // Load penjualan dengan relasi detail penjualan dan user
        $penjualan = TransaksiPenjualanModel::with('detailPenjualan', 'user')->latest()->get();

        return view('Penjualan.index', compact('penjualan', 'activeMenu'));
    }

    // Method untuk pengecekan stok dengan AJAX
    public function cekStok(Request $request)
    {
        $barangIds = $request->barang_ids ?? [];
        $stokInfo = [];

        foreach ($barangIds as $barangId) {
            $totalStok = StokModel::where('barang_id', $barangId)->sum('stok_jumlah');

            if ($totalStok < 1) {
                $stokInfo[$barangId] = 'Stok tidak cukup';
            } else {
                $stokInfo[$barangId] = 'Stok cukup';
            }
        }

        return response()->json([
            'success' => true,
            'stokInfo' => $stokInfo
        ]);
    }

    public function create_ajax()
{
    $barang = BarangModel::all()->map(function ($item) {
        $totalMasuk = StokModel::where('barang_id', $item->barang_id)->sum('stok_jumlah');
        $totalKeluar = $item->penjualanDetail()->sum('jumlah');
        $item->stok = $totalMasuk - $totalKeluar;
        return $item;
    })->filter(function ($item) {
        return $item->stok > 0;
    })->values();

    return view('Penjualan.create', compact('barang'));
}


    public function store_ajax(Request $request)
{
    $validator = Validator::make($request->all(), [
        'pembeli' => 'required|string|max:255',
        'barang_ids' => 'required|array',
        'barang_ids.*' => 'exists:m_barang,barang_id',
        'jumlahs' => 'required|array',
        'jumlahs.*' => 'numeric|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();
    try {
        $penjualan = TransaksiPenjualanModel::create([
            'penjualan_tanggal' => now(),
            'pembeli' => $request->pembeli,
            'user_id' => Auth::id(),
        ]);

        foreach ($request->barang_ids as $i => $barangId) {
            $jumlah = $request->jumlahs[$i] ?? 1;

            // Hitung stok tersedia = stok masuk - jumlah yang sudah terjual
            $totalMasuk = StokModel::where('barang_id', $barangId)->sum('stok_jumlah');
            $totalKeluar = TransaksiPenjualanDetailModel::where('barang_id', $barangId)->sum('jumlah');
            $stokTersedia = $totalMasuk - $totalKeluar;

            // Validasi apakah cukup
            if ($jumlah > $stokTersedia) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup untuk barang ID: ' . $barangId
                ], 422);
            }

            // Simpan detail penjualan
            TransaksiPenjualanDetailModel::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id' => $barangId,
                'jumlah' => $jumlah,
            ]);
        }



        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi penjualan berhasil ditambahkan'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
}
