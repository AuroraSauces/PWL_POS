<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiPenjualanModel;
use App\Models\TransaksiPenjualanDetailModel;
use App\Models\StokModel;
use App\Models\BarangModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = TransaksiPenjualanModel::with(['detailPenjualan.barang', 'user'])->get();
        return response()->json($transaksi);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'pembeli' => 'required|string|max:255',
            'user_id' => 'required|exists:m_user,user_id',
            'items' => 'required|array',
            'items.*.barang_id' => 'required|exists:m_barang,barang_id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Membuat transaksi penjualan
            $transaksi = TransaksiPenjualanModel::create([
                'penjualan_tanggal' => now(),
                'pembeli' => $validatedData['pembeli'],
                'user_id' => $validatedData['user_id'],
            ]);

            // Menambahkan detail transaksi
            foreach ($validatedData['items'] as $item) {
                $barangId = $item['barang_id'];
                $jumlah = $item['jumlah'];

                // Cek stok tersedia
                $totalMasuk = StokModel::where('barang_id', $barangId)->sum('stok_jumlah');
                $totalKeluar = TransaksiPenjualanDetailModel::where('barang_id', $barangId)->sum('jumlah');
                $stokTersedia = $totalMasuk - $totalKeluar;

                if ($jumlah > $stokTersedia) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak cukup untuk barang ID: ' . $barangId
                    ], 422);
                }

                // Simpan detail transaksi
                $detail = TransaksiPenjualanDetailModel::create([
                    'penjualan_id' => $transaksi->penjualan_id,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                ]);

                // HAPUS ATAU KOMENTAR BARIS INI UNTUK MENGHINDARI PENGURANGAN STOK GANDA
                // $detail->reduceStok();

                // Metode reduceStok() sudah otomatis dipanggil melalui event created model
                // jika kita panggil lagi di sini, maka stok akan berkurang dua kali
            }

            DB::commit();

            $transaksi->load(['detailPenjualan.barang', 'user']);
            return response()->json($transaksi, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $transaksi = TransaksiPenjualanModel::with(['detailPenjualan.barang', 'user'])->find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json($transaksi);
    }

    public function update(Request $request, $id)
    {
        $transaksi = TransaksiPenjualanModel::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $validatedData = $request->validate([
            'pembeli' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:m_user,user_id',
            'items' => 'sometimes|array',
            'items.*.detail_id' => 'required|exists:t_penjualan_detail,detail_id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Update data transaksi
            if (isset($validatedData['pembeli'])) {
                $transaksi->pembeli = $validatedData['pembeli'];
            }
            if (isset($validatedData['user_id'])) {
                $transaksi->user_id = $validatedData['user_id'];
            }
            $transaksi->save();

            // Update jumlah item jika ada
            if (isset($validatedData['items'])) {
                foreach ($validatedData['items'] as $item) {
                    $detail = TransaksiPenjualanDetailModel::where('detail_id', $item['detail_id'])
                        ->where('penjualan_id', $id)
                        ->first();

                    if ($detail) {
                        $jumlahBaru = $item['jumlah'];
                        $jumlahLama = $detail->jumlah;

                        // Jika jumlah berubah, kelola perubahan stok di model
                        if ($jumlahBaru != $jumlahLama) {
                            if ($jumlahBaru > $jumlahLama) {
                                $tambahan = $jumlahBaru - $jumlahLama;

                                // Cek stok tersedia
                                $totalMasuk = StokModel::where('barang_id', $detail->barang_id)->sum('stok_jumlah');
                                $totalKeluar = TransaksiPenjualanDetailModel::where('barang_id', $detail->barang_id)
                                    ->where('detail_id', '!=', $detail->detail_id)
                                    ->sum('jumlah');
                                $stokTersedia = $totalMasuk - $totalKeluar - $jumlahLama;

                                if ($tambahan > $stokTersedia) {
                                    DB::rollBack();
                                    return response()->json([
                                        'success' => false,
                                        'message' => 'Stok tidak cukup untuk menambah jumlah barang'
                                    ], 422);
                                }

                                // Tambahkan entri stok untuk penambahan jumlah (model akan menangani)
                                StokModel::create([
                                    'barang_id' => $detail->barang_id,
                                    'stok_tanggal' => now(),
                                    'stok_jumlah' => -$tambahan,
                                    'user_id' => $transaksi->user_id,
                                ]);
                            } elseif ($jumlahBaru < $jumlahLama) {
                                $selisih = $jumlahLama - $jumlahBaru;

                                // Tambahkan entri stok untuk pengurangan jumlah (model akan menangani)
                                StokModel::create([
                                    'barang_id' => $detail->barang_id,
                                    'stok_tanggal' => now(),
                                    'stok_jumlah' => $selisih,
                                    'user_id' => $transaksi->user_id,
                                ]);
                            }
                        }

                        // Update jumlah detail transaksi
                        $detail->jumlah = $jumlahBaru;
                        $detail->save();
                    }
                }
            }

            DB::commit();
            $transaksi->load(['detailPenjualan.barang', 'user']);
            return response()->json($transaksi);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $transaksi = TransaksiPenjualanModel::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Load detail transaksi dengan model untuk memanfaatkan events
            $detailTransaksi = TransaksiPenjualanDetailModel::where('penjualan_id', $id)->get();

            // Perulangan menggunakan model untuk memicu events
            foreach ($detailTransaksi as $detail) {
                // Sebelum menghapus detail, tambahkan flag untuk menandai operasi pengembalian stok
                $detail->is_returning_stock = true;

                // Jangan hapus detail dulu, karena kita perlu informasinya untuk mengembalikan stok
                // Stok akan dikembalikan melalui event deleting/deleted pada model
                $detail->delete();
            }

            // Hapus transaksi setelah semua detail dihapus
            $transaksi->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus dan stok dikembalikan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak dapat dihapus',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
