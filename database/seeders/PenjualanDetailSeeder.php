<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenjualanDetailSeeder extends Seeder
{
    public function run()
    {
        $penjualan = DB::table('t_penjualan')->pluck('penjualan_id')->toArray();
        $barang = DB::table('m_barang')->pluck('barang_id', 'harga_jual')->toArray();

        if (empty($penjualan) || empty($barang)) {
            return;
        }

        $totalPenjualan = [];

        foreach ($penjualan as $penjualan_id) {
            $total = 0;

            for ($i = 0; $i < 3; $i++) {
                $barang_id = array_rand($barang);
                $harga = $barang[$barang_id];
                $jumlah = rand(1, 5);
                $subtotal = $harga * $jumlah;
                $total += $subtotal;

                DB::table('t_penjualan_detail')->insert([
                    'penjualan_id' => $penjualan_id,
                    'barang_id' => $barang_id,
                    'detail_jumlah' => $jumlah,
                    'detail_subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $totalPenjualan[$penjualan_id] = $total;
        }

        foreach ($totalPenjualan as $id => $total) {
            DB::table('t_penjualan')->where('penjualan_id', $id)->update(['penjualan_total' => $total]);
        }
    }
}
