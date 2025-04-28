<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\TransaksiPenjualanDetailModel;
use App\Models\TransaksiPenjualanModel;
use App\Models\BarangModel;

class WelcomeController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';

        // Hitung total stok yang ready (stok masuk - barang terjual)
        $totalMasuk = StokModel::sum('stok_jumlah');
        $totalKeluar = TransaksiPenjualanDetailModel::sum('jumlah');
        $totalStok = $totalMasuk - $totalKeluar;

        // Hitung total barang yang terjual
        $totalTerjual = TransaksiPenjualanDetailModel::sum('jumlah');

        // Hitung total nominal penjualan
        $totalNominalPenjualan = TransaksiPenjualanDetailModel::with('barang')->get()->sum(function ($detail) {
            return $detail->jumlah * ($detail->barang->harga_jual ?? 0);
        });

        // Hitung total transaksi (jumlah transaksi penjualan)
        $totalTransaksi = TransaksiPenjualanModel::count();

        // Ambil daftar barang yang stok-nya masih tersedia
        $barangReady = BarangModel::with(['stok', 'penjualanDetail'])->get()->map(function ($barang) {
            // Jumlah stok yang masuk
            $stokMasuk = $barang->stok->sum('stok_jumlah');
            // Jumlah barang yang terjual
            $stokKeluar = $barang->penjualanDetail->sum('jumlah');
            // Menghitung stok ready
            $barang->stok_ready = $stokMasuk - $stokKeluar;
            return $barang;
        })->filter(function ($barang) {
            return $barang->stok_ready > 0;
        });

        return view("welcome", [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'totalStok' => $totalStok,
            'totalTerjual' => $totalTerjual,
            'totalNominalPenjualan' => $totalNominalPenjualan,
            'totalTransaksi' => $totalTransaksi,
            'barangReady' => $barangReady, // Kirimkan data barang yang ready ke view
        ]);
    }
}
