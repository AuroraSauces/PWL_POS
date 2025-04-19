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

        return view('penjualan.index', compact('penjualan', 'activeMenu'));
    }

    public function create()
    {
        // Mengambil data barang dan menambahkan informasi stok untuk masing-masing barang
        $barang = BarangModel::all()->map(function ($item) {
            // Menghitung total stok barang berdasarkan barang_id
            $item->stok = StokModel::where('barang_id', $item->barang_id)->sum('stok_jumlah');
            return $item;
        });

        return view('penjualan.create', compact('barang'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'penjualan_details' => 'required|array',
            'penjualan_details.*.barang_id' => 'required|exists:m_barang,barang_id',
            'pembeli' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()->all()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cek stok semua barang yang dipilih (jumlah selalu 1)
        foreach ($request->penjualan_details as $detail) {
            $barangId = $detail['barang_id'];

            // Cek total stok barang yang dipilih
            $totalStok = StokModel::where('barang_id', $barangId)->sum('stok_jumlah');

            if ($totalStok < 1) {
                $barang = BarangModel::find($barangId);
                $barangNama = $barang ? $barang->barang_nama : 'ID: ' . $barangId;

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok barang ' . $barangNama . ' tidak cukup.'
                    ], 400);
                }

                return redirect()->route('penjualan.create')->with('error', 'Stok barang ' . $barangNama . ' tidak cukup.');
            }
        }

        // Memulai transaksi
        DB::beginTransaction();

        try {
            // Simpan transaksi penjualan
            $penjualan = TransaksiPenjualanModel::create([
                'penjualan_tanggal' => now(),
                'pembeli' => $request->pembeli,
                'user_id' => Auth::id(), // Menambahkan user_id yang login
            ]);

            // Simpan detail penjualan dan kurangi stok
            foreach ($request->penjualan_details as $detail) {
                $barangId = $detail['barang_id'];

                // Simpan detail transaksi
                $penjualanDetail = TransaksiPenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barangId,
                    'jumlah' => 1, // Jumlah barang yang selalu 1
                ]);

                // Kurangi stok untuk barang yang dijual
                $penjualanDetail->reduceStok(); // Panggil method reduceStok untuk mengurangi stok
            }

            // Commit transaksi
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi penjualan berhasil',
                    'redirect' => route('penjualan.index')
                ]);
            }

            return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('penjualan.create')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
}
