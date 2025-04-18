<?php
namespace App\Http\Controllers;

use App\Models\TransaksiPenjualanModel;
use App\Models\TransaksiPenjualanDetailModel;
use App\Models\BarangModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiPenjualanController extends Controller
{
    public function index()
    {
        $activeMenu = 'penjualan';
        $penjualan = TransaksiPenjualanModel::with('detailPenjualan')->latest()->get();

        return view('penjualan.index', compact('penjualan', 'activeMenu'));
    }

    public function create()
    {
        $barang = BarangModel::all();
        return view('penjualan.create', compact('barang'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'penjualan_details' => 'required|array',
            'penjualan_details.*.barang_id' => 'required|exists:m_barang,barang_id',
            'penjualan_details.*.jumlah' => 'required|integer|min:1',
            'pembeli' => 'required|string|max:255', // Validasi pembeli
        ]);

        // CHECK STOCK FIRST before creating any records
        foreach ($request->penjualan_details as $detail) {
            // Cek apakah stok barang cukup
            $stokBarang = StokModel::where('barang_id', $detail['barang_id'])
                ->orderBy('stok_tanggal', 'asc')
                ->get();

            $jumlahYangDijual = $detail['jumlah'];
            $stokCukup = false;
            $totalStok = 0;

            foreach ($stokBarang as $stokItem) {
                $totalStok += $stokItem->stok_jumlah;
                if ($totalStok >= $jumlahYangDijual) {
                    $stokCukup = true;
                    break;
                }
            }

            if (!$stokCukup) {
                // Get the product name for a more informative error message
                $barang = BarangModel::find($detail['barang_id']);
                $barangNama = $barang ? $barang->barang_nama : 'ID: ' . $detail['barang_id'];

                return redirect()->route('penjualan.create')->with('error', 'Stok barang ' . $barangNama . ' tidak cukup.');
            }
        }

        // Use database transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Only create the transaction if ALL stock checks pass
            $penjualan = TransaksiPenjualanModel::create([
                'penjualan_tanggal' => now(),
                'pembeli' => $request->pembeli,
            ]);

            // Process details and reduce stock
            foreach ($request->penjualan_details as $detail) {
                $penjualanDetail = TransaksiPenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $detail['barang_id'],
                    'jumlah' => $detail['jumlah'],
                ]);

                $penjualanDetail->reduceStok();
            }

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('penjualan.create')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk pengecekan stok dengan AJAX
    public function cekStok(Request $request)
    {
        $stokBarang = StokModel::where('barang_id', $request->barang_id)
            ->orderBy('stok_tanggal', 'asc')
            ->get();

        $jumlahYangDijual = $request->jumlah;
        $stokCukup = false;
        $totalStok = 0;

        foreach ($stokBarang as $stokItem) {
            $totalStok += $stokItem->stok_jumlah;
            if ($totalStok >= $jumlahYangDijual) {
                $stokCukup = true;
                break;
            }
        }

        if ($stokCukup) {
            return response()->json(['success' => true]);
        } else {
            // Get the product name for a more informative error message
            $barang = BarangModel::find($request->barang_id);
            $barangNama = $barang ? $barang->barang_nama : 'ID: ' . $request->barang_id;

            return response()->json([
                'success' => false,
                'message' => 'Stok barang ' . $barangNama . ' tidak cukup.'
            ], 400); // Mengirimkan status 400 jika stok tidak cukup
        }
    }
}
