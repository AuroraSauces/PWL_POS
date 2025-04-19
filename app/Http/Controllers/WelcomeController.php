<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\TransaksiPenjualanDetailModel;
use App\Models\TransaksiPenjualanModel; // Import model TransaksiPenjualan

class WelcomeController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';

        // Hitung total stok yang ready
        $totalStok = StokModel::sum('stok_jumlah');

        // Hitung total barang yang terjual
        $totalTerjual = TransaksiPenjualanDetailModel::sum('jumlah');

        // Hitung total nominal penjualan
        $totalNominalPenjualan = TransaksiPenjualanDetailModel::with('barang')->get()->sum(function ($detail) {
            return $detail->jumlah * ($detail->barang->harga_jual ?? 0);
        });

        // Hitung total transaksi (jumlah transaksi penjualan)
        $totalTransaksi = TransaksiPenjualanModel::count();

        return view("welcome", [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'totalStok' => $totalStok,
            'totalTerjual' => $totalTerjual,
            'totalNominalPenjualan' => $totalNominalPenjualan,
            'totalTransaksi' => $totalTransaksi, // Kirimkan data total transaksi ke view
        ]);
    }
}
